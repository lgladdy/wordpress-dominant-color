=== Dominant Color ===
Contributors: lgladdy
Tags: color, colour, image, automatic
Requires at least: 4.0
Tested up to: 4.3
Stable tag: trunk
License: Apache 2.0
License URI: http://www.apache.org/licenses/LICENSE-2.0

A WordPress plugin to automatically save the dominant color and a color palette for an attachment image into post_meta.

== Description ==
A WordPress plugin to automatically save the dominant color and a color palette for an attachment image into post_meta.

== Installation ==
The dominant color and color palette is automatically saved on upload and edit to post meta.

You need to then use get_post_meta to retrieve the "dominant_color_hex" or "dominant_color_rgb" meta key. hex returns a string, including the #, rgb returns an array with key 0 as red, 1 as green, and 2 as blue.

Alternatively, you can use the meta keys "color_palette_rgb" and "color_palette_hex" to get an array of 8 colors that feature prominently in the image.

== Changelog ==
1.0 Initial Build