<?php
/*
	Plugin Name: Dominant Color
	Description: Add an attachment meta option to provide the hex of the most dominant color of an image.
	Version: 2.0
	Author: Liam Gladdy
	Author URI: https://gladdy.uk
*/

require('vendor/autoload.php');
use ColorThief\ColorThief;

//gets all hex values from svg
function get_svg_colors($svg_url){
	$file = file_get_contents($svg_url);
	preg_match_all("/#[0-9a-f]{6}/i", $file, $colors_6char);
	$file_edit = str_replace($colors_6char[0],'',$file);
	preg_match_all("/#[0-9a-f]{3}/i", $file_edit, $colors_3char);
	$colors = array_merge($colors_6char[0], $colors_3char[0]);
	$colors = array_unique($colors);
	return $colors;
};

// Add our css to the admin
add_action('admin_enqueue_scripts', 'dominance_scripts');

function dominance_scripts() {
	wp_register_style('dominanceColorCSS', plugins_url('assets/dominant_colour_admin.css', __FILE__));
	wp_enqueue_style('dominanceColorCSS');
	wp_register_script('dominanceColorJS', plugins_url('assets/dominant_colour_admin.js', __FILE__), array(), '2.0', true);
	wp_enqueue_script('dominanceColorJS');
}

//Color dominance detection and saving.
add_action('add_attachment', 'update_attachment_color_dominance', 10, 1);


function update_attachment_color_dominance($attachment_id) {

	if (!wp_attachment_is_image($attachment_id)) return;

	$upload_dir = wp_upload_dir();
	$image = $upload_dir['basedir'].'/'.get_post_meta($attachment_id, '_wp_attached_file', true);
	$ext = pathinfo($image, PATHINFO_EXTENSION);
	$post = get_post($attachment_id);
	if (!$image) {
		return;
	} elseif( $ext === 'svg' ) {
		$hex_palette = get_svg_colors($post->guid);
		$hex = $hex_palette[0];
		$dominantColor = hex2rgb($hex);
		$palette = [];
		foreach ($hex_palette as $hex) {
			$palette[] = hex2rgb($hex);
		}
	} else {
		try {
			$dominantColor = ColorThief::getColor($image);
		} catch(Exception $e) {
			//Probably should do something here. I think realistically this just means the image doesn't exist, or isn't an image. So maybe return is fine anyway?
			return;
		}
		$palette = ColorThief::getPalette($image, 8);
		
		$hex = rgb2hex($dominantColor);
		$hex_palette = [];
		foreach($palette as $rgb) {
			$hex_palette[] = rgb2hex($rgb);
		}
	}
	$palette = array_unique($palette);
	$hex_palette = array_unique($hex_palette);
	update_post_meta($attachment_id, 'dominant_color_hex', $hex);
	update_post_meta($attachment_id, 'dominant_color_rgb', $dominantColor);
	update_post_meta($attachment_id, 'color_palette_rgb', $palette);
	update_post_meta($attachment_id, 'color_palette_hex', $hex_palette);
}


// Admin field for overriding.
add_filter("attachment_fields_to_edit", "add_colour_dominance_fields", 10, 2);
function add_colour_dominance_fields($fields, $post) {
	
	//Get the dominant colour pallete, or rebuild it if it doesn't exist.
	$palette = get_color_data($post->ID, 'color_palette_hex', true);
	
	if ($palette === false) {
		$html = 'No Color Dominance Available.<br /><a href="#" class="trigger-rebuild" data-dominance-rebuild="'.$post->ID.'">Calculate Now?</a>';
	} else {
		$dominantColor = get_color_data($post->ID, 'dominant_color_hex', true, false);
		
		$palette = array_merge((array) $dominantColor, $palette);
		
		$current_dominance = get_color_data($post->ID, 'dominant_color_hex', true);
		
		$htmls = array();
		foreach($palette as $pal) {
			$html = '<div class="dominant-colour-square';
			if ($pal == $current_dominance) $html .= ' selected';
			$html .= '" data-col="'.$pal.'" style="background-color: '.$pal.'"></div>';
			
			$htmls[] = $html;
		}
		$html = '<div class="dominantColourHolder">'.implode($htmls).'</div>';
	}
	$html .= '<script>attachDominantColor();</script>';
	
	$fields['dominant-override'] = array(
    'value' => get_post_meta($post->ID, "dominant_override", true),
    'class' => 'dominant-override',
    'input' => 'hidden'
  );
	
	$fields['dominant-color'] = array(
    'value' => '',
    'input' => 'html',
    'html'  => $html,
    'label' => __( 'Dominant Color' )
  );
	return $fields;
}

//Save dominant-override.
add_filter('attachment_fields_to_save','save_dominant_override', 10, 2);
function save_dominant_override($post, $attachment) {
  if (isset($attachment['dominant-override'])) {
	  if ($attachment['dominant-override'] == "trigger-rebuild") {
		  update_attachment_color_dominance($post['ID']);
	  } else {
	    update_post_meta($post['ID'], 'dominant_override', $attachment['dominant-override']);
    }
  }
  return $post;
}

// Even we use this now. Probably needs a major refactor in a future version.
function get_color_data($attachment_id, $thing_to_get, $no_rebuild = false, $allow_override = true) {
	
	//If thing_to_get is dominant_color_hex or dominant_color_rgb, we should check if an override is set first.
	if ($allow_override && ($thing_to_get == "dominant_color_hex" || $thing_to_get == "dominant_color_rgb")) {
		$data = get_post_meta($attachment_id, 'dominant_override', true);
		$data = trim($data);
		if ($data && !empty($data)) {
			if ($thing_to_get == "dominant_color_hex") {
				return $data;
			} else {
				return hex2rgb($data);
			}
		}
	}
	
	$data = get_post_meta($attachment_id, $thing_to_get, true);
	if (!$data) {
		if ($no_rebuild) return false;
		update_attachment_color_dominance($attachment_id);
		return get_post_meta($attachment_id, $thing_to_get, true);
	} else {
		return $data;
	}
}

/* Helper functions */
function rgb2hex($rgb) {
   $hex = "#";
   $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

   return $hex; // returns the hex value including the number sign (#)
}

function hex2rgb($color){
  $color = str_replace('#', '', $color);
  if (strlen($color) != 6){ return array(0,0,0); }
  $rgb = array();
  for ($x=0;$x<3;$x++){
    $rgb[$x] = hexdec(substr($color,(2*$x),2));
  }
  return $rgb;
}