<?php
/**
 * Tests for the root plugin file.
 *
 * @package BH_WP_NGL_WP_Mail
 * @author  BrianHenryIE <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_NGL_WP_Mail;

use BrianHenryIE\WP_NGL_WP_Mail\WP_Includes\BH_WP_NGL_WP_Mail;
use BrianHenryIE\WP_NGL_WP_Mail\WP_Logger\Logger;
use WP_Mock;

/**
 * Class Plugin_WP_Mock_Test
 */
class Plugin_Unit_Test extends \Codeception\Test\Unit {

	protected function setup() : void {
		parent::setUp();
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
		parent::tearDown();
	}

	/**
	 * Verifies the plugin initialization.
	 * Verifies the plugin does not output anything to screen.
	 */
	public function test_plugin_include() {

		// Prevents code-coverage counting, and removes the need to define the WordPress functions that are used in that class.
		\Patchwork\redefine(
			array( BH_WP_NGL_WP_Mail::class, '__construct' ),
			function() {}
		);

		\Patchwork\redefine(
			array( Logger::class, '__construct' ),
			function( $settings ) {}
		);

		$plugin_root_dir = dirname( __DIR__, 2 ) . '/src';

		WP_Mock::userFunction(
			'plugin_dir_path',
			array(
				'args'   => array( \WP_Mock\Functions::type( 'string' ) ),
				'return' => $plugin_root_dir . '/',
			)
		);

		global $plugin_basename;

		WP_Mock::userFunction(
			'plugin_basename',
			array(
				'args'   => array( \WP_Mock\Functions::type( 'string' ) ),
				'return' => $plugin_basename,
			)
		);

		WP_Mock::userFunction(
			'register_activation_hook'
		);

		WP_Mock::userFunction(
			'register_deactivation_hook'
		);

		ob_start();

		include $plugin_root_dir . '/bh-wp-ngl-wp-mail.php';

		$printed_output = ob_get_contents();

		ob_end_clean();

		$this->assertEmpty( $printed_output );

		$this->assertArrayHasKey( 'bh_wp_ngl_wp_mail', $GLOBALS );

		$this->assertInstanceOf( BH_WP_NGL_WP_Mail::class, $GLOBALS['bh_wp_ngl_wp_mail'] );

	}

}
