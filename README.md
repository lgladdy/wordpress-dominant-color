# Dominant Color
A WordPress plugin to automatically save the dominant color and a color palette for an attachment image into post_meta.

Uses [ksubileau/color-thief-php](https://github.com/ksubileau/color-thief-php) as it's core.

### Usage

The dominant color and color palette is automatically saved on upload and edit to post meta.

You need to then use [get_post_meta](https://developer.wordpress.org/reference/functions/get_post_meta/) to retrieve the "dominant_color_hex" or "dominant_color_rgb" meta key. hex returns a string, including the #, rgb returns an array with key 0 as red, 1 as green, and 2 as blue.

Alternatively, you can use the meta keys "color_palette_rgb" and "color_palette_hex" to get an array of 8 colors that feature prominently in the image.

### Todo

Provide a way automatically update previously uploaded images with their color data. Until then, you can use a helper function, get_color_data (instead of get_post_meta) which will automatically build the color data if need be at runtime.

### Apologies

My fellow brits: This was originally called "Dominant Colour", but given that the world we code in uses color, it makes more sense.