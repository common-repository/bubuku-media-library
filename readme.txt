=== Bubuku Media Library ===
Contributors: lruizcode
Tags: images, media library, performance, Alt Text, seo
Requires at least: 5.2
Tested up to: 6.5.3
Requires PHP: 7.2
Stable tag: 1.1.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Plugin to easily know if an image is large or not, it also shows if the image has the Alt text.

== Description ==
The plugin informs the content team in a simple way if the images are heavy and if they have alternative texts.

We can sort the media library by file size, filter by size, and Alt Text.

1. Green: Images 100K or less are assigned a good optimization status.
2. Orange: Images between 100K and 500k are assigned a medium optimization status.
3. Red: For images of 500k or more, we assign a status of poor optimization.

Thanks to this plugin, we can see if images need to be optimized and improve page load and SEO of images.

More information in Spanish about the plugin in the link [How to know if we have to reduce weight to the image and Alt SEO attribute](https://www.bubuku.com/como-saber-reducir-peso-imagen-atributo-alt-seo/)

== Installation ==
Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your
WordPress installation and then activate the Plugin from Plugins page.

== Screenshots ==
1. The column where we show the image size information and if it has alt text. We can also sort by size from largest to smallest.
2. Filter to better identify the images that have the alt text
3. Filter to identify images with larger or smaller sizes.
4. Bulk Action to calculate file size.
5. Plugin settings page
6. Weekly report that are sent by email with the optimization status of the images and alternative text.
7. Dashboard widget where the media library summary is displayed.

== Changelog ==
= 1.1.1 =
* fix widget texts
* Compatibility: WordPress 6.5 – WordPress 6.5.3.

= 1.1.0 =
* Add dashboard widget to display media library summary
* Remove friendly url functionality in image file names. Gave problems with image optimization plugins.

= 1.0.9 =
* Remove "Weekly" in informative texts.
* Fix image URL in metadata['file'].

= 1.0.8 =
* Default media library reporting was disabled, and modifications were made to the email report texts.
* Configuration options for sending the report: weekly, monthly or deactivated.
* Fix error when renaming images to lowercase.

= 1.0.7 =
* Make filename URLs and thumbnail URLs friendly.
* Updates image size value when edited.
* Add button on image to recalculate size.
* Fix API error when WordPress is configured to end with a slash.
* Compatibility: WordPress 6.1 – WordPress 6.4.2.

= 1.0.6 =
* Compatibility: WordPress 6.1 – WordPress 6.3.

= 1.0.5 =
* Add weekly reports that are sent by email with the optimization status of the images and alternative text.
* Add plugin settings page.

= 1.0.4 =
* Compatibility: WordPress 6.1 – WordPress 6.2.
* Fix some PHP errors.
* Add WordPress JavaScript dependencies.

= 1.0.3 =
* Add a Bulk Action to calculate file size in WordPress admin.
* Improvements in measurement ranges.
* fix: Internationalization Issues.

= 1.0.2 =
* Rename variables.
* Correctly filter by file size.
* Styling WordPress Button.

= 1.0.1 =
* Add calculate message to sort and filter.
* Add 'This media is not supported' message in the file size column.

= 1.0.0 =
* Initial release.