<?php
/**
 * Plugin Name: Background Hover Widget
 * Description: Elementor widget — change the entire widget background on hover (repeater up to 4 items) with title and description animation.
 * Version: 1.1.0
 * Author: Sanket Thakkar
 * Text Domain: background-hover-widget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Check if Elementor is active
function bhw_check_elementor_active() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', 'bhw_elementor_missing_notice' );
        deactivate_plugins( plugin_basename( __FILE__ ) );
    }
}
add_action( 'plugins_loaded', 'bhw_check_elementor_active' );

function bhw_elementor_missing_notice() {
    if ( current_user_can( 'activate_plugins' ) ) {
        ?>
        <div class="notice notice-error">
            <p><?php esc_html_e( 'Background Hover Widget requires Elementor to be installed and activated.', 'background-hover-widget' ); ?></p>
        </div>
        <?php
    }
}

// Register scripts & styles
function bhw_register_assets() {
    $plugin_url = plugin_dir_url( __FILE__ );
    wp_register_style( 'bhw-frontend', $plugin_url . 'assets/css/frontend.css', array(), '1.1.0' );
    wp_register_script( 'bhw-frontend', $plugin_url . 'assets/js/frontend.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'init', 'bhw_register_assets' );

// Register the widget
function bhw_register_widget( $widgets_manager ) {
    require_once( __DIR__ . '/includes/widget-background-hover.php' );
    $widgets_manager->register( new \BHW\Widget_Background_Hover() );
}
add_action( 'elementor/widgets/register', 'bhw_register_widget' );