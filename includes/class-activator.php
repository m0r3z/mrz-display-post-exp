<?php
/**
 * Actions exécutées à l'activation du plugin.
 */

namespace Mrzdpe;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Activator {

	public static function activate() {
		if ( ! mrzdpe_has_acf() ) {
			deactivate_plugins( MRZDPE_BASENAME );
			wp_die(
				esc_html__( 'MRZ Display Post nécessite Advanced Custom Fields (Pro recommandé). Veuillez installer et activer ACF avant d\'activer ce plugin.', 'mrz-display-post-exp' ),
				esc_html__( 'Dépendance manquante', 'mrz-display-post-exp' ),
				array( 'back_link' => true )
			);
		}

		( new CPT() )->register_post_type();
		flush_rewrite_rules();
	}
}
