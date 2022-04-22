<?php
/**
 * Register the integration with Newsletter Glue
 *
 * @package brianhenryie/bh-wp-ngl-wp-mail
 */

namespace BrianHenryIE\WP_NGL_WP_Mail\Newsletter_Glue;

class Register {

	/**
	 * Defines the basics of the integration: the id ("value") and title ("label"), plus some optional settings.
	 *
	 * @hooked newsletterglue_get_esp_list
	 * @see NGL_REST_API_Get_Settings::get_esp_list()
	 *
	 * @param array{value:string, label:string, bg:string, help:string, extra_setting?:string, requires?:string} $list The existing list of integrations.
	 * "bg" is a CSS RGB color, "help" is a URL
	 *
	 * @return array{value:string, label:string, bg:string, help:string, extra_setting?:string, requires?:string}
	 */
	public function add_to_email_service_provider_list( array $list ): array {

		// TODO: how to remove requirement for API key an API URL settings?
		$list[] = array(
			'value' => 'wp_mail',
			'label' => 'wp_mail()',
			'bg'    => '#FFF',
		// 'extra_setting' => 'url',
		);

		return $list;
	}


	/**
	 * Second filter hit.
	 *
	 * @hooked newsletterglue_add_wp_mail_supported_app
	 * @see newsletterglue_get_supported_apps()
	 *
	 * @param array<string, string> $apps
	 *
	 * @return array<string, string>
	 */
	public function add_as_supported_app( $apps ): array {

		$apps['wp_mail'] = 'wp_mail()';

		return $apps;
	}


	/**
	 * Third filter hit
	 *
	 * The base URL for the frontend assets of the integration.
	 * i.e. "{WP_CONTENT_URL/plugins/$path}/assets/icon.png" for the integration icon.
	 *
	 * @hooked newsletterglue_get_url
	 * @see newsletterglue_get_url()
	 *
	 * @param string $path The URL to the integration.
	 * @param string $app The value-id of the integration.
	 *
	 * @return string
	 */
	function get_url( string $path, string $app ): string {

		if ( 'wp_mail' === $app ) {
			// Returns the URL for the current directory.
			$integration_url = plugins_url( __DIR__ );
			// But we're using a sibling directory for the base (i.e. the non-class files expected by Newsletter Glue).
			$integration_url = str_replace( 'Newsletter_Glue', 'NGL_Integration', $integration_url );
			return $integration_url;
		}

		return $path;
	}


	/**
	 * Fourth filter hit.
	 *
	 * Fetching the path to instantiate the integration.
	 *
	 * @hooked newsletterglue_get_path
	 * @used-by newsletterglue_get_path()
	 *
	 * @param string $path
	 * @param string $app
	 *
	 * @return string
	 */
	public function get_path( string $path, string $app ): string {

		if ( 'wp_mail' === $app ) {
			return BH_WP_NGL_WP_MAIL_PLUGIN_DIR . '/NGL_Integration';
		}

		return $path;
	}

}
