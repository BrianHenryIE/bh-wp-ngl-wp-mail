<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    BH_WP_NGL_WP_Mail
 * @subpackage BH_WP_NGL_WP_Mail/admin
 */

namespace BH_WP_NGL_WP_Mail\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    BH_WP_NGL_WP_Mail
 * @subpackage BH_WP_NGL_WP_Mail/admin
 * @author     BrianHenryIE <BrianHenryIE@gmail.com>
 */
class Admin {

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @hooked admin_enqueue_scripts
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles(): void {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$version = defined( 'BH_WP_NGL_WP_MAIL_VERSION' ) ? BH_WP_NGL_WP_MAIL_VERSION : time();

		wp_enqueue_style( 'bh-wp-ngl-wp-mail', plugin_dir_url( __FILE__ ) . 'css/bh-wp-ngl-wp-mail-admin.css', array(), $version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @hooked admin_enqueue_scripts
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts(): void {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$version = defined( 'BH_WP_NGL_WP_MAIL_VERSION' ) ? BH_WP_NGL_WP_MAIL_VERSION : time();

		wp_enqueue_script( 'bh-wp-ngl-wp-mail', plugin_dir_url( __FILE__ ) . 'js/bh-wp-ngl-wp-mail-admin.js', array( 'jquery' ), $version, true );

	}

}
