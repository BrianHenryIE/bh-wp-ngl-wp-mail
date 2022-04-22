<?php

namespace BrianHenryIE\WP_NGL_WP_Mail\Newsletter_Glue;

use BrianHenryIE\WP_NGL_WP_Mail\WP_Includes\User;

/**
 * @coversDefaultClass \BrianHenryIE\WP_NGL_WP_Mail\Newsletter_Glue\WP_Mail_Integration
 */
class WP_Mail_Integration_WP_Unit_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * @covers ::send_newsletter
	 */
	public function test_send_newsletter(): void {

		$sut = new WP_Mail_Integration();

		$new_post_args = array(
			'post_title'   => 'Test post',
			'post_content' => 'Test post content',
			'post_status'  => 'publish',
		);
		$post_id       = wp_insert_post( $new_post_args );

		$data = array(
			'app'            => 'wp_mail',
			'subject'        => 'test2',
			'preview_text'   => '',
			'from_name'      => 'BHWP.ie',
			'from_email'     => 'brianhenryie@gmail.com',
			'test_email'     => 'brianhenryie@gmail.com',
			'schedule'       => 'immediately',
			'add_featured'   => '1',
			'double_confirm' => 'yes',
			'brand'          => '',
			'lists'          => '',
			'groups'         => '',
			'segments'       => '',
			'track_opens'    => 0,
			'track_clicks'   => 0,
			'unsub_groups'   => '', // Used by SendGrid only.
			'sent'           => true,
		);

		$is_test = false;

		// Could throw an exception in wp_mail for the test.

		$result = $sut->send_newsletter( $post_id, $data, $is_test );

	}


	/**
	 * Test subscribing an existing user through the block.
	 *
	 * @covers ::add_user
	 */
	public function test_add_user_existing_user(): void {

		add_role( User::UNSUBSCRIBED_ROLE, 'unsubscribed' );

		$sut = new WP_Mail_Integration();

		$data = array(
			'email' => 'brianhenryie@example.com',
		);

		$user_id        = wp_create_user( 'brianhenryie', 'password', 'brianhenryie@example.com' );
		$wp_user_before = get_user_by( 'id', $user_id );
		$wp_user_before->add_role( User::UNSUBSCRIBED_ROLE );

		$wp_user_before_2       = get_user_by( 'id', $user_id );
		$wp_user_before_2_roles = $wp_user_before_2->roles;
		assert( in_array( User::UNSUBSCRIBED_ROLE, $wp_user_before_2_roles, true ) );

		$result = $sut->add_user( $data );

		$wp_user_after = get_user_by( 'id', $user_id );

		$this->assertNotContains( User::UNSUBSCRIBED_ROLE, $wp_user_after->roles );

		$this->assertEquals( 1, $result );
	}


	/**
	 * Test subscribing a new user through the block.
	 *
	 * @covers ::add_user
	 */
	public function test_add_user_new_user(): void {

		add_role( User::UNSUBSCRIBED_ROLE, 'unsubscribed' );

		$sut = new WP_Mail_Integration();

		$data = array(
			'email' => 'brianhenryie@example.com',
		);

		assert( false === get_user_by( 'email', 'brianhenryie@example.com' ) );

		$result = $sut->add_user( $data );

		$this->assertNotFalse( get_user_by( 'email', 'brianhenryie@example.com' ) );

		$this->assertEquals( 1, $result );
	}
}
