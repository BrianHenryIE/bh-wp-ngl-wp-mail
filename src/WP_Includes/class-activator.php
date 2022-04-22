<?php
/**
 * Fired during plugin activation
 *
 * Add the `unsubscribed` role to WordPress.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    brianhenryie/bh-wp-ngl-wp-mail
 */

namespace BrianHenryIE\WP_NGL_WP_Mail\WP_Includes;

class Activator {

	/**
	 * add_unsubscribed_role_to_wordpress
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate(): void {

		add_role( User::UNSUBSCRIBED_ROLE, 'NLG Unsubscribed' );

	}

}
