<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    BH_WP_NGL_WP_Mail
 * @subpackage BH_WP_NGL_WP_Mail/includes
 */

namespace BH_WP_NGL_WP_Mail\Includes;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    BH_WP_NGL_WP_Mail
 * @subpackage BH_WP_NGL_WP_Mail/includes
 * @author     BrianHenryIE <BrianHenryIE@gmail.com>
 */
class I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @hooked plugins_loaded
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain(): void {

		load_plugin_textdomain(
			'bh-wp-ngl-wp-mail',
			false,
            plugin_basename( dirname( __FILE__, 2 ) ) . '/languages/'
		);

	}

}
