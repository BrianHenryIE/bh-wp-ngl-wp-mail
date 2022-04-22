<?php
/**
 *
 *
 * @see NGL_Abstract_Integration
 *
 * @package brianhenryie/bh-wp-ngl-wp-mail
 */

namespace BrianHenryIE\WP_NGL_WP_Mail\Newsletter_Glue;

use BrianHenryIE\WP_NGL_WP_Mail\Frontend\Unsubscribe;
use BrianHenryIE\WP_NGL_WP_Mail\WP_Includes\User;
use NGL_Abstract_Integration;
use Psr\Log\NullLogger;
use WP_User;

/**
 * @see newsletterglue_get_supported_apps()
 * @see filter newsletterglue_get_supported_apps
 */
class WP_Mail_Integration extends NGL_Abstract_Integration {

	public $app = 'wp_mail';

	public function __construct() {

	}

	/**
	 * Called with the integration api key etc to validate.
	 *
	 * @used-by NGL_REST_API_Verify_API::response()
	 * @used-by NGL_REST_API_Verify_Connection::response()
	 * @used-by newsletterglue_ajax_connect_api()
	 *
	 * @param array{api_key:string, api_url:string, api_secret:string} $args
	 *
	 * @return array{response:string} "successful"|"invalid".
	 */
	public function add_integration( $args = array() ) {

		// All other implementations call `$this->save_integration()`.

		/**
		 * This seems to be the same as what is passed as $args.
		 *
		 * @var array{api_key:string, api_url:string, api_secret:string} $connection_args
		 */
		$connection_args = $this->get_connection_args( $args );

		// Our BS API key.
		$api_key = $args['api_key'];

		$account = array(
			'from_email' => '',
			'from_name'  => '',
			'api_key'    => $api_key,
		);

		if ( ! $this->already_integrated( $this->app, $api_key ) ) {
			$this->save_integration( $api_key );
		}

		$result = array( 'response' => 'successful' );

		update_option( 'newsletterglue_wp_mail', $account );

		return $result;
	}

	/**
	 *
	 * @return void
	 */
	protected function save_integration( $api_key ) {

		// This seems to be intentional... maybe only one can exist at a time?
		delete_option( 'newsletterglue_integrations' );

		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ $this->app ]            = array();
		$integrations[ $this->app ]['api_key'] = $api_key;

