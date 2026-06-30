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
						// has_term() vérifie directement sur le post : fonctionne même si la
						// taxonomie n'est pas activée dans les filtres du bloc.
						return has_term( $forced_term, $forced_tax, (int) $p['id'] );
					}
				)
			);
			$data['forced'] = array(
				'taxonomy' => $forced_tax,
				'term'     => $forced_term,
				'hide'     => $hide_forced,
			);
		}

		// Restriction à la catégorie du contexte courant (single ou archive).
		// Contextuel → appliqué au rendu, après le cache partagé du bloc.
		if ( ! empty( $data['config']['restrictCurrentTerm'] ) ) {
			$ctx_tax = sanitize_key( $data['config']['currentTermTaxonomy'] );
			if ( '' !== $ctx_tax && taxonomy_exists( $ctx_tax ) ) {
				$context_terms   = array();
				$current_post_id = 0;
				if ( is_singular() ) {
					$current_post_id = (int) get_queried_object_id();
					$terms           = get_the_terms( $current_post_id, $ctx_tax );
					if ( $terms && ! is_wp_error( $terms ) ) {
						$context_terms = wp_list_pluck( $terms, 'term_id' );
					}
				} elseif ( is_tax( $ctx_tax ) || ( 'category' === $ctx_tax && is_category() ) ) {
					$obj = get_queried_object();
					if ( $obj && isset( $obj->term_id ) ) {
						$context_terms = array( (int) $obj->term_id );
					}
				}

				if ( ! empty( $context_terms ) ) {
					$context_terms = array_map( 'intval', $context_terms );
					$data['items'] = array_values(
						array_filter(
							$data['items'],
							function ( $p ) use ( $context_terms, $ctx_tax, $current_post_id ) {
								if ( $current_post_id && (int) $p['id'] === $current_post_id ) {
									return false; // exclure l'élément courant (single)
								}
								return has_term( $context_terms, $ctx_tax, (int) $p['id'] );
							}
						)
					);
				}
			}
		}

		$assets = new Assets();
		$assets->enqueue_for_shortcode();

		$uid = 'mrz-display-post-exp-' . $list_id . '-' . wp_generate_uuid4();

		ob_start();
		include MRZ_DISPLAY_POST_EXP_DIR . 'public/views/list-wrapper.php';
		return ob_get_clean();
	}
}
