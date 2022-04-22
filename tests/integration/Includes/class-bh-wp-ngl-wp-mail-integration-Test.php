<?php
/**
 * Tests for BH_WP_NGL_WP_Mail main setup class. Tests the actions are correctly added.
 *
 * @package BH_WP_NGL_WP_Mail
 * @author  BrianHenryIE <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_NGL_WP_Mail\WP_Includes;

use BrianHenryIE\WP_NGL_WP_Mail\Admin\Admin;
use BrianHenryIE\WP_NGL_WP_Mail\Frontend\Frontend;

/**
 * Class Develop_Test
 */
class BH_WP_NGL_WP_Mail_Integration_Test extends \Codeception\TestCase\WPTestCase {

	public function hooks() {
		$hooks = array(
			array( 'init', I18n::class, 'load_plugin_textdomain' ),
			array( 'admin_enqueue_scripts', Admin::class, 'enqueue_styles' ),
			array( 'admin_enqueue_scripts', Admin::class, 'enqueue_scripts' ),
		);
		return $hooks;
	}

	/**
	 * @dataProvider hooks
	 *
	 * @param string $action_name The name of the action.
	 * @param string $class_type The expected class.
	 * @param string $method_name The class's method.
	 * @param int    $expected_priority The priority of the action (default: 10).
	 */
	public function test_is_function_hooked_on_action( string $action_name, string $class_type, string $method_name, int $expected_priority = 10 ) {

		global $wp_filter;

		$this->assertArrayHasKey( $action_name, $wp_filter, "$method_name definitely not hooked to $action_name" );

		$actions_hooked = $wp_filter[ $action_name ];

		$this->assertArrayHasKey( $expected_priority, $actions_hooked, "$method_name definitely not hooked to $action_name priority $expected_priority" );

		$hooked_method = null;
		foreach ( $actions_hooked[ $expected_priority ] as $action ) {
			$action_function = $action['function'];
			if ( is_array( $action_function ) ) {
				if ( $action_function[0] instanceof $class_type ) {
					if ( $method_name === $action_function[1] ) {
						$hooked_method = $action_function[1];
						break;
					}
				}
			}
		}

		$this->assertNotNull( $hooked_method, "No methods on an instance of $class_type hooked to $action_name" );

		$this->assertEquals( $method_name, $hooked_method, "Unexpected method name for $class_type class hooked to $action_name" );

	}
}
