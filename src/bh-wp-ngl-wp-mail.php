<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           brianhenryie/bh-wp-ngl-wp-mail
 *
 * @wordpress-plugin
 * Plugin Name:       Newsletter Glue wp_mail() Integration
 * Plugin URI:        http://github.com/BrianHenryIE/bh-wp-ngl-wp-mail/
 * Description:       Adds WordPress's native email sender as an option in Newsletter glue.
 * Version:           0.1.0
 * Requires PHP:      7.4
 * Author:            BrianHenryIE
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bh-wp-ngl-wp-mail
 * Domain Path:       /languages
 */

namespace BrianHenryIE\WP_NGL_WP_Mail;

use BrianHenryIE\WP_NGL_WP_Mail\WP_Includes\Activator;
use BrianHenryIE\WP_NGL_WP_Mail\WP_Includes\Deactivator;
use BrianHenryIE\WP_NGL_WP_Mail\WP_Includes\BH_WP_NGL_WP_Mail;
use BrianHenryIE\WP_NGL_WP_Mail\WP_Includes\User;
use BrianHenryIE\WP_NGL_WP_Mail\WP_Logger\Logger;
use BrianHenryIE\WP_NGL_WP_Mail\WP_Logger\Logger_Settings_Interface;
use BrianHenryIE\WP_NGL_WP_Mail\WP_Logger\Logger_Settings_Trait;
use Psr\Log\LogLevel;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'autoload.php';

define( 'BH_WP_NGL_WP_MAIL_VERSION', '0.1.0' );
define( 'BH_WP_NGL_WP_MAIL_BASENAME', plugin_basename( __FILE__ ) );
define( 'BH_WP_NGL_WP_MAIL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, array( Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Deactivator::class, 'deactivate' ) );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function instantiate_bh_wp_ngl_wp_mail(): BH_WP_NGL_WP_Mail {

	$settings = new class() implements Logger_Settings_Interface {
		use Logger_Settings_Trait;

		public function get_log_level(): string {
			return LogLevel::DEBUG;
		}

		public function get_plugin_name(): string {
			return 'Newsletter Glue wp_mail() Integration';
		}

		public function get_plugin_slug(): string {
			return 'bh-wp-ngl-wp-mail';
		}

		public function get_plugin_basename(): string {
			return defined( 'BH_WP_NGL_WP_MAIL_BASENAME' ) ? BH_WP_NGL_WP_MAIL_BASENAME : 'bh-wp-ngl-wp-mail/bh-wp-ngl-wp-mail.php';
		}
	};

	$logger = Logger::instance( $settings );

	$plugin = new BH_WP_NGL_WP_Mail( $logger );

	return $plugin;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and frontend-facing site hooks.
 */
$GLOBALS['bh_wp_ngl_wp_mail'] = instantiate_bh_wp_ngl_wp_mail();
