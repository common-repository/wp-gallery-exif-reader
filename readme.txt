=== Plugin Name ===
Contributors: eclipticcreations
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4274157
Tags: gallery, photo, images, exif
Requires at least: 2.7
Tested up to: 2.8
Stable tag: 0.9.3

Extract EXIF information when using the gallery shortcode.

== Description ==

Extract EXIF information when using the gallery shortcode. This is specifically for those without
access to the `php_exif` extension on their servers. Style by editing gallery-exif-reader.css.
Debug by uncommenting `add_action('loop_start', 'ec_exif_reader_debug', 1);` in
gallery-exif-reader.php. 

== Installation ==

1. Upload folder `wp-gallery-exif-reader` to the `/wp-content/plugins/` directory or
install the zip file through the dashboard
2. Activate the plugin through the 'Plugins' menu in WordPress. Works out of the box.

== Frequently Asked Questions ==

= The data isn't displaying. I use an attachment.php or image.php file. =

Just add `<?php ec_get_image_exif(); ?>` after the image dispaying code in your file.

= Can I display exif data even if I'm not using the gallery shortcode? =

Yes. The function `ec_get_item_exif` can take the url of the full-size image or the attachment ID as an argument.

= Can I display data other than the default? =

Uncomment `//add_action('loop_start', 'ec_exif_reader_debug', 1)`

The available data will display in an array at the top of the page. If it doesn't, add this to the top of the page:

`<?php ec_exif_reader_debug(); ?>`

Choose your data then add it to the list in the `gallery-exif-reader.php` file, in this form:
 
1. `$output .= "<li><span class=\"exifTitle\">Aperture</span> " . $exif_array['SubIFD']['ApertureValue'] . "</li>\n";`
2. `echo "<li><span class=\"exifTitle\">Aperture</span> " . $exif_array['SubIFD']['ApertureValue'] . "</li>\n";` 

(1) is for `ec_get_attachment_exif` (2) is for `ec_get_image_exif` and `ec_get_item_exif`

= Some of my images have no EXIF data. Will the plugin display an empty table? =

No. The plugin checks for a value in the `['IFD0']['Model']` field of the array.
If there's no value, the table doesn't display.

= Can I change the way the date displays? =

Yes. Change `$exif_date` using formatting from [php.net/date](http://php.net/date/ "PHP Date Format").


== Screenshots ==

1. Data table, an unordered list.

2. The data is displayed below the image by default.

== Changelog ==

** 0.9.3 (6/8/09) **

Added new function (ec_get_item_exif) which takes as an argument either the url of the full-sized image (including "http://") or the attachment ID.

`<?php ec_get_item_exif($att_image_url_or_ID); ?>`

** 0.9.2 (4/7/09) ** 

By default the date will display in the format set in Settings > General.

** 0.9.1b **

Not all files were included in subversion-generated zip archive.


** 0.9.1a (3/29/09) **

Repaired incorrect path to stylesheet.


** 0.9.1 (3/29/09) **

After editing Picasa strips `ApertureValue` from the data in favor of `FNumber`. Now checks both.
Formatted `FNumber` to round to 1 decimal.
Shooting date is now determined by `DateTimeOriginal`, rather than `DateTime`.

== Demo ==

More info at [POS51](http://pos51.org/wordpress-plugin-wp-gallery-exif-reader/ "WP Gallery Reader")

See examples at [POS51](http://pos51.org/category/technology/photos/gallery/ "WP Gallery EXIF Reader Demo")