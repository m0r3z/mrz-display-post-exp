<?php
/**
 * Actions exécutées à l'activation du plugin.
 */

namespace MrzDisplayPostExp;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Activator {

	public static function activate() {
		if ( ! mrz_display_post_exp_has_acf() ) {
			deactivate_plugins( MRZ_DISPLAY_POST_EXP_BASENAME );
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
