<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * frontend-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    BH_WP_NGL_WP_Mail
 * @subpackage BH_WP_NGL_WP_Mail/includes
 */

namespace BH_WP_NGL_WP_Mail\Includes;

use BH_WP_NGL_WP_Mail\Admin\Admin;
use BH_WP_NGL_WP_Mail\Frontend\Frontend;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * frontend-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    BH_WP_NGL_WP_Mail
 * @subpackage BH_WP_NGL_WP_Mail/includes
 * @author     BrianHenryIE <BrianHenryIE@gmail.com>
 */
class BH_WP_NGL_WP_Mail {

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the frontend-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_frontend_hooks();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	protected function set_locale(): void {

		$plugin_i18n = new I18n();

		add_action( 'init', array( $plugin_i18n, 'load_plugin_textdomain' ) );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	protected function define_admin_hooks(): void {

		$plugin_admin = new Admin();

		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_scripts' ) );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	protected function define_frontend_hooks(): void {

		$plugin_frontend = new Frontend();

		add_action( 'wp_enqueue_scripts', array( $plugin_frontend, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $plugin_frontend, 'enqueue_scripts' ) );

	}

}