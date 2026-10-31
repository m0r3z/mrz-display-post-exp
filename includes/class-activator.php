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
				esc_html__( 'MRZ Display Post Exp requires Advanced Custom Fields (Pro recommended). Please install and activate ACF before activating this plugin.', 'mrz-display-post-exp' ),
				esc_html__( 'Missing dependency', 'mrz-display-post-exp' ),
				array( 'back_link' => true )
			);
		}

		( new CPT() )->register_post_type();
		flush_rewrite_rules();
	}
}
