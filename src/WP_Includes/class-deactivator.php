<?php
/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    brianhenryie/bh-wp-ngl-wp-mail
 */

namespace BrianHenryIE\WP_NGL_WP_Mail\WP_Includes;

class Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate(): void {

		remove_role( User::UNSUBSCRIBED_ROLE );
	}

}
