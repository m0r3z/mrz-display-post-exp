<?php
/**
 * Agrège les données d'un bloc d'affichage pour le rendu front.
 */

namespace MrzDisplayPostExp;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class DataProvider {

	const CACHE_PREFIX = 'mrz_display_post_exp_';

	public static function invalidate( $list_id ) {
		delete_transient( self::CACHE_PREFIX . (int) $list_id );
	}

	public static function get_list_data( $list_id ) {
		$list_id = (int) $list_id;
		if ( $list_id <= 0 || get_post_type( $list_id ) !== MRZ_DISPLAY_POST_EXP_CPT ) {
			return null;
		}

		$cache_key = self::CACHE_PREFIX . $list_id;
		$cached    = get_transient( $cache_key );
		if ( false !== $cached && is_array( $cached ) ) {
			return $cached;
		}

		$values = ListConfig::get_values( $list_id );

		$data = array(
			'id'      => $list_id,
			'config'  => self::build_config( $values ),
			'filters' => array(),
			'items'   => array(),
		);

		$items = self::build_items( $values );

		$data['items']   = $items;
		$data['filters'] = self::build_filters( $values, $items );

		$ttl = (int) apply_filters( 'mrz_display_post_exp_cache_ttl', 5 * MINUTE_IN_SECONDS );
		if ( $ttl > 0 ) {
			set_transient( $cache_key, $data, $ttl );
		}

		return $data;
	}

	private static function build_config( $values ) {
		$clear_btn_text = '' !== (string) $values['clear_btn_text']
			? (string) $values['clear_btn_text']
			: __( 'Effacer', 'mrz-display-post-exp' );

		$search_fields = array_filter( array_map( 'trim', explode( ',', (string) $values['search_acf_fields'] ) ) );

		return array(
			'format'           => (string) $values['format'],
			'layoutFilters'    => (string) $values['layout_filters'],
			'itemClickAction'  => (string) $values['item_click_action'],
			'showClearBtn'     => ! empty( $values['show_clear_btn'] ),
			'clearBtnText'     => $clear_btn_text,
			'showFilterCounts' => ! empty( $values['show_filter_counts'] ),
			'urlFilters'       => ! empty( $values['url_filters_enabled'] ),
			'perPage'          => (int) $values['per_page'],
			'sourcePt'         => (string) $values['source_pt'],
			'search'           => array(
				'enabled'     => ! empty( $values['search_enabled'] ),
				'label'       => '' !== (string) $values['search_label']
					? (string) $values['search_label']
					: __( 'Rechercher', 'mrz-display-post-exp' ),
				'placeholder' => '' !== (string) $values['search_placeholder']
					? (string) $values['search_placeholder']
					: __( 'Rechercher…', 'mrz-display-post-exp' ),
				'layout'      => (string) $values['search_layout'],
				'acfFields'   => array_values( $search_fields ),
			),
			'grid'             => array(
				'minWidth' => (int) $values['grid_min_width'],
			),
			'slider'           => array(
				'perView'       => (int) $values['slider_per_view'],
				'perViewTablet' => (int) $values['slider_per_view_tablet'],
				'perViewMobile' => (int) $values['slider_per_view_mobile'],
				'gap'           => (int) $values['slider_gap'],
				'autoplay'      => ! empty( $values['slider_autoplay'] ),
				'autoplayDelay' => (int) $values['slider_autoplay_delay'],
				'loop'          => ! empty( $values['slider_loop'] ),
				'showArrows'    => ! empty( $values['slider_show_arrows'] ),
				'showDots'      => ! empty( $values['slider_show_dots'] ),
			),
			'taxonomies'       => array_values( (array) $values['taxonomies'] ),
			'taxoModes'        => (array) $values['taxo_modes'],
		);
	}

