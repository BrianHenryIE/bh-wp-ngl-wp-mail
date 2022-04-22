<?php //phpcs:disable WordPress.Files.FileName.InvalidClassFileName
/**
 *
 *
 * @see NGL_REST_API_Verify_Connection::response()
 *
 * Must be in a file named `init.php` due to how NGL includes:
 * `include_once newsletterglue_get_path( $esp ) . '/init.php';`
 * Must be in the global namespace, with this classname, due to how NGL instantiates:
 * `$classname  = 'NGL_' . ucfirst( $integration_app_name ); new $classname`
 *
 * @package brianhenryie/bh-wp-ngl-wp-mail
 *
 * phpcs:disable PEAR.NamingConventions.ValidClassName.Invalid
 */

use BrianHenryIE\WP_NGL_WP_Mail\Newsletter_Glue\WP_Mail_Integration;

class NGL_Wp_mail extends WP_Mail_Integration {}
