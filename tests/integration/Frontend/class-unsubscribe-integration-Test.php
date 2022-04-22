<?php

namespace BrianHenryIE\WP_NGL_WP_Mail\Frontend;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_NGL_WP_Mail\WP_Includes\User;

class Unsubscribe_Integration_Test extends \Codeception\TestCase\WPTestCase {

	public function test_post_unsubscribe(): void {

		$this->markTestIncomplete( 'We cant create a post for the test, then use wp_remote_http to query it' );

		$user_id = wp_create_user( 'test', 'abcdefg' );

		$wp_user = get_user_by( 'id', $user_id );

		$roles = $wp_user->roles;
		assert( false === in_array( User::UNSUBSCRIBED_ROLE, $roles, true ) );

		$new_post_args = array(
			'post_title'   => 'Test post',
			'post_content' => 'Test post content',
			'post_status'  => 'publish',
		);

		$post_id = wp_insert_post( $new_post_args );

		$unsubscribe = new Unsubscribe( new ColorLogger() );

		$url = $unsubscribe->get_unsubscribe_link( $wp_user, $post_id );

		$args = array(
			'body' => 'List-Unsubscribe=One-Click',
		);

		$response = wp_remote_post( $url, $args );

		$updated_wp_user       = get_user_by( 'id', $user_id );
		$updated_wp_user_roles = $updated_wp_user->roles;

		$this->assertContains( User::UNSUBSCRIBED_ROLE, $updated_wp_user_roles );

	}

}
