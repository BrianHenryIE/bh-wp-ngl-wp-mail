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
 * @package    brianhenryie/bh-wp-ngl-wp-mail
 */

namespace BrianHenryIE\WP_NGL_WP_Mail\WP_Includes;

use BrianHenryIE\WP_NGL_WP_Mail\Frontend\Unsubscribe;
use BrianHenryIE\WP_NGL_WP_Mail\Newsletter_Glue\Content;
use BrianHenryIE\WP_NGL_WP_Mail\Newsletter_Glue\Register;
use BrianHenryIE\WP_NGL_WP_Mail\Newsletter_Glue\Settings;
use Psr\Log\LoggerInterface;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * frontend-facing site hooks.
 *
 * @since      1.0.0
 * @package    brianhenryie/bh-wp-ngl-wp-mail

 * @author     BrianHenryIE <BrianHenryIE@gmail.com>
 */
class BH_WP_NGL_WP_Mail {

	protected LoggerInterface $logger;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the frontend-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( LoggerInterface $logger ) {

		$this->logger = $logger;

		$this->set_locale();

		$this->define_frontend_hooks();

		$this->define_register_hooks();
		$this->define_settings_hooks();
		$this->define_content_hooks();

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
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	protected function define_frontend_hooks(): void {

		$unsubscribe = new Unsubscribe( $this->logger );

		add_action( 'wp', array( $unsubscribe, 'handle_ngl_wp_mail_unsubscribe' ) );

		add_action( 'wp_ajax_bh_ngl_handle_unsubscribe', array( $unsubscribe, 'ajax_handle_unsubscribe' ) );
	}

	/**
	 * Add actions for the basic registration fo the integration with Newsletter Glue.
	 */
	protected function define_register_hooks(): void {

		$register = new Register();

		add_filter( 'newsletterglue_get_esp_list', array( $register, 'add_to_email_service_provider_list' ) );

		add_filter( 'newsletterglue_get_supported_apps', array( $register, 'add_as_supported_app' ) );

		add_filter( 'newsletterglue_get_url', array( $register, 'get_url' ), 10, 2 );

		add_filter( 'newsletterglue_get_path', array( $register, 'get_path' ), 10, 2 );

	}

	protected function define_settings_hooks(): void {

		$settings = new Settings();

		add_filter( 'newsletterglue_allow_connection_edit', array( $settings, 'filter_newsletterglue_should_allow_connection_edit_wp_mail' ), 10, 2 );

	}

	protected function define_content_hooks(): void {

		$content = new Content();

		add_filter( 'newsletterglue_email_content_wp_mail', array( $content, 'filter_newsletterglue_email_content_wp_mail' ), 10, 3 );

	}

}
