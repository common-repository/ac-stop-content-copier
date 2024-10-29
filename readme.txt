=== AC STOP Content Copier ===
Contributors: adaptcoder
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=7V7GRJBM8J4KJ&lc=US&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: scrapers, scrapy, stop Copier, stop thiefs, stop copy, copy protect, protect content
Requires at least: 3.0.1 or higher
Tested up to: 4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Content is king! Protect your unique work by using this plugin and BLOCK UNWANTED CONTENT SCRAPERS.

== Description ==

This plugin will help keep away programmatic content scrapers by protecting your VALUABLE CONTENT with a three layers of checks.


Similar to Google: Show CAPTCHA for suspicious “visitors” and never lose the REAL ONES.


Layer 1: This is the most basic stripping out programming languages user agents like cURL* (used by PHP and many others), libwww-perl (used by Perl language to scrape websites), scrappy (library used by Python, Ruby) and many others.

Layer 2: A bot will always browse very fast and it will be able to “browse” a lot of your pages within a very low seconds range. The plugin detects this and shows a captcha to the “visitor” to make sure it isn’t a bot.

Layer 3: Complex behaviour computation – a bot is set to crawl your site at the same time using a cronjob. We detect that by using a three day comparison. If such a scraper is detected, the guest is asked for a CAPTCHA to ensure it is legit!
Also, to keep you updated with what’s happening in the background, WP STOP CONTENT Copier is logging everything so you can analyze: IP addresses asked for CAPTCHA and the result: (COMPLETED CAPTCHA OR FAILED)

= Start protecting your valuable content from thiefs and avoid having duplicates on search engines. =


== Installation ==

Installing the plugin is very easy just like any other wordpress plugin. 
Connect to your WP-ADMIN panel by going to yoursite.com/wp-admin/

1st step) Point to plugins section and click add new. Then Choose upload file and pick the ZIP file you have downloaded.

2nd step) Second step is to activate the plugin by going to wp-admin->plugins. From the list of the plugins activate AC Stop Content Copier.


== Frequently Asked Questions ==

= Setting fast browse interval =

What is fast browse interval? : Basically this is a measure to detect a user browsing your website TOO QUICKLY.
WHY: A real user would take 2-3 seconds to go to a next page of your site while a robot can browse even 10-100-etc pages in a second.

You can choose the allowed number of seconds before a new page can be requested (browsed) by setting fast browse interval : Just go to Stop Copier page in wp-admin panel and enter an interval of seconds to allow users browse from page to page. We recommend using a 2 or 3 second.


= Changing CAPTCHA REQUEST page title =

This page is automatically created when the plugin is installed containg a shortcode called [acbd_show_captcha] 

When the plugin is detecting a suspicious visitor it will redirect to a CAPTCHA COMPLETION page requesting for a code to be entered by visitor to confirm he is a real human guest and not a content scraper. 

Changing CAPTCHA Request Page title is the same as you would change any other wordpress page title: go to wp-admin->pages and find the page called WP Stop Content Copier Captcha.


= Log page =

To keep you updated with what's happening in the background, WP STOP CONTENT Copier is logging everything so you can analyze: IP addresses (visitors) asked for CAPTCHA and the result: (COMPLETED CAPTCHA OR FAILED).

Find this log into AC Stop Content Copier page in wp-admin panel.


== Screenshots ==

1. Plugin setting Page.