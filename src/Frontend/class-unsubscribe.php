<?php
/**
 * Listen on `init` for the unsubscribe link.
 * If the request is a POST with the correct body, unsubscribe the user.
 * Otherwise enqueue JavaScript that will unsubscribe the user on page load.
 * Redirect the user to the Unsubscribe Confirmation page.
 *
 * @see https://www.ietf.org/rfc/rfc2369.txt
 * @see https://datatracker.ietf.org/doc/html/rfc8058
 *
 * To add email (mailto:) unsubscribe, install bh-wp-one-click-list-unsubscribe.
 *
 * @see https://github.com/BrianHenryIE/bh-wp-one-click-list-unsubscribe
 *
 * @package brianhenryie/bh-wp-ngl-wp-mail
 */

namespace BrianHenryIE\WP_NGL_WP_Mail\Frontend;

use BrianHenryIE\WP_NGL_WP_Mail\WP_Includes\User;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use WP;
use WP_User;

class Unsubscribe {
	use LoggerAwareTrait;

	/**
	 * The page id to redirect users to after unsubscribing will be stored in this wp_option.
	 */
	const UNSUBSCRIBED_PAGE_ID_OPTION_NAME = 'bh_wp_ngl_wp_mail_unsubscribed_page_id';

	public function __construct( LoggerInterface $logger ) {
		$this->setLogger( $logger );
	}

	/**
	 *
	 * Newsletterglue footers include the code
	 *
	 * {{ unsubscribe_link }}
	 */

	// Given a user id and a post id, generate a token for unsubscribing.

	// Or if they're logged in already

	// This needs to go in every link.

	// There needs to be an unsubscribe page.
	// Autogenerate one with the subscribe form
	// Set it hidden from search indexing.
	// Autofill the unsubscribe form with the email address for resubscription

