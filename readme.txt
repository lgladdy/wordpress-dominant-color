=== Dominant Color ===
Contributors: lgladdy
Tags: color, colour, image, dominant, dominance, automatic
Requires at least: 4.0
Tested up to: 4.3
Stable tag: 2.0
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

2.0
---

Add a colour picker to the attachment edit screen to override the dominant color with a custom pick.
The WordPress media gallery will now let you pick a dominant color override which will be returned in place of dominant_color_hex/rgb when set.
It'll also let you generate a palette for legacy images uploaded before you installed the plugin.

1.0
---

Initial Build