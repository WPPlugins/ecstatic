=== ecSTATic ===
Contributors: Mike Soja
Donate link: http://www.kayak2u.com/blog/
Tags: charts, statistics, stats, visitors, visits, search engine tracker, visitor log, page views, graphs, browsers, referrers, IPs, login tracker, widget, geoip
Requires at least: 2.7
Tested up to: 3.6.1
Stable tag: 0.9933

Faster, Smarter, Visitor Management and Stats for your Wordpress Blog.

== Description ==

ecSTATic is a fast, flexible, feature packed Visitor logger for tracking Visitors, monitoring the multitudes of Bots and Spiders, and for blocking trackback and comment spammers.&nbsp; It even squirts out a graph or two, if needed.

== Features ==
* Code in two main sections:  A compact set of functions that quickly and efficiently record visitors, and a larger suite of files to provide the administrator views.
* More than sixty settings, including how to order your data, how much of it to show, along with who to block and how, whether to log logged-in users (and Administrators), whether to anonymize Referrer links, etc.
* Categorizes and tracks Visitors, Feed Reads, and Spider/Bots, and keeps cumulative totals of same, both as unique hits and clustered page views (settable) that are permanent (while purging old data from the live tables per your settings).
* Blocks failed login attempts after a certain number that you set.
* Sends daily, weekly, or you set it customizable eMails of the ecSTATic visitor log.
* Shows visitors by IP, User Agent, Referrer, Requested URI, and Domain, in great detail, in sortable tables!
* Classify visitors five ways by IP, IP range, User Agent token, or Referrer token, with a few easy clicks. Existing entries are easily edited or deleted.  No need to edit text files or wait for third party Updates to categorize your own entries.  Comes with a full list of known Spider/Bots.
* Export and import the main classification files for backup and restore.
* Score visitors based on a combination of IP, User Agent, Referrer, Requested URI, Domain Response, and five default items with user-settable thresholds.  A score of 10 blocks the visitor before they can steal your bandwidth.
* Automatically block unknown visitors who grab a hundred pages in a few seconds with the WTF (Way Too Fast) option.
* View Charts and Graphs for Page Views, Monthly Totals, Pages per Visitor, as well as for Browsers, Operating Systems, Spiders/Bots, Referrers, Search Engine Referrers, Search Phrases, Pages, Categories, and Feed Reads.
* Widgetized!  Currently displayable stats are "Visitors", "All Pages", "Feed Reeds", "Spider/Bots", "This Page", "Viz Today", "Pages Today", "RSS Today", "Bots Today", and "Visitors Online."&nbsp; All labels are customizable.&nbsp; The display order can be overridden.&nbsp; Style-able via CSS (with included CSS file to get things started.)
* Search the database (now with AJAX, returning results without a full page reload!)
* WHOIS and reverse IP lookup functions are built in, including two hooks to Maxmind's geolocation database, with customized links to Google Maps, Project Honeypot, and the RobTex blacklist lookup.
* Override the included .CSS file to set your own colors, fonts, etc., that persists across upgrades.
* Comes with a rudimentary (out of date) Help! file.&nbsp; For better help read the Changelog.
* All form data validated and kept within bounds.
* Universal use of WordPress's wpdb->prepare functions to protect against SQL injection attacks.  Plus, you can flag dodgy URI strings to block them before they have a chance to even try.
* Removes itself completely if you decide to uninstall it.
* Too many other features to mention!

== Installation ==

1. Create a folder/directory named "ecstatic" in your "/wp-content/plugins/" folder/directory
2. Upload the ecSTATic files to the newly created ecstatic folder/directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. ecSTATic picks it up from there.&nbsp; Look for a new button at the lower end of the dashboard menu buttons.&nbsp; Allow a little time for visitors stats to accrue.&nbsp; Visit the Settings page.  Enjoy.

== Screenshots ==

1. Main Page `screenshot-1.png`
2. Visitor Chart `screenshot-2.png`
3. Sequential Page `screenshot-3.png`
4. Details Page with WNKS table `screenshot-4.png`
5. Settings Page `screenshot-5.png`

== Changelog ==

= 0.9933 =
* September 12, 2013 - Bug fixes
* Fixed more bad defines.
* Eliminated test for PHP_MAXPATHLEN in ecstatic_tables.php.

= 0.9932 =
* September 12, 2013 - Bug fixes
* Changed the "load_latest" datetime variable to point to the timestamp of the most recently shown hit, rather than the current datetime.&nbsp; The old way could have missed hits.
* Fixed a chart javascript parameter.
* Eliminated wpdb->prepare function calls that aren't needed and were throwing Warnings.
* Fixed bad define test in ecstatic_tables.php

