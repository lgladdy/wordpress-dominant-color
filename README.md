# Dominant Colour
A WordPress plugin to automatically save the dominant colour for an attachment image into post_meta.

Uses [ksubileau/color-thief-php](https://github.com/ksubileau/color-thief-php) as it's core.

### Usage

The dominant colour is automatically saved on upload and edit to post meta.
You need to then use [get_post_meta](https://developer.wordpress.org/reference/functions/get_post_meta/) to retrieve the "dominant_colour" meta key.