	/**
	 * Listen for query-strings containing "unsubscribe" and adds the "unsubscribed" role to users who clicked the link.
	 *
	 * A valid link has ?unsubscribe=123|88888888 –– an `unsubscribe` key with a user id, followed by `|`, followed by
	 * an eight character hash.
	 *
	 * @hooked wp
	 *
	 * phpcs:disable WordPress.Security.NonceVerification.Recommended
	 */
	public function handle_ngl_wp_mail_unsubscribe(): void {

		if ( ! isset( $_GET['unsubscribe'] ) ) {
			return;
		}

		/** @var string $unsubscribe_param The sanitized $_GET['unsubscribe'] parameter. */
		$unsubscribe_param = preg_replace( '/[^\w|]*/', '', wp_unslash( $_GET['unsubscribe'] ) );

		if ( 1 !== preg_match( '/^(\d+)\|(\w{8})$/', $unsubscribe_param, $output_array ) ) {
			$this->logger->info( 'Bad unsubscribe parameter passed: ' . $unsubscribe_param, array( 'unsubscribe_param' => $unsubscribe_param ) );
			return;
		}

		$user_id          = intval( $output_array[1] );
		$unsubscribe_hash = $output_array[2];

		$wp_user = get_user_by( 'id', $user_id );

		if ( false === $wp_user ) {
			/**
			 *
			 * @var WP $wp
			 */
			global $wp;
			$this->logger->info(
				'No user found when trying to unsubscribe userid:' . $user_id,
				array(
					'user_id'          => $user_id,
					'unsubscribe_hash' => $unsubscribe_hash,
					'request'          => $wp->request,
				)
			);
			return;
		}

		global $post;
		if ( empty( $post ) ) {
			global $wp;
			// TODO: Not on a post at all, shouldn't be able to reach here.
			$this->logger->info(
				'Reached an unsubscribe point without being on a page.',
				array(
					'unsubscribe'      => $unsubscribe_param,
					'user_id'          => $user_id,
					'unsubscribe_hash' => $unsubscribe_hash,
					'request'          => $wp->request,
				)
			);
			return;
		}
		$post_id = $post->ID;

		// TODO: Delete this after e2e testing.
		// $post_body     = file_get_contents( 'php://input' );
		// $expected_body = 'List-Unsubscribe=One-Click';
		// if ( $_SERVER['REQUEST_METHOD'] === 'POST' && $post_body === $expected_body ) {

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['List-Unsubscribe'] ) && 'One-Click' === $_POST['List-Unsubscribe'] ) {
			// This is a request from a mail client using the one-click standard.

			$should_unsubscribe = $this->verify_hash( $unsubscribe_hash, $wp_user, $post_id );

			if ( $should_unsubscribe ) {
				$wp_user->add_role( User::UNSUBSCRIBED_ROLE );

				$unsubscribe_confirm_page_id = get_option( self::UNSUBSCRIBED_PAGE_ID_OPTION_NAME );

				$unsubscribe_confirm_url = get_post_permalink( $unsubscribe_confirm_page_id );

				if ( is_wp_error( $unsubscribe_confirm_url ) ) {
					$unsubscribe_confirm_url = get_home_url();
				}

				// TODO: Add info to the URL. Maybe link to the post. Certainly try to prefill the subscribe form.

				wp_safe_redirect( $unsubscribe_confirm_url );

			}
		} else {

			// This is a user who has clicked a link and landed on the site.

			$script_handle = 'bh-wp-ngl-wp-mail-unsubscribe';
			$version       = defined( 'BH_WP_NGL_WP_MAIL_VERSION' ) ? BH_WP_NGL_WP_MAIL_VERSION : '1.0.0';

			wp_enqueue_script( $script_handle, plugin_dir_url( __FILE__ ) . 'js/unsubscribe.js', array( 'jquery' ), $version, true );

			$json_data = wp_json_encode(
				array(
					'ajaxurl'          => newsletterglue_get_ajax_url(),
					'nonce'            => wp_create_nonce( $script_handle ),
					'user_id'          => $user_id,
					'unsubscribe_hash' => $unsubscribe_hash,
					'post_id'          => $post_id,
				)
			);

			$script = <<<EOD
var bh_wp_ngl_wp_mail_unsubscribe = $json_data;
EOD;

			wp_add_inline_script( $script_handle, $script, 'before' );
		}

	}

	/**
	 * Given a post ID and a user, return a URL with the unsubscribe parameter.
	 *
	 * @param WP_User $user The user the unsubscribe URL is for.
	 * @param int     $post_id The post id the URL is for.
	 *
	 * @return string
	 */
	public function get_unsubscribe_link( WP_User $user, int $post_id ): string {
		return add_query_arg( 'unsubscribe', $user->ID . '|' . $this->get_unsubscribe_key_for_user( $user, $post_id ), get_post_permalink( $post_id ) );
	}

	/**
	 * Given a user and a post id, generate a hash unique to the user and the post that can later be verified.
	 *
	 * TODO: Use a wp-config auth salt to make this unique per site.
	 *
	 * @param WP_User $user The user the code is for.
	 * @param int     $post_id The post the code is for.
	 *
	 * @return string
	 */
	public function get_unsubscribe_key_for_user( WP_User $user, int $post_id ): string {

		// A value unique to the user.
		$user_registered = $user->user_registered;

		// Subtract the post id so it is unique to the post.
		$number = intval( preg_replace( '/[^\d]/', '', $user_registered ) ) - $post_id;

		// Return the beginning of the hash.
		return substr( md5( "{$number}" ), 0, 8 );
	}

	/**
	 * Given an unsubscribe code from a URL, check is it valid for the expected user and post.
	 *
	 * @param string  $provided_hash The code provided in the URL.
	 * @param WP_User $user The user being verified against.
	 * @param int     $post_id The post being verified against.
	 *
	 * @return bool
	 */
	public function verify_hash( string $provided_hash, WP_User $user, int $post_id ): bool {

		$expected_hash = $this->get_unsubscribe_key_for_user( $user, $post_id );

		return hash_equals( $expected_hash, $provided_hash );
	}

	/**
	 * An AJAX endpoint for unsubscribing the user when they follow the unsubscribe link to the site.
	 *
	 * Responds with a redirect URL to the unsubscribe confirmation page.
	 *
	 * @hooked wp_ajax_bh_ngl_handle_unsubscribe
	 */
	public function ajax_handle_unsubscribe(): void {
		$script_handle = 'bh-wp-ngl-wp-mail-unsubscribe';

		if ( ! check_ajax_referer( $script_handle, '_ajax_nonce-add-meta', false ) ) {
			wp_send_json_error( array( 'message' => 'Bad nonce.' ), 400 );
		}

		if ( ! isset( $_POST['user_id'], $_POST['post_id'], $_POST['unsubscribe_hash'] ) ) {
			wp_send_json_error( array( 'message' => 'Missing parameter.' ), 400 );
		}

		$user_id          = intval( $_POST['user_id'] );
		$post_id          = intval( $_POST['post_id'] );
		$unsubscribe_hash = sanitize_key( wp_unslash( $_POST['unsubscribe_hash'] ) ) ?? '';

		// TODO: compare user unsubscribing with `wp_get_current_user();`?
		$wp_user = get_user_by( 'ID', $user_id );

		if ( empty( $wp_user ) || 1 === $wp_user->ID ) {
			wp_send_json_error( array( 'message' => 'Bad user id.' ), 400 );
		}

		$should_unsubscribe = $this->verify_hash( $unsubscribe_hash, $wp_user, $post_id );

		if ( ! $should_unsubscribe ) {
			wp_send_json_error( array( 'message' => 'Hash does not match user.' ), 400 );
		}

		 $user_caps = $wp_user->get_role_caps();
		if ( isset( $user_caps[ User::UNSUBSCRIBED_ROLE ] ) && $user_caps[ User::UNSUBSCRIBED_ROLE ] ) {
			wp_send_json( array( 'message' => 'User already unsubscribed.' ), 200 );
		}

		$wp_user->add_role( User::UNSUBSCRIBED_ROLE );

		$unsubscribe_confirm_page_id = get_option( self::UNSUBSCRIBED_PAGE_ID_OPTION_NAME );

		$unsubscribe_confirm_url = ! empty( $unsubscribe_confirm_page_id ) ? get_post_permalink( $unsubscribe_confirm_page_id ) : get_home_url();

		$response = array(
			'message'     => 'User unsubscribed.',
			'redirect_to' => $unsubscribe_confirm_url,
		);

		wp_send_json_success( $response );

	}
}
