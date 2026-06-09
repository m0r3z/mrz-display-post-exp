<?php
/**
 * Nettoyage à la désinstallation du plugin.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Supprime tous les posts du CPT et leurs métas associées.
$cpt      = 'mrz_dpe_list';
$list_ids = $wpdb->get_col(
	$wpdb->prepare(
		"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s",
		$cpt
	)
);

if ( ! empty( $list_ids ) ) {
	foreach ( $list_ids as $list_id ) {
		wp_delete_post( (int) $list_id, true );
	}
}

// Supprime les post_meta orphelines éventuelles (_mrz_display_post_exp_*).
$meta_like = $wpdb->esc_like( '_mrz_display_post_exp_' ) . '%';
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
		$meta_like
	)
);

// Supprime les transients de cache.
$transient_like         = $wpdb->esc_like( '_transient_mrz_display_post_exp_' ) . '%';
$transient_timeout_like = $wpdb->esc_like( '_transient_timeout_mrz_display_post_exp_' ) . '%';
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
		$transient_like,
		$transient_timeout_like
	)
);