	private static function build_items( $values ) {
		$source_pt   = $values['source_pt'];
		$taxonomies  = (array) $values['taxonomies'];
		$acf_filters = (array) $values['acf_filters'];
		$limit       = (int) $values['limit'];

		$search_fields = array_filter( array_map( 'trim', explode( ',', (string) $values['search_acf_fields'] ) ) );

		$args = array(
			'post_type'              => $source_pt,
			'post_status'            => 'publish',
			'posts_per_page'         => $limit > 0 ? $limit : -1,
			'orderby'                => (string) $values['orderby'],
			'order'                  => (string) $values['order'],
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
		);

		$query = new \WP_Query( $args );
		if ( ! $query->have_posts() ) {
			return array();
		}

		$items = array();
		foreach ( $query->posts as $post ) {
			$item = array(
				'id'        => $post->ID,
				'url'       => get_permalink( $post->ID ),
				'title'     => get_the_title( $post->ID ),
				'html'      => TemplateParser::render( (string) $values['tpl_item'], $post->ID ),
				'terms'     => array(),
				'acfValues' => array(),
				'searchText' => '',
			);

			// Valeurs des champs ACF utilisés comme filtres (valeur brute, non formatée).
			if ( function_exists( 'get_field' ) ) {
				foreach ( $acf_filters as $spec ) {
					$name = $spec['field'];
					if ( '' === $name ) {
						continue;
					}
					$val = get_field( $name, $post->ID, false );
					if ( null === $val || '' === $val ) {
						continue;
					}
					if ( is_array( $val ) ) {
						$flat = array();
						foreach ( $val as $v ) {
							if ( is_scalar( $v ) ) {
								$flat[] = (string) $v;
							}
						}
						$item['acfValues'][ $name ] = $flat;
					} elseif ( is_scalar( $val ) ) {
						$item['acfValues'][ $name ] = (string) $val;
					}
				}

				// Texte additionnel pour la recherche (champs ACF texte choisis).
				if ( ! empty( $search_fields ) ) {
					$search_parts = array();
					foreach ( $search_fields as $name ) {
						$sval = get_field( $name, $post->ID );
						if ( is_scalar( $sval ) && '' !== (string) $sval ) {
							$search_parts[] = (string) $sval;
						}
					}
					if ( ! empty( $search_parts ) ) {
						$item['searchText'] = wp_strip_all_tags( implode( ' ', $search_parts ) );
					}
				}
			}

			// Termes des taxonomies de filtre.
			foreach ( $taxonomies as $tax ) {
				$terms = get_the_terms( $post->ID, $tax );
				if ( empty( $terms ) || is_wp_error( $terms ) ) {
					continue;
				}
				$ids = array();
				foreach ( $terms as $term ) {
					$ids[] = (int) $term->term_id;
				}
				$item['terms'][ $tax ] = $ids;
			}

			$items[] = $item;
		}

		return $items;
	}

