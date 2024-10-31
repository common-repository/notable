=== Plugin Name ===
Contributors: sgrayban
Donate link: https://www.paypal.me/SGrayban
Tags: social networks, del.icio.us, digg, reddit, notable, wp-notable
Requires at least: 3.0
Tested up to: 4.4
Stable tag: 2.3

Adds social bookmark links to each blog entry.

== Description ==

It puts a bar of icons at the bottom of each post allowing your readers to submit your posts to several different
social networking and bookmarking sites. (del.icio.us, digg, fark, etc.)

The original plugin was called <a href="http://blog.calevans.com/2006/02/08/notable-another-wordpress-plugin/">wp-notable</a>
but no longer worked or was supported. The author was Cal Evans.

== Installation ==

1. Copy the entire folder notable into wp-content/plugins/ folder on your blog.
2. Activate the plugin by going to your blog's Plugin tab and clicking Activate.
3. Then go to the Settings menu and you'll see notable listed. Chose the social links you want to allow.
4. Add `<?php if (function_exists('wp_notable')) wp_notable(); ?>` somewhere in your post loop in single.php

== Frequently Asked Questions ==

= Will you add more social links like twitter ? =

Yes. The new social networks will be added soon.

== Screenshots ==

1. Screen shot of the settings.

== Changelog ==

= 2.2 =
* Actually included the functions library this time !

= 2.1 =
* Forgot to add the functions file

= 2.0 =
* Code re-write to support wp 3.x

= 1.0 =
* original code by - Cal Evans

== Upgrade Notice ==

= 2.0 =
Remove the old blogbling/wp-notable.php plugin if you still have it and upload this version.
