<?php

namespace BrianHenryIE\WP_NGL_WP_Mail\Newsletter_Glue;

use WP_Post;

class Content {


	/**
	 *
	 * e.g. "{{ ng.header }}<p>Some content</p>{{ ng.footer }}"
	 *
	 * This filter is generally used by integrations to change '{{ unsubscribe_link }}' to the syntax that the ESP uses
	 * for the unsubscribe_link, e.g. Klaviyo uses the slightly different '{% unsubscribe_link %}'.
	 *
	 * @used-by  newsletterglue_generate_content()
	 *
	 * @hooked newsletterglue_email_content_{app_id_value}
	 * @hooked newsletterglue_email_content_wp_mail
	 */
	function filter_newsletterglue_email_content_wp_mail( string $content, WP_Post $post, string $subject ): string {
		return $content;
	}


}
