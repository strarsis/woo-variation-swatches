=== WooCommerce Variation Swatches ===
Contributors: EmranAhmed, wpeshaan
Tags: woocommerce variation, woocommerce, variation swatches, woocommerce attributes, product attributes, product color, product size, variable product attributes, variation product swatches, color variation swatch, image variation swatch
Requires at least: 4.8
Tested up to: 4.9
Requires PHP: 5.6
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Beautiful Color, Image and Buttons Variation Swatches For WooCommerce Product Attributes

== Description ==

[youtube https://www.youtube.com/watch?v=BySSYFuqwls]

WooCommerce Variation Swatches is easy to use WooCommerce product variation swatches plugin. It offers an aesthetic and professional experience to select attributes for variation products. It turns the product variation select options fields into radio images, colors, and label. It means with the help of this powerful WooCommerce color or image variation swatches plugin, you can show product variation items in images, colors, and label. So, you can say goodbye to dropdown product attribute fields.

WooCommerce Variation Swatches not only offers the color, image and label attributes in the single WooCommerce product. It also enables them in product quick view. If you're using WooCommerce themes like Flatsome that comes with default Quick View option, you don’t need to hassle to load color and image swatches for variable product attributes in quick view separately.

In the free WooCommerce attribute variation plugin, besides creating size, brand, image, color, and label variation swatches, you can handle the swatches shape style <strong>Rounded and Circle</strong>. On top of that, it allows you beautiful tooltips on variation swatch hover.  Based on your requirement you enable and disable hover from the settings. To maximize and personalize your development process,  the product attributes swatches comes an option to disable default plugin stylesheet so that you can personally write your own CSS. 

= Key Feature Comes with this Plugin =

* Work on variable product
* Work on variable product quick view
* Enable attributes into images swatches
* Enable attributes into color swatches
* Enable attributes into label/text/button swatches
* Control attribute setting globally.
* Compatible with popular themes and plugins
* Option to select ROUNDED and SQUARED variation shape.
* Flexible tooltip insert and display settings
* Option to disable default plugin stylesheet for theme developer.
* No extra option and no conclusion.

= Sites Built With WooCommerce Variation Swatches Plugin =
<blockquote>
<ul>
<li><a target="_blank" href="http://bit.ly/woovs-demo-04">gimp3d.com</a> | Built With: Flatsome Theme | Niche: 3D Printing Parts Store </li>
<li><a target="_blank" href="http://bit.ly/woovs-demo-02">loja2passos.com.br</a> | Built With: Flatsome Theme | Niche: Shoe Store </li>
<li><a target="_blank" href="http://bit.ly/woovs-demo-01">plotnikoff.ru</a> Built With: Uncode Theme | Niche: Furniture and Home Appliances Store </li>
<li><a target="_blank" href="http://bit.ly/woovs-demo-03">robinsongarden.co.uk</a> Built With: Labomba Theme | Niche: Bespoke Furniture Store</li>
</ul>
</blockquote>

= Officially tested plugins =

* WPML
* <a target="_blank" href="https://wordpress.org/plugins/polylang/">Polylang</a>
* <a target="_blank" href="https://wordpress.org/plugins/loco-translate/">Loco Translate</a>

= Forum and Feature Request =

<blockquote>
<h4>For feature request and bug reporting</h4>
<ul>
<li><a target="_blank" href="http://bit.ly/getwoopluginsgroup">Join Our Facebook Group</a></li>
</ul>
<h4>For contribution</h4>
<ul>
<li><a target="_blank" href="https://github.com/EmranAhmed/woo-variation-swatches/?utm_source=wordpress.org&utm_campaign=Woo+Variation+Swatches">Join Project in Github</a></li>
</ul>
<h4>For more information</h4>
<ul>
<li><a target="_blank" href="https://getwooplugins.com/?utm_source=wordpress.org&utm_campaign=Woo+Variation+Swatches">Visit Our Official Website</a></li>
</ul>
</blockquote>


== Installation ==

### Automatic Install From WordPress Dashboard

1. Login to your the admin panel
2. Navigate to Plugins -> Add New
3. Search **WooCommerce Variation Swatches**
4. Click install and activate respectively.

### Manual Install From WordPress Dashboard

If your server is not connected to the Internet, then you can use this method-

1. Download the plugin by clicking on the red button above. A ZIP file will be downloaded.
2. Login to your site's admin panel and navigate to Plugins -> Add New -> Upload.
3. Click choose file, select the plugin file and click install

### Install Using FTP

If you are unable to use any of the methods due to internet connectivity and file permission issues, then you can use this method-

1. Download the plugin by clicking on the red button above. A ZIP file will be downloaded.
2. Unzip the file.
3. Launch your favorite FTP client. Such as FileZilla, FireFTP, CyberDuck etc. If you are a more advanced user, then you can use SSH too.
4. Upload the folder to `wp-content/plugins/`
5. Log in to your WordPress dashboard.
6. Navigate to Plugins -> Installed
7. Activate the plugin

== Frequently Asked Questions ==

= How can I configure attributes? =

Even this plugin has been installed and activated on your site, variable products will still show dropdowns if you’ve not configured product attributes.

1. Log in to your WordPress dashboard, navigate to the Products menu and click Attributes.
2. Click to attribute name to edit an exists attribute or in the Add New Attribute form you will see the default Type selector.
3. Click to that Type selector to change attribute’s type. Besides default options Select and Text, there are more 3 options Color, Image, Button/Label to choose.
4. Select the suitable type for your attribute and click Save Change/Add attribute
5. Go back to manage attributes screen. Click the cog icon on the right side of attribute to start editing terms.
6. Start adding new terms or editing exists terms. There is will be a new option at the end of form that let you choose the color, upload image or choose as a button for those terms.

= Is it compatible with any kinds of WooCommerce Theme? =

Yes, it's compatible with any woocommerce theme including Flatsome / X-Theme / Avada / Uncode / Storefront / Labomba. But sometimes it may require small css tweak.

= Does it show in product QuickView? =

Yes, it supports any kinds of product quick view.

= How to use it on ajax load more? =

Just call this javascript function on ajax load event `$('.variations_form').trigger('wc_variation_form');` or `$('.variations_form').WooVariationSwatches();`. And your are ready to go.

== Screenshots ==

1. Single Product variation page
2. Product QuickView
3. Available Attribute Options
4. Attribute Image Type Preview
5. Attribute Color Type Preview
6. Attribute Button / Label Type Preview
7. Variation Product Edit view
8. Settings Panel

== Changelog ==

= 1.0.10 =

* Extendable hooks added to extend
* Fixed: Out Of Stock Product Issue.
* ajax variation threshold option added to control ajax variation.

= 1.0.9 =

* Merged Pull request from `spoyntersmith`
* Tooltip hardware acceleration issue fix for theme animation
* use jquery `sibling` instead of `prev`

= 1.0.8 =

* Improve variation javascript to support ajax variation
* Renamed tooltip attribute to resolve conflict
* Renamed variation javascript class name

= 1.0.7 =

* Improving frontend CSS
* Disable Bootstrap tooltip conflict

= 1.0.6 =

* Update translation

= 1.0.5 =

* Fix backend js issue

= 1.0.4 =

* Fix Number Issue selection

= 1.0.3 =

* Added css class on body based on settings
* tooltip and frontend css changed
* `add_theme_support( 'woo-variation-swatches', array( 'tooltip' => FALSE, 'stylesheet' => FALSE ) );` for theme developer default setting control.
* `wvs_clear_transient` to clear saved transient.

= 1.0.2 =

* Added tooltip
* Default stylesheet enable/disable option
* Display style added to show Rounded / Squared shaped style

= 1.0.1 =

* Fix text type select list

= 1.0.0 =

* Initial release

== Upgrade Notice ==