= 0.9931 =
* May 11, 2013
* Added Search Engines charts, other new charts, consolidated chart pages, rewrote the chart routines for organization and simplicity, added many new details, tweaks.
* Added a new form to the Details page so users can train the User Agent Parser in Browser identification, meaning users will no longer have to wait for me to update the ignored_ids entries.  Includes jQuery powered "help" link, and a new utility page so that I can export my copy of the ignored_ids array for new installations.
* Added special case tests to the User Agent Parser suite, along with an array of special case UA tokens to help parse the User Agents that previously slipped through the cracks.
* Added an automatic one-time-run function to find previously recorded "Unknown" Browsers, and send them through the new parser routines.
* Added an Option in the eMail Stats Settings section to include or not include clickable links in the eMailed stats.  The default is to send clickable links.
* Added a missing ">" to an object variable in a WhoIS function.
* Changed the Manual Purge forms to eliminate unnecessary complexity in returning to original page.
* Changed the default "Block link prefetching by Mozilla" Setting to OFF.

= 0.9920 =
* April 26, 2013
* Added a Setting to the ecSTATic Widget, so users can set the preferred number of recent minutes for which visitors are to be counted as "Online". See the WordPress Admin Widgets page to open the ecSTATic widget editing form.  And remember, the Widget has to be enabled in ecSTATic Settings.
* Updated out-of-date browser ID strings in the User Agent Parser.
* Then completely reworked the User Agent Parser functions (which are borrowed), eliminating kilobytes of unused code, and heavily modifying the classes and load arrangement.&nbsp; Interfacing code is streamlined, too.&nbsp; Much faster now, with less memory overhead.
* Fixed javascript that was throwing a Warning.&nbsp; Likewise, some CSS.&nbsp; Should look at the error logs more often.&nbsp; WordPress, itself, throws a LOT of errors and warnings.
* More Charts!&nbsp; Better Charts.&nbsp; Prettier Charts.&nbsp; Some of the old charts on the SomeStats page will show abnormalities until the New Charts are fully implemented.
* Chart development demanded tweaking to several routines.&nbsp; The previously unused aux field in the Referrers table is now used.&nbsp; I always knew it was for something.
* Added &lt;label&gt; elements to the WNKS table Add form to make checking the check boxes easier.
* Changed the way the Block Mozilla Prefetch option works.&nbsp; Such hits should now show up in the lists.
* Hellaceous bug was crashing the Opera browser after performing a Search in the Details page and mousing over the Scores.  Firefox and Chrome weren't bothered.  It was HTML related instead of jQuery.
* Changed a whoIS function to check for "*.*.*.*" and instead return IP.
* Added a CSS value to better show the state of the ShowUn/UnShow button.
* Updated some of the screenshots available on the WordPress plugins page, and moved them to the ecSTATic .svn "assets" folder per preferred WordPress practice, meaning they will no longer be included in downloads or upgrades from the WordPress repository.

= 0.9901 =
* April 11, 2013
* <b>NEW</b> NEVER BEFORE SEEN CHARTS (using the Google Chart API), via the new "New Charts" menu link.  More Charts to come, with eventual Build-Yer-Own capabilities (maybe).
* Developing the charts showed MAJOR PROBLEMS with one of the basic features of ecSTATic, pertaining to the NoLog setting.  Previously, entities set to NoLog were purposely NOT recorded, except for a bare count kept in the WNKS table.  After playing with the NEW Charts, it became apparent that even NoLog hits <i>should</i> be added to the database.  You don't want to see them in the listings, but you DO want to count them, right?  So, now, the NoLog flag has been renamed the NoShow flag.  All hits with or without the flag are logged, but the NoShow flag keeps entries out of the listings.  The new change more accurately reflects one's blog activity.  The downside is that it will be harder to look at your listings and cross reference them with the numbers shown in the tables and charts at the top of the pages, but you should be over that by now:  The numbers are all accurate.  Here's the new regime:  Regular Visitors are grouped as individuals according to the "Count as New Visitor after X minutes" Setting with Visitor identity determined by IP/User Agent.  Page Views are recorded on every hit.  Bots and RSS hits are grouped PER DAY as individuals, using the User Agent as identity, while the Page Totals are updated on every hit.  Relatedly, KILLed entries are NOT added to the Cumulative tallies, but still show up in the listings.  And all FAILED LogIn attempts are shown, regardless of the NoShow flag.
* There is now a "ShowUn/UnShow" button on the Sequential View page to toggle the hits normally hidden with the NoShow flag.
* Added a new option to the Settings page to anonymize Referrer links, so when you click the referrer links, the site at the other end doesn't see your Admin page referrer.  Options are "none", "anonym.to", "surfsneaky", "linkscheck", "nullrefer", and "urlink2", which are third party web sites that you have to redirect <i>through</i>.  Some may be similar to one another, and some may be faster than others.  (No warranties expressed or implied.)  If there are others that work better or that you'd like to see added, let me know.  Some of the other miscellaneous links could benefit from the same treatment and I will add them as time goes by.
* Tweaked the "Load Latest" and "Load All" functions to be more "sticky", requiring ecSTATic's first entry in the WP Options table.  The option name is "ecstatic_loadlatest", and if you uninstall ecSTATic, it is removed.
* By popular request:  Removed the links from the eMailed log.
* sQuashed another bug in the WhoIS functions, where a rare blank IP range wasn't tested for.
* Found TWO HUGE BUGS related to timestamps.  Really embarrassing.  gmdate() vs date().  Made changes in multiple files.  Still BETA after all these years.

