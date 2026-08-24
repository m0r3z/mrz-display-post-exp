=== MRZ Display Post Exp ===
Contributors: m0r3z
Tags: acf, custom post type, taxonomy, slider, grid
Requires at least: 6.3
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.1.1
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Display posts and custom posts with their ACF fields as a list, grid or slider, with customizable HTML templates, client-side filters and search.

== Description ==

MRZ Display Post Exp is a generic, minimalist plugin to display any post type — standard posts or custom post types — together with their ACF fields. Each item is rendered from a customizable HTML template using placeholders (ACF fields, taxonomies, title, thumbnail…). Everything is configured from the WordPress admin — no theme code required — and styled to match the site through the theme CSS.

= Features =

* Multiple display blocks via a dedicated Custom Post Type — each block has its own configuration and shortcode.
* Three display formats: list, grid and a dependency-free native CSS slider.
* Customizable HTML item template with ACF / taxonomy / post placeholders and conditionals.
* Client-side filters by taxonomy and by ACF field, in dropdown / radio / checkbox mode, with OR / AND logic.
* Optional client-side text search on titles (and selected ACF text fields).
* Optional URL filter synchronization — share a pre-filtered link.
* Responsive layout — filters above, left or right; collapsible filters on mobile.
* Shortcode with optional forced filter: `[mrzdpe id="X" filter_taxonomy="genre" filter_term="42"]`.
* Translation-ready (text-domain `mrz-display-post-exp`, .pot file provided).

= Requirements =

* WordPress 6.3 or later, PHP 7.4 or later.
* Advanced Custom Fields (Pro recommended) — for the ACF field placeholders and filters.

= Privacy / external calls =

MRZ Display Post Exp makes no external HTTP calls, loads no remote resources, and collects no telemetry. Everything runs locally on your site.

= Source code and contributions =

Development happens publicly on GitHub: https://github.com/m0r3z/mrz-display-post-exp

== Installation ==

1. Install and activate Advanced Custom Fields (Pro recommended).
2. Upload the plugin to `/wp-content/plugins/mrz-display-post-exp` and activate it.
3. Open the "MRZ Display Post" menu, create a display block, configure its source, template, filters and format.
4. Copy the generated shortcode and paste it into any page or post.

== Changelog ==

= 1.1.1 =
* Fix: the taxonomy filter rows were no longer displayed in the block admin screen after the 1.1.0 prefix rename (admin.js still referenced the old field IDs). No effect on the front end.

= 1.1.0 =
* Renamed the internal PHP prefix to "mrzdpe" (constants, namespace, functions, hooks, shortcode, CPT, meta keys) to meet WordPress.org's unique-prefix requirement. The public shortcode is now [mrzdpe]. A one-time automatic migration updates existing blocks and shortcodes on upgrade — no manual action needed. CSS classes and the text domain are unchanged.

= 1.0.6 =
* WordPress.org release preparation: public plugin name set to "MRZ Display Post Exp" (slug mrz-display-post-exp), contributor updated. No functional change.

= 1.0.5 =
* New: drag-and-drop reordering of the taxonomy and ACF filters in the admin — the front displays them in the chosen order (taxonomy group stays above the ACF group).

= 1.0.4 =
* New: option to restrict the list to the current context's category (related-posts mode) — on a single it keeps only posts sharing the current category and excludes the current post; on a category archive it restricts to that category. The context taxonomy is configurable.

= 1.0.3 =
* {acf_date} now outputs the spans in month / day / year order.

= 1.0.2 =
* Fix: the shortcode forced filter (filter_taxonomy / filter_term) now works even when the taxonomy is not enabled as a front filter (uses has_term on each post).

= 1.0.1 =
* New placeholder {acf_date:field} — splits an ACF date field into day/month/year spans, with an optional month format (m, n, F, M).
* Admin: the template placeholder help is now displayed below the item template, inside a collapsible toggle, with a dedicated date-format section.

= 1.0.0 =
* First stable release.
* Three display formats: list, grid and a dependency-free native CSS slider (configurable per-view, gap, speed, autoplay, loop, arrow position).
* Customizable HTML item template with placeholders: {post_title}, {post_url}, {post_excerpt}, {post_excerpt:N} (word-limited), {post_thumbnail}, {%acf_field%}, {taxonomy:slug} (each term in its own span), conditionals {#if}.
* Client-side filters (taxonomy + ACF, OR/AND), text search, optional URL filter sync, mobile filter toggle.
* Sort by an ACF date field with an upcoming/past server-side filter (agenda use-case).
* CSS aspect-ratio option for the featured image (object-fit, no file cropping).
* Configurable grid column min-width and gap.

= 0.1.0 =
* Initial beta — list / grid / slider display of posts and custom posts with ACF placeholders, client-side filters and search.
