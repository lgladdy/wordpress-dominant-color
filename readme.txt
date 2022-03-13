=== Dominant Color ===
Contributors: lgladdy
Tags: color, colour, image, dominant, dominance, automatic
Requires at least: 5.4
Requires PHP: 7.2
Tested up to: 5.9.1
Stable tag: 2.1.0
License: Apache 2.0
Text Domain: dominant-color
License URI: http://www.apache.org/licenses/LICENSE-2.0

A WordPress plugin to automatically save the dominant color and a color palette for an attachment image into post_meta.

Requires:
PHP >= 7.2
Fileinfo extension
One or more PHP extensions for image processing:
- GD >= 2.0
- Imagick >= 2.0 (but >= 3.0 for CMYK images)
- Gmagick >= 1.0

== Description ==
A WordPress plugin to automatically save the dominant color and a color palette for an attachment image into post_meta.

== Installation ==
The dominant color and color palette is automatically saved on upload and edit to post meta.

You need to then use get_post_meta to retrieve the "dominant_color_hex" or "dominant_color_rgb" meta key. hex returns a string, including the #, rgb returns an array with key 0 as red, 1 as green, and 2 as blue.

Alternatively, you can use the meta keys "color_palette_rgb" and "color_palette_hex" to get an array of 8 colors that feature prominently in the image.

== Screenshots ==

1. Shows the plugin in use on hellobrstl.com
2. Shows the plugin in use on hellobrstl.com
3. Shows the plugin in use on hellobrstl.com

== Changelog ==

2.2.0
---

Modernise javascript for deprecated jQuery handlers
Update to latest version of [PHP Color Thief](https://github.com/ksubileau/color-thief-php)

2.1.0
---

Support translated languages (Submit your translation at translate.wordpress.org!)

2.0.1
---

Fix a bug where loading our JS in the footer would cause a javascript error.

2.0
---

Add a colour picker to the attachment edit screen to override the dominant color with a custom pick.
The WordPress media gallery will now let you pick a dominant color override which will be returned in place of dominant_color_hex/rgb when set.
It'll also let you generate a palette for legacy images uploaded before you installed the plugin.

1.0
---

Initial Build