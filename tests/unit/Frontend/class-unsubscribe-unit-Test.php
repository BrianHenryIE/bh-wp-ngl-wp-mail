<?php

namespace BrianHenryIE\WP_NGL_WP_Mail\Frontend;

use BrianHenryIE\ColorLogger\ColorLogger;

/**
 * @coversDefaultClass \BrianHenryIE\WP_NGL_WP_Mail\Frontend\Unsubscribe
 */
class Unsubscribe_Unit_Test extends \Codeception\Test\Unit {

	protected function setup(): void {
		parent::setup();
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * Check the function returns quickly when the `unsubscribe` parameter is absent.
	 *
	 * To make the test fail add:
	 * `$_GET['unsubscribe'] ='123|charact8';`
	 *
	 * @covers ::handle_ngl_wp_mail_unsubscribe
	 */
	public function test_no_param_handle_ngl_wp_mail_unsubscribe(): void {

		$logger = new ColorLogger();

		$sut = new Unsubscribe( $logger );

		\WP_Mock::userFunction(
			'wp_unslash',
			array(
				'return_arg' => true,
			)
		);

		\WP_Mock::userFunction(
			'get_user_by',
			array(
				'args'  => array( 'id', \WP_Mock\Functions::type( 'int' ) ),
				'times' => 0,
			)
		);

		$sut->handle_ngl_wp_mail_unsubscribe();

	}


	/**
	 * Check the function returns quickly when the `unsubscribe` parameter is malformed.
	 *
	 * @covers ::handle_ngl_wp_mail_unsubscribe
	 */
	public function test_bad_param_handle_ngl_wp_mail_unsubscribe(): void {

		$logger = new ColorLogger();

		$sut = new Unsubscribe( $logger );

		// Bad param.
		$_GET['unsubscribe'] = '123|charact8910';

		\WP_Mock::userFunction(
			'wp_unslash',
			array(
				'return_arg' => true,
			)
		);

		\WP_Mock::userFunction(
			'get_user_by',
			array(
				'args'  => array( 'id', \WP_Mock\Functions::type( 'int' ) ),
				'times' => 0,
			)
		);

		$sut->handle_ngl_wp_mail_unsubscribe();

		$this->assertTrue( $logger->hasInfoThatMatches( '/Bad unsubscribe parameter passed/' ) );

	}
}
