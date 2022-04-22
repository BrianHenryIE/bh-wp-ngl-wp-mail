<?php
/**
 * Loads all required classes
 *
 * Uses classmap, PSR4 & wp-namespace-autoloader.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           BH_WP_NGL_WP_Mail
 *
 * @see https://github.com/pablo-sg-pacheco/wp-namespace-autoloader/
 */

namespace BrianHenryIE\WP_NGL_WP_Mail;

use BrianHenryIE\WP_NGL_WP_Mail\Pablo_Pacheco\WP_Namespace_Autoloader\WP_Namespace_Autoloader;

add_action(
	'plugins_loaded',
	function(): void {

		if ( ! defined( 'NGL_PLUGIN_DIR' ) ) {
			return;
		}

		$class_map = array(
			'NGL_Abstract_Integration' => NGL_PLUGIN_DIR . 'includes/abstract-integration.php',
		);

		if ( is_array( $class_map ) ) {
			spl_autoload_register(
				function ( $classname ) use ( $class_map ) {

					if ( array_key_exists( $classname, $class_map ) && file_exists( $class_map[ $classname ] ) ) {
						require_once $class_map[ $classname ];
					}
				}
			);
		}
	},
	0
);

// Load strauss classes after autoload-classmap.php so classes can be substituted.
require_once __DIR__ . '/strauss/autoload.php';

$wpcs_autoloader = new WP_Namespace_Autoloader();
$wpcs_autoloader->init();

