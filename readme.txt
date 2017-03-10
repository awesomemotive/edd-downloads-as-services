=== EDD Downloads As Services ===
Contributors: easydigitaldownloads, sumobi
Tags: easy digital downloads, digital downloads, e-downloads, edd, services, e-commerce, ecommerce, sumobi
Requires at least: 3.3
Tested up to: 4.7.3
Stable tag: 1.0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Mark Downloads As Services in Easy Digital Downloads

== Description ==

This plugin requires [Easy Digital Downloads](http://wordpress.org/extend/plugins/easy-digital-downloads/ "Easy Digital Downloads") v1.9 or greater.

Many customers use Easy Digital Downloads to sell "services" rather than "downloads". We get many requests to remove the "no downloadable files" on the purchase confirmation page, because their services do not have/need files attached. This plugin fixes that.

It will allow you to select individual downloads which are services, or an entire category which houses your services. If a download is marked as a service, or belongs to one of the categories you selected, the the purchase confirmation page will not show "no downloadable files" for that particular download. If your service does have a file attached, it will still show the file. It also slightly modifies the email receipt so it does not include a dash next to the name.

So far, this plugin allows you to:

1. Hide the "no downloadable files found" message on the purchase confirmation page when your service has no download files attached to it
1. Hide the extra dash in the purchase receipt email when your service has no download files attached to it
1. Select categories from the plugin settings that house your "services". Any download within the selected categories will be marked as a service
1. Mark downloads as services on a per-download level. A checkbox is added to the download configuration metabox

Suggestions welcome for future features. Developers can take advantage of the _edd_das_enabled meta_key and extend the plugin to suit their needs.

== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to your site
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin

OR you can just install it with WordPress by going to Plugins >> Add New >> and type this plugin's name

== Screenshots ==

1. Select a category to make all downloads within it a service
1. Mark downloads as services on a per-download level

== Upgrade Notice ==

= 1.0.6 =
* Fix: Previous version was missing code related to the update.

== Changelog ==

= 1.0.6 =
* Fix: Previous version was missing code related to the update.

= 1.0.5 =
* Fix: Removed the "No downloadable files found." text in email receipts for download services without a file. This was due to a recent update to EDD.

= 1.0.4 =
* New: Spanish translations courtesy of Andrew Kurtis

= 1.0.3 =
* Fix: Fatal error that could occur

= 1.0.2 =
* Fix: PHP notice
* Fix: PHP warning

= 1.0.1 =
* Fix: If a download is marked as a service but has downloadable files attached, the files will be still shown as normal
* Fix: Moved the checkbox option to the "Download Settings" metabox

= 1.0 =
* Initial release
