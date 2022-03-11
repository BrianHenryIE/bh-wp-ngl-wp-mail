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
 * @package           BH_WP_NGL_WP_Mail
 *
 * @wordpress-plugin
 * Plugin Name:       BH WP NGL WP Mail
 * Plugin URI:        http://github.com/username/bh-wp-ngl-wp-mail/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Requires PHP:      7.4
 * Author:            BrianHenryIE
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bh-wp-ngl-wp-mail
 * Domain Path:       /languages
 */

namespace BH_WP_NGL_WP_Mail;

use BH_WP_NGL_WP_Mail\Includes\Activator;
use BH_WP_NGL_WP_Mail\Includes\Deactivator;
use BH_WP_NGL_WP_Mail\Includes\BH_WP_NGL_WP_Mail;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'autoload.php';

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BH_WP_NGL_WP_MAIL_VERSION', '1.0.0' );

define( 'BH_WP_NGL_WP_MAIL_BASENAME', plugin_basename( __FILE__ ) );

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

	$plugin = new BH_WP_NGL_WP_Mail();

	return $plugin;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and frontend-facing site hooks.
 */
$GLOBALS['bh_wp_ngl_wp_mail'] = instantiate_bh_wp_ngl_wp_mail();