		$integrations[ $this->app ]['connection_name'] = newsletterglue_get_name( $this->app );

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );

		$options = array(
			'from_name'  => newsletterglue_get_default_from_name(),
			'from_email' => isset( $account['email'] ) ? $account['email'] : '',
		);

		foreach ( $options as $key => $value ) {
			$globals[ $this->app ][ $key ] = $globals[ $this->app ][ $key ] ?? $value;
		}

		update_option( 'newsletterglue_options', $globals );
	}

	/**
	 * The lists|groups|segments|audiences|brand and their settings.
	 *
	 * Mainly, how to populate the list of lists of users to send to.
	 * In this case, we're using WordPress roles, multiple can be selected to send to, and multiple
	 * can be selected to exclude.
	 *
	 * @see NGL_Abstract_Integration::option_array()
	 *
	 * @used-by NGL_REST_API_Verify_Connection::response()
	 * @used-by NGL_REST_API_Get_ESP_Options::response()
	 * @used-by NGL_REST_API_Get_Settings::get_esp_options()
	 *
	 * @return array<string, array{type:string, title:string, help:string, is_multi?:bool, callback?:string, param?:string onchange?:string}>
	 */
	public function option_array() {
		return array(
			'lists'        => array(
				'type'     => 'select',
				'callback' => 'get_roles',
				'title'    => __( 'Roles', 'bh-ngl-wp-mail' ),
				'help'     => __( 'Which users receive your email.', 'bh-ngl-wp-mail' ),
				'is_multi' => true,
			),
			'unsub_groups' => array(
				'type'     => 'select',
				'callback' => 'get_roles',
				'title'    => __( 'Unsubscribed/Excluded', 'bh-ngl-wp-mail' ),
				'help'     => __( 'Which users DO NOT receive your email.', 'bh-ngl-wp-mail' ),
				'is_multi' => true,
			),
		);
	}

	/**
	 * Maybe wp_roles isn't the best idea. It'll be fine on small sites.
	 *
	 * @return array<string, string> id,name.
	 */
	public function get_roles(): array {
		$roles    = array();
		$wp_roles = wp_roles();
		// TODO: Add user count: https://wordpress.stackexchange.com/a/219715/129606
		foreach ( $wp_roles->role_objects as $role_id => $role_object ) {
			$roles[ $role_id ] = $role_object->name;
		}
		return $roles;
	}

	/**
	 * Where does this print?
	 *
	 * @param array<string, array{api_key?:string} $integrations
	 * @see settings-connect-ui.php:38
	 * @return void ECHO
	 */
	public function get_connect_settings( $integrations = array() ) {

		$a = $integrations;

		echo 'get_connect_settings function';
	}


	/**
	 * Send the newsletter!
	 *
	 * Although the function has default values, it is never invoked without supplied values.
	 *
	 * The $data array was saved into the post's `_newsletterglue` meta key.
	 *
	 * @used-by newsletterglue_send()
	 *
	 * @param int   $post_id The post to send.
	 * @param array $data
	 * @param bool  $test Is this a test/preview email while in draft?
	 *
	 * @return array{status:"error"|"draft"|"sent", success?:string, fail?:string}
	 */
	public function send_newsletter( $post_id = 0, $data = array(), $test = false ) {

		$post = get_post( $post_id );

		$subject    = isset( $data['subject'] ) ? ngl_safe_title( $data['subject'] ) : ngl_safe_title( $post->post_title );
		$from_name  = isset( $data['from_name'] ) ? $data['from_name'] : newsletterglue_get_default_from_name();
		$from_email = isset( $data['from_email'] ) ? $data['from_email'] : $this->get_current_user_email();
		$lists      = isset( $data['lists'] ) && ! empty( $data['lists'] ) && is_array( $data['lists'] ) ? $data['lists'] : '';
		$schedule   = isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';

		/**
		 * Set the content type header to "text/html".
		 * This may be redundant, since we set it in the headers below.
		 *
		 * @used-by wm_mail()
		 *
		 * @see NGL_Abstract_Integration::wp_mail_content_type()
		 */
		add_filter( 'wp_mail_content_type', array( $this, 'wp_mail_content_type' ) );

		if ( $test ) {

			$test_email_recipient_address = $data['test_email'];

			if ( ! is_email( $test_email_recipient_address ) ) {
				$response['fail'] = __( 'Please enter a valid email', 'newsletter-glue' );
				return $response;
			}

			$wp_user = get_user_by( 'email', $test_email_recipient_address );

			if ( empty( $wp_user ) ) {
				$wp_user             = new class() extends WP_User {};
				$wp_user->user_email = $test_email_recipient_address;
			}
			$recipient_list = array( $test_email_recipient_address => $wp_user );

			$subject = sprintf( __( '[Test] %s', 'newsletter-glue' ), $subject );

		} else {

			/** @var string $include */
			$include = newsletterglue_get_option( 'lists', 'wp_mail' );
			$exclude = newsletterglue_get_option( 'unsub_groups', 'wp_mail' );

			$wp_users = get_users(
				array(
					'role__in'     => explode( ',', $include ),
					'role__not_in' => explode( ',', $exclude ),
				)
			);

			/** @var array<string, WP_User> $recipient_list */
			$recipient_list = array_combine(
				array_map(
					function( WP_User $user ) {
						return $user->user_email;
					},
					$wp_users
				),
				$wp_users
			);
		}

		$body = newsletterglue_generate_content( $post, $subject, 'wp_mail' );

		$headers = array(
			'from'         => "$from_name <$from_email>",
			'reply-to'     => $from_email,
			'content-type' => 'text/html',
		);

		// wp_mail() parses the headers as an array of strings of colon separated name:values, it does not use the PHP array keys.
		$headers = array_map(
			function( $key, $value ) {
				return "{$key}:{$value}";
			},
			array_keys( $headers ),
			$headers
		);

		$headers_array_object = new \ArrayObject( $headers );

		$unsubscribe = new Unsubscribe( new NullLogger() );

		$result = false;

		foreach ( $recipient_list as $recipient_email_address => $wp_user ) {

			$headers_copy = $headers_array_object->getArrayCopy();
			$body_copy    = $body;

			$unsubscribe_link = $unsubscribe->get_unsubscribe_link( $wp_user, $post_id );

			$headers_copy[] = "List-Unsubscribe: <{$unsubscribe_link}>";
			$headers_copy[] = 'List-Unsubscribe-Post: List-Unsubscribe=One-Click';

			$body_copy = str_replace( '{{ unsubscribe_link }}', $unsubscribe_link, $body_copy );

			/**
			 * @var bool $result
			 */
			$result = wp_mail( $recipient_email_address, $subject, $body_copy, $headers_copy );

		}

		// TODO: This is just using the result for the last user.
		if ( $result ) {
			$response['success'] = 'success'; // $this->get_test_success_msg();
		} else {
			$response['fail'] = $this->nothing_to_send();
		}

		return $response;
	}

	/**
	 * Show/hide the warning message about test emails being sent by `wp_mail()`.
	 *
	 * Return false to hide the warning notice, since sending by WordPress is not something we need to warn _against_.
	 *
	 * When this is true, a warning is shown beside "Send test email":
	 * "This test email is sent by WordPress. Formatting and deliverability might differ slightly from email campaigns sent by wp_mail()."
	 *
	 * @see NGL_Abstract_Integration::test_email_by_wordpress()
	 * @see wp-content/plugins/newsletter-glue-pro/includes/admin/metabox/views/send-test.php
	 * Return true if test emails are sent by WordPress.
	 * @return bool
	 */
	public function test_email_by_wordpress() {
		return false;
	}

	/**
	 * Subscribe the user from email submitted through the form block.
	 *
	 * When the subscribe form is used, if the email address exists for a user, remove any "unsubscribed" role there
	 * might be, otherwise create a new user for that email address.
	 *
	 * TODO: add an "unconfirmed" role to the new user, and have a link in the email that removes the role.
	 *
	 * @used-by NGL_Block_Form::subscribe()
	 *
	 * @param array{email:string, double_optin:string} $data
	 *
	 * @return int 0 for failure, >0 for success.
	 */
	public function add_user( $data ) {

		$email_address = $data['email'];

		$wp_user = get_user_by( 'email', $email_address );

		if ( $wp_user instanceof WP_User ) {
			$wp_user->remove_role( User::UNSUBSCRIBED_ROLE );
			return 1;
		}

		$base_username   = explode( '@', $email_address )[0];
		$username_suffix = 0;
		$password        = wp_generate_password( 24 );
		do {
			$username = $base_username;
			if ( $username_suffix > 0 ) {
				$username = "{$username}-{$username_suffix}";
			}
			// It appears this does not send the new user an email.
			$result = wp_create_user( $username, $password, $email_address );
			$username_suffix++;
		} while ( is_wp_error( $result ) );

		return 1;
	}
}
