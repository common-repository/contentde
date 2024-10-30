=== contentde ===
Contributors: contentde
Donate link: http://www.content.de/
Tags: content.de, seo, text, unique content, copywriting, keywords, content creation, Content Erstellung, content optimization, crowdsourcing, optimized texts, search engine optimization, seo, SEOText, SEO Texte, suchmaschinenoptimierte Texte, suchmaschinenoptimierung, text creation, text optimization, texte, Texterstellung, Webinhalte generieren, blogger, content, content marketing, content software, copywriter, freelance writer, freelancer, hire blogger, hire writer, seo content, seo software, web site content, website content, writer
Requires at least: 3.2
Tested up to: 6.3
Stable tag: 1.0.18
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Manage all your content.de texts directly from your wordpress-blog.

== Description ==

English:
The content.de wordpress-plugin allows you to access your content.de account
directly from the wordpress backend. You can order texts, list, review and
manage all the texts in your content.de account and import them into your
wordpress-blog. With content.de you can order easily SEO-texts from a network of over 5000 authors.
content.de delivers unique content. Every text ist checked by copyscape to avoid duplicate content in your blog.

For further information visit <a href="http://www.content.de">www.content.de</a>

Deutsch:
Mit dem content.de Wordpress-Plugin haben Sie bequemen Zugriff aus dem Wordpress-Backend heraus auf Ihren content.de Account.
So können Sie Texte beauftragen, abnehmen, überarbeiten und direkt in Ihren Wordpress-Blog übernehmen. Mit content.de können Sie ganz einfach
SEO-Texte von einem Netzwerk mit über 5000 Autoren schreiben lassen. Content.de liefert Unique Content für Ihren Blog, denn jeder Text wird von Copyscape
überprüft, um Duplicate Content zu vermeiden.

Für weitere Informationen besuchen Sie <a href="http://www.content.de">www.content.de</a>

== Installation ==

1. Upload the archive contents to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently asked questions ==

= What PHP-modules do i have to install? =
To make the plugin work properly you must install the following PHP-modules:
* soap or xmlrpc
* json

== Screenshots ==

1. order
2. order overview
3. login
4. creating a new order

== Changelog ==

= 1.0.18 =
* uses XML-RPC as default instead of SOAP für WP6.x

= 1.0.17 =
* extended BB-Code support

= 1.0.16 =
* minor backend css inclusion bug fixed

= 1.0.15 =
* project settings are being loaded automatically when a new project is select while creating a new order
* the editor for defining the briefing is much bigger now

= 1.0.14 =
* added support for custom post types

= 1.0.13 =
* number of orders shown ist adjustable in the settings section
* auto-archive option added in settings section
* individual prices features added

= 1.0.12 =
* menue position bug fixed

= 1.0.11 =
* the order overview now displays the id of an order

= 1.0.10 =
* added plugin icons

= 1.0.9 =
* the costs of a text will now be displayed properly

= 1.0.8 =
* fixed decoding of xmlrpc messages

= 1.0.7 =
* users can now cache their content.de orders for later use.

= 1.0.6 =
* a broken rpc module does not prevent module activation
* removed CURLOPT_FOLLOWLOCATION from the xmlrpc module, which caused problems with servers that use the ini-setting open_basedir

= 1.0.5 =
* More debug informations will be displayed when the plugin failed to activate

= 1.0.4 =
* BBCodes are now replaced with the respective HTML tags

= 1.0.3 =
* Fixed soap wsdl cache problem

= 1.0.2 =
* Fixed connection errors with the xmlrpc-module
* the connection is now tested when the plugin is being activated

= 1.0.1 =
* Added missing error translations
* Fixed ajax requests after form submissions
* TinyMCE now works properly in Firefox when placing a new order

= 1.0 =
* Initial release

== Upgrade Notice ==

none

