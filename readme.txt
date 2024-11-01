=== XSL to Feeds ===
Contributors: michaelmarus, trabaria
Tags: xslt, xsl, kml, dspace, data, feeds, FeedWordPress
Requires at least: 2.9.2
Tested up to: 3.3.2
Stable tag: 0.4

Easily feed your WordPress CMS with structured data from KML, Dspace digital assets repository metadata ... from virtually any XML source.

== Description ==
Structured data conversion is key to interoperability.  I have used xslt as a simple tool to define both the sources and rules for feeding data to my WordPress CMS.

This plugin allows you to upload XSLT files, manage them, such that your externally managed KML, Dspace collections, database dumps, or any XML source can be converted and used in WordPress.

The most common use is to take XML from other sources and transform it to a proper Feed, so that, for example, you can import the data with FeedWordPress.


== Installation ==
1. Extract `xsl-2-feeds.zip` to the `plugins` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Upload XSL files (based on the sample included in the installation) to the Media Library.
1. Go to the Settings > XSL to Feeds Settings screen to initiate the feeds, select the frequency of update, and copy the URLs for use with FeedWordPress.

== Frequently Asked Questions ==

= How do I make a feed from my Dspace collection? =

Use the sample.xsl file as a guide for getting a feed of all metadata from your collection.

= How can I import my KML from data I manage in a Google spreasheet? =

I'll be loading examples of KML to feeds in the near future, but in the meantime, the concept is always the same as that used in the sample.xsl included with the plugin.

== Screenshots ==

1. This is the basic setup, which allows you to add a cron job to generate feeds.
2. The XSL files uploaded appear here, and can be included in the cron job, or immediately generated.

== Changelog ==
= 0.4 =
* Fixed cron job issues regarding memory and scheduling.
= 0.3 =
* Added KML from Google Maps sample.
= 0.2 =
* Instructions Updated.
= 0.1 =
* First release.
== Upgrade Notice ==
= 0.3 =
Added KML from Google Maps sample XSL.
= 0.2 =
Instructions Updated
= 0.1 =
First version of the plugin