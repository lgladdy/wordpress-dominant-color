<?php
/*
Plugin Name: Dominant Color
Description: Add an attachment meta option to provide the hex of the most dominant color of an image.
Version: 2.2.0
Text Domain: dominant-color
Author: Liam Gladdy
Author URI: https://gladdy.uk
*/

require 'vendor/autoload.php';
use ColorThief\ColorThief;

// Add our css to the admin
add_action( 'admin_enqueue_scripts', 'dominance_scripts' );

function dominance_scripts() {
	wp_register_style( 'dominant-color-css', plugins_url( 'assets/dominant_colour_admin.css', __FILE__ ) );
	wp_enqueue_style( 'dominant-color-css' );
	wp_register_script( 'dominant-color-js', plugins_url( 'assets/dominant_colour_admin.js', __FILE__ ), array(), '2.0' );
	wp_enqueue_script( 'dominant-color-js' );
}

// Color dominance detection and saving.
add_action( 'add_attachment', 'update_attachment_color_dominance', 10, 1 );


function update_attachment_color_dominance( $attachment_id ) {

	if ( ! wp_attachment_is_image( $attachment_id ) ) {
		return;
	}

	$upload_dir = wp_upload_dir();
	$image      = $upload_dir['basedir'] . '/' . get_post_meta( $attachment_id, '_wp_attached_file', true );

	if ( ! $image ) {
		return;
	}
	try {
		$dominant_color = ColorThief::getColor( $image );
		$palette        = ColorThief::getPalette( $image, 8 );
	} catch ( Exception $e ) {
		// Probably should do something here. I think realistically this just means the image doesn't exist, or isn't an image. So maybe return is fine anyway?
		return;
	}
	$hex = rgb2hex( $dominant_color );

	update_post_meta( $attachment_id, 'dominant_color_hex', $hex );
	update_post_meta( $attachment_id, 'dominant_color_rgb', $dominant_color );

	update_post_meta( $attachment_id, 'color_palette_rgb', $palette );

	$hex_palette = array();
	foreach ( $palette as $rgb ) {
		$hex_palette[] = rgb2hex( $rgb );
	}
	update_post_meta( $attachment_id, 'color_palette_hex', $hex_palette );
}


// Admin field for overriding.
add_filter( 'attachment_fields_to_edit', 'add_colour_dominance_fields', 10, 2 );
function add_colour_dominance_fields( $fields, $post ) {

	// Get the dominant colour pallete, or rebuild it if it doesn't exist.
	$palette = get_color_data( $post->ID, 'color_palette_hex', true );

	if ( $palette === false ) {
		$html  = __( 'No Color Dominance Available.', 'dominant-color' );
		$html .= '<br /><a href="#" class="trigger-rebuild" data-dominance-rebuild="' . $post->ID . '">';
		$html .= __( 'Calculate Now?', 'dominant-color' );
		$html .= '</a>';
	} else {
		$dominant_color = get_color_data( $post->ID, 'dominant_color_hex', true, false );

		$palette = array_merge( (array) $dominant_color, $palette );

		$currently_selected = get_color_data( $post->ID, 'dominant_color_hex', true );

		$htmls = array();
		foreach ( $palette as $pal ) {
			$html = '<div class="dominant-colour-square';
			if ( $pal == $currently_selected ) {
				$html .= ' selected';
			}
			$html .= '" data-col="' . $pal . '" style="background-color: ' . $pal . '"></div>';

			$htmls[] = $html;
		}
		$html = '<div class="dominantColourHolder">' . implode( $htmls ) . '</div>';
	}
	$html .= '<script>attachDominantColor();</script>';

	$fields['dominant-override'] = array(
		'value' => get_post_meta( $post->ID, 'dominant_override', true ),
		'class' => 'dominant-override',
		'input' => 'hidden',
	);

	$fields['dominant-color'] = array(
		'value' => '',
		'input' => 'html',
		'html'  => $html,
		'label' => __( 'Dominant Color', 'dominant-color' ),
	);
	return $fields;
}

// Save dominant-override.
add_filter( 'attachment_fields_to_save', 'save_dominant_override', 10, 2 );
function save_dominant_override( $post, $attachment ) {
	if ( isset( $attachment['dominant-override'] ) ) {
		if ( $attachment['dominant-override'] == 'trigger-rebuild' ) {
			update_attachment_color_dominance( $post['ID'] );
		} else {
			update_post_meta( $post['ID'], 'dominant_override', $attachment['dominant-override'] );
		}
	}
	return $post;
}

// Even we use this now. Probably needs a major refactor in a future version.
function get_color_data( $attachment_id, $thing_to_get, $no_rebuild = false, $allow_override = true ) {

	// If thing_to_get is dominant_color_hex or dominant_color_rgb, we should check if an override is set first.
	if ( $allow_override && ( $thing_to_get == 'dominant_color_hex' || $thing_to_get == 'dominant_color_rgb' ) ) {
		$data = get_post_meta( $attachment_id, 'dominant_override', true );
		$data = trim( $data );
		if ( $data && ! empty( $data ) ) {
			if ( $thing_to_get == 'dominant_color_hex' ) {
				return $data;
			} else {
				return hex2rgb( $data );
			}
		}
	}

	$data = get_post_meta( $attachment_id, $thing_to_get, true );
	if ( ! $data ) {
		if ( $no_rebuild ) {
			return false;
		}
		update_attachment_color_dominance( $attachment_id );
		return get_post_meta( $attachment_id, $thing_to_get, true );
	} else {
		return $data;
	}
}

/* Helper functions */
function rgb2hex( $rgb ) {
	$hex  = '#';
	$hex .= str_pad( dechex( $rgb[0] ), 2, '0', STR_PAD_LEFT );
	$hex .= str_pad( dechex( $rgb[1] ), 2, '0', STR_PAD_LEFT );
	$hex .= str_pad( dechex( $rgb[2] ), 2, '0', STR_PAD_LEFT );

	return $hex; // returns the hex value including the number sign (#)
}

function hex2rgb( $color ) {
	$color = str_replace( '#', '', $color );
	if ( strlen( $color ) != 6 ) {
		return array( 0, 0, 0 ); }
	$rgb = array();
	for ( $x = 0;$x < 3;$x++ ) {
		$rgb[ $x ] = hexdec( substr( $color, ( 2 * $x ), 2 ) );
	}
	return $rgb;
}

add_action( 'plugins_loaded', 'dominant_color_load_textdomain' );
function dominant_color_load_textdomain() {
	load_plugin_textdomain( 'dominant-color', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}
