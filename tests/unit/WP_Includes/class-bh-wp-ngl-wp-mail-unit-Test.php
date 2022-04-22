<?php
/**
 * @package BH_WP_NGL_WP_Mail_Unit_Name
 * @author  BrianHenryIE <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_NGL_WP_Mail\WP_Includes;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_NGL_WP_Mail\Admin\Admin;
use BrianHenryIE\WP_NGL_WP_Mail\Frontend\Unsubscribe;
use WP_Mock\Matcher\AnyInstance;

/**
 * Class BH_WP_NGL_WP_Mail_Unit_Test
 *
 * @coversDefaultClass \BrianHenryIE\WP_NGL_WP_Mail\WP_Includes\BH_WP_NGL_WP_Mail
 */
class BH_WP_NGL_WP_Mail_Unit_Test extends \Codeception\Test\Unit {

	protected function setup(): void {
		parent::setup();
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * @covers ::set_locale
	 */
	public function test_set_locale_hooked() {

		\WP_Mock::expectActionAdded(
			'init',
			array( new AnyInstance( I18n::class ), 'load_plugin_textdomain' )
		);

		$logger = new ColorLogger();
		new BH_WP_NGL_WP_Mail( $logger );
	}

	/**
	 * @covers ::define_frontend_hooks
	 */
	public function test_frontend_hooks() {

		\WP_Mock::expectActionAdded(
			'wp',
			array( new AnyInstance( Unsubscribe::class ), 'handle_ngl_wp_mail_unsubscribe' )
		);

		$logger = new ColorLogger();
		new BH_WP_NGL_WP_Mail( $logger );
	}

}