	private static function build_filters( $values, $items ) {
		$filters = array();

		// Filtres par taxonomie.
		$taxonomies = (array) $values['taxonomies'];
		$modes      = (array) $values['taxo_modes'];
		$tax_logics = (array) $values['taxo_logic'];
		$tax_labels = (array) $values['taxo_labels'];

		foreach ( $taxonomies as $slug ) {
			$tax_obj = get_taxonomy( $slug );
			if ( ! $tax_obj ) {
				continue;
			}

			$used_ids = array();
			foreach ( $items as $p ) {
				if ( ! empty( $p['terms'][ $slug ] ) ) {
					foreach ( $p['terms'][ $slug ] as $tid ) {
						$used_ids[ $tid ] = isset( $used_ids[ $tid ] ) ? $used_ids[ $tid ] + 1 : 1;
					}
				}
			}
			if ( empty( $used_ids ) ) {
				continue;
			}

			$terms = get_terms(
				array(
					'taxonomy'   => $slug,
					'include'    => array_keys( $used_ids ),
					'hide_empty' => false,
				)
			);
			if ( is_wp_error( $terms ) || empty( $terms ) ) {
				continue;
			}

			$options = array();
			foreach ( $terms as $term ) {
				$options[] = array(
					'id'    => (int) $term->term_id,
					'name'  => $term->name,
					'count' => isset( $used_ids[ $term->term_id ] ) ? (int) $used_ids[ $term->term_id ] : 0,
				);
			}

			$custom_label = isset( $tax_labels[ $slug ] ) ? (string) $tax_labels[ $slug ] : '';
			$filters[]    = array(
				'type'     => 'tax',
				'taxonomy' => $slug,
				'label'    => '' !== $custom_label ? $custom_label : $tax_obj->labels->singular_name,
				'mode'     => isset( $modes[ $slug ] ) ? (string) $modes[ $slug ] : 'dropdown',
				'logic'    => isset( $tax_logics[ $slug ] ) ? (string) $tax_logics[ $slug ] : 'or',
				'options'  => $options,
			);
		}

		// Filtres par champ ACF.
		$acf_filters = (array) $values['acf_filters'];
		foreach ( $acf_filters as $spec ) {
			$field = $spec['field'];
			if ( '' === $field ) {
				continue;
			}

			$counts = array();
			foreach ( $items as $p ) {
				if ( ! isset( $p['acfValues'][ $field ] ) ) {
					continue;
				}
				$v    = $p['acfValues'][ $field ];
				$vals = is_array( $v ) ? $v : array( (string) $v );
				foreach ( $vals as $single ) {
					$single = (string) $single;
					if ( '' === $single ) {
						continue;
					}
					$counts[ $single ] = isset( $counts[ $single ] ) ? $counts[ $single ] + 1 : 1;
				}
			}
			if ( empty( $counts ) ) {
				continue;
			}

			$choices = self::get_acf_choices( $field );

			$options = array();
			foreach ( $counts as $value => $count ) {
				$options[] = array(
					'id'    => (string) $value,
					'name'  => isset( $choices[ $value ] ) ? (string) $choices[ $value ] : (string) $value,
					'count' => (int) $count,
				);
			}

			usort(
				$options,
				static function ( $a, $b ) {
					return strcasecmp( $a['name'], $b['name'] );
				}
			);

			$filters[] = array(
				'type'    => 'acf',
				'field'   => $field,
				'label'   => '' !== $spec['label'] ? $spec['label'] : $field,
				'mode'    => $spec['mode'],
				'logic'   => isset( $spec['logic'] ) ? (string) $spec['logic'] : 'or',
				'options' => $options,
			);
		}

		return $filters;
	}

	/**
	 * Récupère les choix (value => label) d'un champ ACF de type select/radio/checkbox.
	 * Retourne un tableau vide si le champ n'a pas de choices définis.
	 */
	private static function get_acf_choices( $field_name ) {
		static $cache = array();
		if ( array_key_exists( $field_name, $cache ) ) {
			return $cache[ $field_name ];
		}
		$choices = array();
		if ( function_exists( 'acf_get_field' ) ) {
			$obj = acf_get_field( $field_name );
			if ( is_array( $obj ) && isset( $obj['choices'] ) && is_array( $obj['choices'] ) ) {
				foreach ( $obj['choices'] as $key => $label ) {
					$choices[ (string) $key ] = (string) $label;
				}
			}
			if ( is_array( $obj ) && isset( $obj['type'] ) && 'true_false' === $obj['type'] ) {
				$choices = array(
					'1' => isset( $obj['ui_on_text'] ) && '' !== $obj['ui_on_text'] ? (string) $obj['ui_on_text'] : __( 'Oui', 'mrz-display-post-exp' ),
					'0' => isset( $obj['ui_off_text'] ) && '' !== $obj['ui_off_text'] ? (string) $obj['ui_off_text'] : __( 'Non', 'mrz-display-post-exp' ),
				);
			}
		}
		$cache[ $field_name ] = $choices;
		return $choices;
	}
}
