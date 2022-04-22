<?php
/**
 * Define the role we will use for unsubscribed users.
 *
 * @package brianhenryie/bh-wp-ngl-wp-mail
 */

namespace BrianHenryIE\WP_NGL_WP_Mail\WP_Includes;

/**
 * Not named WP_User to avoid inconveniencing intellisense.
 */
class User {

	const UNSUBSCRIBED_ROLE = 'bh_ngl_unsubscribed';

	// TODO: add to users.php subscribe/unsubscribe checkbox
}
