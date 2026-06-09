<?php
/**
 * Shortcode [mrz_display_post_exp id="X"].
 */

namespace MrzDisplayPostExp;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Shortcode {

	public function register() {
		add_shortcode( 'mrz_display_post_exp', array( $this, 'render' ) );
	}

	public function render( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'                 => 0,
				'filter_taxonomy'    => '',
				'filter_term'        => '',
				'hide_forced_filter' => 'false',
			),
			$atts,
			'mrz_display_post_exp'
		);

		$list_id = absint( $atts['id'] );
		if ( $list_id <= 0 || get_post_type( $list_id ) !== MRZ_DISPLAY_POST_EXP_CPT ) {
			return '';
		}

		$data = DataProvider::get_list_data( $list_id );
		if ( null === $data ) {
			return '';
		}

		// Filtre forcé par shortcode.
		$forced_tax  = sanitize_key( $atts['filter_taxonomy'] );
		$forced_term = absint( $atts['filter_term'] );
		$hide_forced = in_array( strtolower( (string) $atts['hide_forced_filter'] ), array( 'true', '1', 'yes' ), true );

		$public_tax = array_keys( get_taxonomies( array( 'public' => true ), 'names' ) );
		if ( '' !== $forced_tax && in_array( $forced_tax, $public_tax, true ) && $forced_term > 0 ) {
			$data['items'] = array_values(
				array_filter(
					$data['items'],
					function ( $p ) use ( $forced_tax, $forced_term ) {
						return isset( $p['terms'][ $forced_tax ] )
							&& in_array( $forced_term, $p['terms'][ $forced_tax ], true );
					}
				)
			);
			$data['forced'] = array(
				'taxonomy' => $forced_tax,
				'term'     => $forced_term,
				'hide'     => $hide_forced,
			);
		}

		$assets = new Assets();
		$assets->enqueue_for_shortcode();

		$uid = 'mrz-display-post-exp-' . $list_id . '-' . wp_generate_uuid4();

		ob_start();
		include MRZ_DISPLAY_POST_EXP_DIR . 'public/views/list-wrapper.php';
		return ob_get_clean();
	}
}
