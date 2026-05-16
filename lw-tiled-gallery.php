<?php
/**
 * Plugin Name: LW Tiled Gallery
 * Plugin URI:  https://github.com/leadwhite/lw-tiled-gallery
 * Description: A tiled mosaic gallery widget for Elementor. Replicates the layout algorithm of Jetpack's Tiled Mosaic gallery — aspect-ratio-driven rows with varied shapes — as a native Elementor widget with full style controls.
 * Version:     1.0.0
 * Author:      John Clark
 * Author URI:  https://leadwhite.co.uk
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: lw-tiled-gallery
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'LW_TILED_GALLERY_VERSION', '1.0.0' );
define( 'LW_TILED_GALLERY_PATH', plugin_dir_path( __FILE__ ) );
define( 'LW_TILED_GALLERY_URL', plugin_dir_url( __FILE__ ) );

/**
 * Initialise the plugin after all plugins are loaded.
 * Checks Elementor is active before registering anything.
 */
function lw_tiled_gallery_init() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', 'lw_tiled_gallery_missing_elementor_notice' );
        return;
    }

    add_action( 'elementor/widgets/register', 'lw_tiled_gallery_register_widget' );
    add_action( 'wp_enqueue_scripts', 'lw_tiled_gallery_enqueue_assets' );
    add_action( 'elementor/editor/after_enqueue_scripts', 'lw_tiled_gallery_enqueue_assets' );
}
add_action( 'plugins_loaded', 'lw_tiled_gallery_init' );

/**
 * Register the Elementor widget.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager
 */
function lw_tiled_gallery_register_widget( $widgets_manager ) {
    require_once LW_TILED_GALLERY_PATH . 'widgets/class-lw-tiled-gallery-widget.php';
    $widgets_manager->register( new LW_Tiled_Gallery_Widget() );
}

/**
 * Register plugin assets for conditional loading.
 * Actual enqueue is handled by get_style_depends() / get_script_depends()
 * on the widget class, so assets only load on pages where the widget is used.
 */
function lw_tiled_gallery_enqueue_assets() {
    wp_register_style(
        'lw-tiled-gallery',
        LW_TILED_GALLERY_URL . 'css/lw-tiled-gallery.css',
        array(),
        LW_TILED_GALLERY_VERSION
    );

    wp_register_script(
        'lw-tiled-gallery',
        LW_TILED_GALLERY_URL . 'js/lw-tiled-gallery.js',
        array(),
        LW_TILED_GALLERY_VERSION,
        true
    );
}

/**
 * Admin notice shown when Elementor is not active.
 */
function lw_tiled_gallery_missing_elementor_notice() {
    echo '<div class="notice notice-error"><p>';
    echo '<strong>LW Tiled Gallery</strong> requires Elementor to be installed and active.';
    echo '</p></div>';
}
