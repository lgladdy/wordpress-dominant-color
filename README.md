# Dominant Colour
A WordPress plugin to automatically save the dominant colour and a colour palette for an attachment image into post_meta.

Uses [ksubileau/color-thief-php](https://github.com/ksubileau/color-thief-php) as it's core.

### Usage

The dominant colour and colour palette is automatically saved on upload and edit to post meta.

You need to then use [get_post_meta](https://developer.wordpress.org/reference/functions/get_post_meta/) to retrieve the "dominant_colour_hex" or "dominant_colour_rgb" meta key. hex returns a string, including the #, rgb returns an array with key 0 as red, 1 as green, and 2 as blue.

Alternatively, you can use the meta keys "colour_palette_rgb" and "colour_palette_hex" to get an array of 8 colours that feature prominently in the image.