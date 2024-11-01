<?php
/*
Plugin Name: WP Gallery EXIF Reader
Plugin URI: http://www.pos51.org/wordpress-plugin-wp-gallery-exif-reader/
Description: Extract EXIF information when using the gallery shortcode. This is specifically for those without access to the <code>php_exif</code> extension on their servers. Style by editing gallery-exif-reader.css. Debug by uncommenting <code>add_filter('the_content', 'ec_exif_reader_debug', 1);</code> in gallery-exif-reader.php. 
Author: Charles Jones
Version: 0.9.3
Author URI: http://www.pos51.org/
*/ 

/*
** 0.9.3 (6/8/09) **

Added new function (ec_get_item_exif) which takes either the url of the full-sized image (including "http://") or the attachment ID.

** 0.9.2 (4/7/09) ** 

By default the date will display in the format set in Settings > General.

** 0.9.1b (3/29/09) **

Repaired incorrect path to stylesheet.

** 0.9.1 (3/29/09) **

After editing Picasa strips `ApertureValue` from the data in favor of `FNumber`. WPG-ER now checks both.
Formatted `FNumber` to round to 1 decimal.
Shooting date is now determined by `DateTimeOriginal`, rather than `DateTime`.
*/

// Exifer EXIF library, from JakeO.com and ZenPhoto.org
include('exif.php');

// Thanks rjackson from dslreports.com ( code found at http://www.dslreports.com/forum/remark,15677773 )
function convertExifToTimestamp($exifString, $dateFormat) {
  $exifPieces = explode(":", $exifString);
  return date($dateFormat, strtotime($exifPieces[0] . "-" . $exifPieces[1] .
        "-" . $exifPieces[2] . ":" . $exifPieces[3] . ":" . $exifPieces[4]));
}

function ec_get_attachment_exif($content) {

	global $wp_query;
		if (!$wp_query->is_single) return $content;
	
		global $post;
		$post = get_post($post);
		
		if (is_attachment()) {
		
			if (wp_attachment_is_image($post->id)) {
		
				$att_image_full = wp_get_attachment_image_src( $post->id, "full" );
				$path_substr_start = strlen(get_bloginfo('wpurl'));
				$site_abs_path = ABSPATH;
				$att_image_path = $site_abs_path . substr($att_image_full[0], $path_substr_start); 
				$exif_array = read_exif_data_raw($att_image_path,0);
				
				//Set your date format. Common uses: m-d-Y (03-26-2009), F j, Y (March 26, 2009), d.m.Y (26.03.2009); http://us.php.net/date
				$exif_date = null;
				
				if ($exif_date == null) :
					$exif_date = get_option('date_format');
				endif;				
				
				//There are two aperture fields in the SubIFD array, FNumber and AptertureValue. Some programs strip one during edit or resize
				if ($exif_array['SubIFD']['ApertureValue'] != null) {
                	$apertureVal = $exif_array['SubIFD']['ApertureValue'];
                	} else {
                	$apertureVal = $exif_array['SubIFD']['FNumber'];
                }

				$output = "<ul id=\"exifData\">\n";
				$output .= "<li><span class=\"exifTitle\">Original Size</span> <a href=\"" . $att_image_full[0] . "\">" . $exif_array['SubIFD']['ExifImageWidth'] . " x " . $exif_array['SubIFD']['ExifImageHeight'] . " pixels</a></li>\n";
				$output .= "<li><span class=\"exifTitle\">Date Taken</span> " . convertExifToTimestamp($exif_array['SubIFD']['DateTimeOriginal'], $exif_date) . "</li>\n";
				$output .= "<li><span class=\"exifTitle\">Aperture</span> " . $apertureVal . "</li>\n";
				$output .= "<li><span class=\"exifTitle\">Shutter Speed</span> " . $exif_array['SubIFD']['ExposureTime'] . "</li>\n";
				$output .= "<li><span class=\"exifTitle\">Focal Length</span> " . $exif_array['SubIFD']['FocalLength'] . "</li>\n";
				$output .= "<li><span class=\"exifTitle\">ISO</span> " . $exif_array['SubIFD']['ISOSpeedRatings']."</li>\n";
				$output .= "<li><span class=\"exifTitle\">Camera</span> " . $exif_array['IFD0']['Model'] . "</li>\n";
				$output .= "</ul>";
				
				if ($exif_array['IFD0']['Model'] != null) {
					return $content.$output;
				} else {
					return $content;
				}
			}
			
		} else {
			return $content;
		}
}

