=== SWG Downloads ===
Contributors: ohiowebpro
Tags: pdf, download, downloader
Requires at least: 5.9.3
Tested up to: 5.9.3
Stable tag: 1.0.10
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Simple downloader based on ACF and Bootstrap intended for wordpress theme developers

== Description ==

This plugin uses ACF and bootstrap 5. You must have the ACF plugin installed and be using Bootstrap 5 in your theme, as it does not load either.

After uploading a download, you will see the shortcode on the list page to include in a page.

You may also put downloads in categories and include the whole category with the shortcode format:

`[swg_downloads cat="cat-slug"]`

You can also set columns:

`[swg_downloads cat="cat-slug" col="3"]`

You can set the bootstrap breakpoint:

`[swg_downloads cat="cat-slug" col="3" break="sm"]`

You can override the bootstrap container with container attribute. If empty it will add container.

`[swg_downloads cat="cat-slug" col="3" break="sm" container="container-fluid"]`

You can add row classes to the row with row_class attribute.

`[swg_downloads cat="cat-slug" col="3" break="sm" container="container-fluid" row_class="justify-content-center"]`

You can override the view templates in the views folder by placing multiple.php or single.php in you theme in the directory /swg_plugins/swg_downloads/

You can also set a custom template to use with each shortcode by putting it in the directory /swg_plugins/swg_downloads/ and passing it in the shortcode like this:

`[swg_downloads cat="cat-slug" col="3" break="sm" container="container-fluid" row_class="justify-content-center" template="my-template.php"]`

Using ACF Pro will give you a width and height in the Theme Settings. After changing the size, you must regenerate thumbs with an image regen plugin.


== Frequently Asked Questions ==

= Who is this plugin for? =

Developers who use Bootstrap and ACF in custom themes



== Screenshots ==


== Changelog ==

= 1.0 =
* First version