= 0.9871 =
* April 1, 2013
* This version number fixes a versioning problem that has been recurring, where users are not informed of the latest version.

= 0.987 =
* April 1, 2013
* Added "Load Latest" and "Load All" buttons to the top of the Main Panels and Sequential View pages.  "Load Latest" retrieves all hits since the last *manual* ecSTATic refresh.  Using your browser's F5 refresh button, or setting an automatic refresh on the page after a "Load Latest" press will continue to use the previous "Load Latest" time value.  It's a feature!  Use "Load All" to break out of the "Load Latest" loop, or use "Load Latest" again to set a new value.
* More tweaks and code cleanup related to the CIDR preference.
* Tweaks to the Permalinks URI parsing.

= 0.986 =
* March 3, 2013
* Changed the "Options" menu name in the WordPress dashboard ecSTATic menu to "Settings".
* Updated column displays to remove duplicate columns for those using the new raw Requested URI Setting.
* Opened the door to IPv6 addresses.  A user reported all IPv6 visitors were being blocked.  They are now allowed through, unless the filter_var() function (only available with PHP 5.2 and above) flags them as invalid.  For those not running PHP 5.2 or above, a possibly dodgy regex expression test is given.   ---*** Attempting to use ecSTATic to block IPv6 addresses will NOT work. ***---
* Added the option to roll your own .CSS file that won't be overwritten with plugin upgrades.  The program checks "../wp-content/ecstatic/" for the file "my_ecstatic.css", and if found, loads it, *after* loading ecstatic.css from the ecstatic plugin directory.  Create a new folder "ecstatic" in the "wp-content" folder.  Copy your tweaked "my_ecstatic.css" file into that folder, and keep your personal settings there.  Do NOT just copy "ecstatic.css" to the "wp-content/ecstatic/my_ecstatic.css", as you'll be loading all the settings twice.  Use "my_ecstatic.css" to overwrite a subset of individual styles, and when I add new styles with upgrades, they'll still show up.
* By popular request:  Added a Manual Purge Setting.  There is a new Manual Purge menu option in the WordPress dashboard ecSTATic menu, whether or not the Manual Purge Option is enabled.  If the Setting is enabled, automatic purging is turned off, and buttons magically appear at the top of the Main and Sequential pages.  Non-automatic purging can take some time, depending on the number of items to be deleted from the database.
* By popular request:  The purge routine no longer combines purges of regular visitors, RSS feeds, and Spider/Bots if their values are equal to each other.  It gives more detailed feedback, at some slight loss in speed.
* By popular request:  Added a Setting to prefer CIDR notation in the WNKS table, over ecSTATic's DOS-like IP range descriptors.  Visitor IPs will be compared to the CIDR entries for blocking, etc.  The "Near IPs" tab in the Details pages will NOT find nearby CIDR entries in its little search.

= 0.9841 =
* February 28, 2013
* More tweaking of Permalinks URI parsing.
* Added Option to Miscellaneous Settings to show raw Requested URIs in all the listings, instead of parsed, abbreviated ones.
* Other tweaks geared toward simplicity, reducing db calls, and increasing speed.

= 0.983 =
* February 27, 2013
* Rewrote the URI parsing for Permalinks.  I think I got it this time.  Simpler.  More complete.

= 0.982 =
* January 27, 2013 one bug
* An inappropriate semicolon had the process_login_fails() function hooked, even when disabled.

= 0.981 =
* January 26, 2013 Small changes
* Changed the failed Login option parameters from TINYINT to SMALLINT to allow greater ranges.
* In the "Login Locker" option block, setting the third paramater (blocking minutes) to zero forces the program to calculate an appropriate blocking time on a sliding scale based on the number of minutes a visitor took to execute X number of failed Logins.  See Options page for details.
* Added code to prevent Administrators from being locked out accidentally.  A couple of people wrote about that.

