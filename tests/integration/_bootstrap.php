<?php
/**
 * Runs after WordPress has been initialised (after plugins are loaded) and before tests are run.
 *
 * @package           BH_WP_NGL_WP_Mail
 */

add_filter(
	'pre_option_siteurl',
	function(): string {
		return 'http://localhost:8080/bh-wp-ngl-wp-mail';
	}
);
add_filter(
	'pre_option_home',
	function(): string {
		return 'http://localhost:8080/bh-wp-ngl-wp-mail';
	}
);
