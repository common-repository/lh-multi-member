=== LH Multi Member ===
Contributors: shawfactor
Donate link: https://lhero.org/portfolio/lh-multi-member/
Tags: user, users, multisite
Requires at least: 3.6
Tested up to: 4.7
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copies all child site users and allocates them a role of unclaimed in the primary site. It also ensures that users that have an entry in wp_capabilities have been given a role

== Description ==

Want to give all your users a role on the main site, just install and activate and you are good to go.

== Installation ==

1. Upload the `lh-multi-member` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Does the plugin require a multisite installation? =

Yes, this only works and is appropriate on multisite.


== Changelog ==

= 1.00 =
* Initial release

**1.01 August 16, 2016**  
* Cron new role, and 4.6 bump

**1.02 February 16, 2016**  
* Give empty users a role