= 0.98 =
* December 13, 2012
* Added new routines to track attempted Logins, both those that come in through the front Login door, and those that take the form of forged Cookies.  The user (that's you) specifies the number of attempted logins allowed over a designated period, and how long the IP is banned for exceeding that limit.  The old Anti-Maleagent Scoring Option -- Req. URI with 'login.php' -- is kaput.  Rather than scoring every instance of wp-login.php, which was problematic, at best, a hard limit (or no limit) is applied to the number of failed attempts.
* Added [logins] tag and logins tally to the Sequential Log eMail.  See the Options page for more.
* Added an X-ecSTATic header to the Sequential Log eMail options.  Provides a user settable x-header that some eMail clients can use for filtering or classification.
* Squashed bugs in the Sequential Log eMail suite related to the (now not so) recent switch to Daylight Saving Time.
* Fixed a bug where an unresolved Domain in the Main or Sequential pages was then resolved in the Details page, but with criss-crossed variables leaving a blank Domain tab.
* Tweaked the new "Near IPs" tab some more.  Added another small table, populated with IPs and Domains similar to the current visitor that have been previously scored, drawn from the main IP Table, representing Scored entities NOT in the WNKS Table.  Non-editable.
* The new tables in the "Near IPs" tab are sorted by IP, for which PHP's natsort() function is very handy.  That started me thinking about the other ecSTATic tables, which are sorted with <a href='http://www.leigeber.com/2009/03/table-sorter/' title='TinyTable' target='_blank'>Michael Leigeber's TinyTable</a> javascript sorting routine, which does a great job, but doesn't handle natural sorts.  Javascript, surprisingly, offers no function comparable to PHP's natsort(), but after a little searching, I found Jim Palmer's <a href='http://www.overset.com/2008/09/01/javascript-natural-sort-algorithm-with-unicode-support/' title='javascript natsort' target='_blank'>Javascript Natural Sort Algorithm With Unicode Support</a>.  After that, it only took about six hours to splice the two routines together.  In the end, it amounted to about 70 characters of new code, or two lines, plus the new natsort function.  Sweet.  So, now, to invoke the Natural Sort in your own projects, burnish the appropriate <th> tag with "class='natsort'" (the other available tag is "class='nosort'"). All the ecSTATic IP lists are now NatSorted, which is mo better than plain ASCII sorting.
* Re-re-re-refined the Referrer strings in all pages to improve sort results and general appearance.
* Semi-major change:  After being schooled in <a href='http://wordpress.org/support/topic/after-33-upgrade-plugin-upgrade-deletes-files-in-plugin-folder' title='plugin upgrade deletes files in plugin folder' target='_blank'>this</a> thread, I opted to move the Maxmind Geolocation files to wp-content/ecstatic, as Ipstenu suggested.  If you choose to download the Maxmind databases (instead of relying on the default, but external third party <a href='http://www.geoplugin.com/introduction' title='geoPlugin' target='_blank'>geoPlugin</a> which act as a middleman between you and the Maxmind databases), see the Help file for which files to download, then stick them in wp-content/ecstatic (which you'll have to create), rather than in the ecstatic plugin folder as you used to.  In the new location they won't be deleted on every ecSTATic upgrade.
* Thanks to user Alex, I found an unused ".boti" in ecstatic.css, which coincidentally matched an undefined "class='botp'" in ecstatic_interface.php.  The CSS definition now reads ".botp" in ecstatic.css.
* Option maxtoshow (max number of entries to show in Details pages) was not upgraded from TINYINT to SMALLINT across some versions.
* Fixed sloppy MySQL query string feeding the "Near IPs" tab in the Details pages.  That showed up in the new WordPress 3.5.

= 0.972 =
* February 17, 2012 Tweaks.
* Added "Seen X times" to Details report at top of page.  Saves scrolling through the WNKS table.
* Fine tuned the new "Near IPs" tab to better highlight overlapping and contained ranges.

= 0.971 =
* February 16, 2012 Bug fix.
* eMail time setting routine was wrong.

= 0.97 =
* February 15, 2012 Late Valentines Edition
* Referrer entries in the SomeStats small graphs page are now clickable, and zoom you away to the referring party's website.
* Cleaned up the Widget code and added a "Visitors Online" variable, ie. a count of unique IPs from the previous hour, for possible Widget output.  Visit your Admin Widget page to fidget with the Widget, and don't forget the Widget must also be enabled in ecSTATic Settings.
* Added a new tab to the Details page:  Near IPs.  The new routine takes the current visitor IP and pulls nearby Ranges from the WNKS table.  Helps eliminate near duplicate or overlapping range entries, and can help with consolidating adjacent ranges, thereby reducing unnecessary WNKS entries.  As with the WNKS table, entries may be edited in place, thanks to the magic of jQuery.
* Added a big new Option, to facilitate a daily, weekly, or any other regularly periodic eMailing of ecSTATic visitor logs to the address of your choice.  The body of the eMail resembles the Sequential page output, complete with clickable referrers and whatnot.  Numerous options abound.  Thanks to user Robert for this and other ideas.