function ec_get_image_exif() {
	
		global $post;
		$post = get_post($post);
		
		if (is_attachment()) :
		
			if (wp_attachment_is_image($post->id)) {
		
				$att_image_full = wp_get_attachment_image_src( $post->id, "full" );
				$path_substr_start = strlen(get_bloginfo('wpurl'));
				$site_abs_path = ABSPATH;
				$att_image_path = $site_abs_path . substr($att_image_full[0], $path_substr_start); 
				$exif_array = read_exif_data_raw($att_image_path,0);

				
				//Set your date format. Common uses: m-d-Y (03-26-2009), F j, Y (March 26, 2009), d.m.Y (26.03.2009); http://us.php.net/date
				$exif_date = null;
				
				if ($exif_date == null) :
					$exif_date = get_option('date_format');
				endif;				

				//There are two aperture fields in the SubIFD array, FNumber and AptertureValue. Some programs strip one during edit or resize
				if ($exif_array['SubIFD']['ApertureValue'] != null) {
                	$apertureVal = $exif_array['SubIFD']['ApertureValue'];
                	} else {
                	$apertureVal = $exif_array['SubIFD']['FNumber'];
                }

				if ($exif_array['IFD0']['Model'] != null) :
				
					echo "<ul id=\"exifData\">\n";
					echo "<li><span class=\"exifTitle\">Original Size</span> <a href=\"" . $att_image_full[0] . "\">" . $exif_array['SubIFD']['ExifImageWidth'] . " x " . $exif_array['SubIFD']['ExifImageHeight'] . " pixels</a></li>\n";
					echo "<li><span class=\"exifTitle\">Date Taken</span> " . convertExifToTimestamp($exif_array['SubIFD']['DateTimeOriginal'], $exif_date) . "</li>\n";
					echo "<li><span class=\"exifTitle\">Aperture</span> " . $apertureVal . "</li>\n";
					echo "<li><span class=\"exifTitle\">Shutter Speed</span> " . $exif_array['SubIFD']['ExposureTime'] . "</li>\n";
					echo "<li><span class=\"exifTitle\">Focal Length</span> " . $exif_array['SubIFD']['FocalLength'] . "</li>\n";
					echo "<li><span class=\"exifTitle\">ISO</span> " . $exif_array['SubIFD']['ISOSpeedRatings']."</li>\n";
					echo "<li><span class=\"exifTitle\">Camera</span> " . $exif_array['IFD0']['Model'] . "</li>\n";
					echo "</ul>";			
					
				endif;
			}
			
		endif;
}

function ec_get_item_exif($att_image_url_or_ID) {
			
			$ec_http_hook = strpos($att_image_url_or_ID, 'http');
			if ($ec_http_hook === false) {
				$att_image_full = wp_get_attachment_image_src( $att_image_url_or_ID, "full" );
				$att_image_full_url = $att_image_full[0];
			} else {
				$att_image_full_url = $att_image_url_or_ID;
			}
							
			$path_substr_start = strlen(get_bloginfo('wpurl'));
			$site_abs_path = ABSPATH;
			$att_image_path = $site_abs_path . substr($att_image_full_url, $path_substr_start); 
			$exif_array = read_exif_data_raw($att_image_path,0);

			//Set your date format. Common uses: m-d-Y (03-26-2009), F j, Y (March 26, 2009), d.m.Y (26.03.2009); http://us.php.net/date
			$exif_date = null;
			
			if ($exif_date == null) :
				$exif_date = get_option('date_format');
			endif;				

			//There are two aperture fields in the SubIFD array, FNumber and AptertureValue. Some programs strip one during edit or resize
			if ($exif_array['SubIFD']['ApertureValue'] != null) {
				$apertureVal = $exif_array['SubIFD']['ApertureValue'];
				} else {
				$apertureVal = $exif_array['SubIFD']['FNumber'];
			}

			if ($exif_array['IFD0']['Model'] != null) :
			  
				echo "<ul id=\"exifData\">\n";
				echo "<li><span class=\"exifTitle\">Original Size</span> <a href=\"" . $att_image_full[0] . "\">" . $exif_array['SubIFD']['ExifImageWidth'] . " x " . $exif_array['SubIFD']['ExifImageHeight'] . " pixels</a></li>\n";
				echo "<li><span class=\"exifTitle\">Date Taken</span> " . convertExifToTimestamp($exif_array['SubIFD']['DateTimeOriginal'], $exif_date) . "</li>\n";
				echo "<li><span class=\"exifTitle\">Aperture</span> " . $apertureVal . "</li>\n";
				echo "<li><span class=\"exifTitle\">Shutter Speed</span> " . $exif_array['SubIFD']['ExposureTime'] . "</li>\n";
				echo "<li><span class=\"exifTitle\">Focal Length</span> " . $exif_array['SubIFD']['FocalLength'] . "</li>\n";
				echo "<li><span class=\"exifTitle\">ISO</span> " . $exif_array['SubIFD']['ISOSpeedRatings']."</li>\n";
				echo "<li><span class=\"exifTitle\">Camera</span> " . $exif_array['IFD0']['Model'] . "</li>\n";
				echo "</ul>";			
				  
			endif;
		  
}

function ec_exif_reader_debug() {

		if (is_attachment()) :
		
			if (wp_attachment_is_image($post->id)) {
		
				$att_image_full = wp_get_attachment_image_src( $post->id, "full" );
				$path_substr_start = strlen(get_bloginfo('wpurl'));
				$site_abs_path = ABSPATH;
				$att_image_path = $site_abs_path . substr($att_image_full[0], $path_substr_start); 
				$exif_array = read_exif_data_raw($att_image_path,0);
		
				echo "<PRE>";
				echo "The number of letters in your site URL, including \"http://\":\n";
				print_r($path_substr_start); 
				echo "\n \n";
				echo "Server path to your site directory:\n";
				print_r($site_abs_path);
				echo "\n \n";
				echo "Attachment full-size image URL:\n";
				print_r($att_image_path); 
				echo "\n \n";
				echo "Arrays of EXIF data...what we came for:\n";
				print_r($exif_array); 
				echo "</PRE>";			
			
			}
			
		endif;

}

function ec_exif_data_css() {

	echo '<link rel="stylesheet" type="text/css" media="screen" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wp-gallery-exif-reader/gallery-exif-reader.css" />';
	
}

add_action('wp_head', 'ec_exif_data_css');
add_filter('the_content', 'ec_get_attachment_exif');
//add_action('loop_start', 'ec_exif_reader_debug', 1);

?>