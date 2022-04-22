/**
 * JavaScript is used for unsubscribing to avoid spam check / robots following links in emails and accidentally
 * unsubscribing the user.
 */

(function( $ ) {
    'use strict';

    $(function() {

        var ajaxurl = bh_wp_ngl_wp_mail_unsubscribe.ajaxurl;

        var data = {
            'action': 'bh_ngl_handle_unsubscribe',
            '_ajax_nonce': bh_wp_ngl_wp_mail_unsubscribe.nonce,
            'post_id': bh_wp_ngl_wp_mail_unsubscribe.post_id,
            'unsubscribe_hash': bh_wp_ngl_wp_mail_unsubscribe.unsubscribe_hash,
            'user_id': bh_wp_ngl_wp_mail_unsubscribe.user_id
        };

        jQuery.post(ajaxurl, data, function(response) {
            window.location = response.data.redirect_to;
        });

    });

})( jQuery );
