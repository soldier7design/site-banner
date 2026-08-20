=== Site Banner ===
Contributors: bencoyourdesign
Tags: banner, notification, announcement
Requires at least: 5.9
Tested up to: 7.1
Stable tag: 1.0.7
License: GPLv2 or later

Enable a sitewide banner for important notifications.

== Description ==

Site Banner adds a full-width notification bar to the top of any WordPress site.

Features:
* On/off toggle
* Title and short message text
* Optional link, opens the whole banner as a clickable bar, with an arrow icon
* Pulls global colors from Elementor automatically when available, plus a custom color picker
* Optional scheduled display with a "Now" or specific publish date/time, and an expiration date that turns the banner off automatically at midnight
* Visitors can close the banner. It collapses into a small alert icon tucked into the top-right corner, remembered via cookie
* Sits in normal page flow, not pinned to the viewport, so it scrolls away naturally as the visitor scrolls down

== Installation ==

1. Upload the site-banner folder to /wp-content/plugins/
2. Activate through the Plugins screen
3. Go to Site Banner in the left admin menu to configure

== Changelog ==

= 1.0.7 =
* Reopen indicator icon changed back to the arrow (rotated 45 degrees) instead of the caret, repositioned to sit better centered within the triangle.
* Fixed banner text now stays centered when it wraps to multiple lines on narrow screens, instead of the wrapped lines defaulting to left-aligned.
* Fixed mode now also pushes ordinary, non-fixed page content (like a theme header that isn't itself fixed or sticky) down by the banner's real height, not just other fixed/sticky elements. Recalculated on resize since text wrapping can change the banner's height, and the WordPress admin bar's height is always measured fresh rather than assumed. Only applies while the full banner is open, not for the small corner indicator.

= 1.0.6 =
* Reopen indicator now correctly accounts for the WordPress admin bar's real height (measured, not assumed) so it no longer sits hidden behind it when logged in.
* Reopen indicator redesigned as a small triangular corner tab flush to the top-left, with a caret icon rotated to point down and to the right.
* Added a new "Fix the banner to the top of the page" setting. When on, the banner and its collapsed indicator stay pinned to the top of the screen while scrolling and push the site's own fixed header down, with the admin bar and screen size accounted for automatically. When off (the default), everything sits in normal page flow and scrolls away as before.
* Banner padding is now symmetric left and right, so centered banner text stays visually centered at every screen size instead of drifting left on mobile.

= 1.0.5 =
* Close button top offset reduced from 6px to 3px to vertically center it.
* Reduced the banner's z-index from 999999 to 99.
* Reopen indicator icon changed from a megaphone to a down-facing caret, to better suggest that clicking it slides the banner back down.

= 1.0.4 =
* Reopen indicator no longer sits in normal page flow, it's absolutely positioned and centered over the header at a low z-index so it never pushes content down.
* Reopen indicator icon changed from an alert triangle to a megaphone.
* Reopen indicator corners changed to round both bottom corners, with a little more horizontal padding.
* Banner arrow icon nudged down 4px to vertically center it, and its left margin reduced from 10px to 7px.
* Banner content line-height set to 22px.
* Updated "Tested up to" to WordPress 7.1.

= 1.0.3 =
* Updated "Tested up to" to WordPress 7.0 to clear the compatibility warning on the plugin details screen.

= 1.0.2 =
* Banner and the reopen indicator are no longer fixed to the viewport. Both sit in normal page flow and scroll away with the rest of the page instead of staying pinned in place.
* Reopen indicator redesigned: a compact alert-triangle icon, flush into the top-right corner with only the bottom-left corner rounded, smaller padding.
* Replaced the text-character arrow on linked banners with a custom SVG arrow icon.
* Removed the JavaScript that nudged other fixed page elements out of the way, no longer needed now that nothing on the banner is fixed.

= 1.0.1 =
* Close button and reopen indicator changed from native button elements to styled divs, so theme-level button style resets can no longer bleed into them.
* Added keyboard support (Enter/Space) for the close and reopen controls to keep them accessible after the button-to-div change.

= 1.0.0 =
* Initial release.
* On/off toggle, title, message text, optional clickable link.
* Background, title, and text color pickers with automatic Elementor global color palette support.
* Optional scheduled display with publish (now or scheduled) and expiration date/time.
* Visitor dismiss with cookie memory, tied to the banner's content so edits reset the dismissal.
