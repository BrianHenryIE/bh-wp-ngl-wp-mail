<?php
/**
 *
 *
 * @package BH_WP_NGL_WP_Mail
 * @author  BrianHenryIE <BrianHenryIE@gmail.com>
 */

namespace BH_WP_NGL_WP_Mail\Includes;

/**
 * Class Plugin_WP_Mock_Test
 *
 * @coversDefaultClass \BH_WP_NGL_WP_Mail\Includes\I18n
 */
class I18n_Unit_Test extends \Codeception\Test\Unit {

	protected function setup(): void {
	    parent::setup();
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * Verify load_plugin_textdomain is correctly called.
	 *
	 * @covers ::load_plugin_textdomain
	 */
	public function test_load_plugin_textdomain() {

		global $plugin_root_dir;

        \WP_Mock::userFunction(
            'plugin_basename',
            array(
                'args'   => array(
                    \WP_Mock\Functions::type( 'string' )
                ),
                'return' => 'bh-wp-ngl-wp-mail',
                'times' => 1
            )
        );

        \WP_Mock::userFunction(
			'load_plugin_textdomain',
			array(
                'times' => 1,
				'args'   => array(
					'bh-wp-ngl-wp-mail',
					false,
					'bh-wp-ngl-wp-mail/languages/',
				)
			)
		);

        $i18n = new I18n();
        $i18n->load_plugin_textdomain();
	}
}
