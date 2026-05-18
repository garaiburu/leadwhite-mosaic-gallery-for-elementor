=== Leadwhite Mosaic Gallery for Elementor ===
Contributors: garaiburu
Tags: gallery, elementor, tiled, mosaic, images
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A tiled mosaic gallery widget for Elementor with aspect-ratio-driven layout, full style controls, and hover overlays.

== Description ==

Leadwhite Mosaic Gallery for Elementor adds a Tiled Gallery widget to Elementor that produces a varied mosaic layout driven by image aspect ratios — similar to Jetpack's Tiled Mosaic gallery but built natively for Elementor with full style controls.

**How it works**

Images are grouped into rows automatically based on their aspect ratios. Each row uses the best available shape from a library of eight layout patterns (adapted from Jetpack's open-source grouper):

* Symmetric Row — portrait, two stacked landscapes, portrait
* One Three — portrait + three stacked landscapes
* Three One — three stacked landscapes + portrait
* One Two — single + stack of two
* Two One — stack of two + single
* Four — four singles
* Three — three singles
* Panoramic — single wide image fills the row

Row heights and column widths are calculated from aspect ratios so all images in a row share the same height, with no cropping distortion.

**Features**

* Tiled mosaic layout with 8 shape types
* Hover overlay with background colour, fade or slide-up animation
* Title and description pulled from attachment metadata (title, caption, alt, description)
* Independent typography controls for title and description
* Image border, border radius, and CSS filters (Normal and Hover states)
* Zoom In hover animation on images
* Responsive — stacks to single column below a configurable breakpoint
* Elementor lightbox integration
* Assets load only on pages where the widget is used

== Installation ==

1. Upload the `Leadwhite-Mosaic-Gallery-for-Elementor` folder to `/wp-content/plugins/`
2. Activate the plugin through the Plugins menu in WordPress
3. The Tiled Gallery widget will appear in the Elementor widget panel under General

== Usage ==

1. Add the Tiled Gallery widget to any Elementor page
2. Add images via the Gallery Images control in the Content tab
3. Configure layout (gap, mobile breakpoint) and interaction (lightbox, link) in the Content tab
4. Set overlay and image styles in the Style tab
5. Add captions or titles to images in the WordPress Media Library — these are used by the overlay Title and Description controls

== Changelog ==

= 1.0.0 =
* Initial release
* Eight shape types adapted from Jetpack tiled gallery grouper
* Full overlay system with independent title/description typography
* Image border, radius, CSS filters, zoom hover
* Conditional asset loading
* Responsive mobile stacking

== Frequently Asked Questions ==

= Does this require Jetpack? =

No. The layout algorithm is an independent port of Jetpack's open-source grouper logic. Jetpack is not required.

= Does this require Elementor Pro? =

Yes. The widget uses Elementor Pro controls for typography styling. 
Elementor Pro must be active.

= Where does the overlay text come from? =

Title and Description text is pulled from WordPress attachment metadata — the Title, Caption, Alt Text, and Description fields you set in the Media Library for each image.
