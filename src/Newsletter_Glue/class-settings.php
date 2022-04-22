<?php

namespace BrianHenryIE\WP_NGL_WP_Mail\Newsletter_Glue;

class Settings {

	/**
	 * Foukk, doesn't do what I expected.
	 *
	 * @param bool   $approve
	 * @param string $app
	 *
	 * @return bool
	 * @see newsletter-glue-pro/includes/admin/settings/views/settings-connect-card.php
	 */
	public function filter_newsletterglue_should_allow_connection_edit_wp_mail( bool $approve, string $app ): bool {
		if ( 'wp_mail' === $app ) {
			$approve = false;
		}

		return $approve;
	}

}
