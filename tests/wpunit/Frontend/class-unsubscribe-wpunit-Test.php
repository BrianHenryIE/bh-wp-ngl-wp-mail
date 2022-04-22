<?php

namespace BrianHenryIE\WP_NGL_WP_Mail\Frontend;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_NGL_WP_Mail\WP_Includes\User;

/**
 * @coversDefaultClass \BrianHenryIE\WP_NGL_WP_Mail\Frontend\Unsubscribe
 */
class Unsubscribe_WPUnit_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * Given a user with a valid unsubscribe code, they should have the correct role after POSTing the to site.
	 *
	 * @covers ::handle_ngl_wp_mail_unsubscribe
	 */
	public function test_handle_ngl_wp_mail_unsubscribe_post(): void {

		add_role( User::UNSUBSCRIBED_ROLE, 'unsubscribed' );

		$user_id = wp_create_user( 'test', 'password' );
		$wp_user = get_user_by( 'id', $user_id );

		$new_post_args = array(
			'post_title'   => 'Test post',
			'post_content' => 'Test post content',
			'post_status'  => 'publish',
		);
		$post_id       = wp_insert_post( $new_post_args );
		global $post;
		$post = get_post( $post_id );

		$logger = new ColorLogger();
		$sut    = new Unsubscribe( $logger );

		$unsubscribe_key = $sut->get_unsubscribe_key_for_user( $wp_user, $post_id );

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_GET['unsubscribe']       = "{$user_id}|{$unsubscribe_key}";
		$_POST['List-Unsubscribe'] = 'One-Click';

		$sut->handle_ngl_wp_mail_unsubscribe();

		$updated_wp_user       = get_user_by( 'id', $user_id );
		$updated_wp_user_roles = $updated_wp_user->roles;

		$this->assertContains( User::UNSUBSCRIBED_ROLE, $updated_wp_user_roles );

	}


	/**
	 * @covers ::get_unsubscribe_key_for_user
	 */


	/**
	 * @covers ::get_unsubscribe_link
	 */


}
