<?php
/**
 * Métaboxes de configuration d'un bloc d'affichage + sauvegarde sécurisée.
 */

namespace MrzDisplayPostExp;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ListConfig {

	const NONCE_ACTION = 'mrz_display_post_exp_save';
	const NONCE_NAME   = '_mrz_display_post_exp_nonce';

	/**
	 * Définition des valeurs autorisées pour les champs à choix fermé.
	 */
	public static function layouts_filters() {
		return array( 'above', 'side-left', 'side-right' );
	}
	public static function formats() {
		return array( 'list', 'grid', 'slider' );
	}
	public static function item_click_actions() {
		return array( 'none', 'link' );
	}
	public static function search_layouts() {
		return array( 'inline', 'top' );
	}
	public static function slider_arrows_positions() {
		return array( 'sides', 'top' );
	}
	public static function taxo_modes() {
		return array( 'dropdown', 'radio', 'checkbox' );
	}
	public static function filter_logics() {
		return array( 'or', 'and' );
	}
	public static function orderbys() {
		return array( 'date', 'title', 'menu_order', 'rand', 'modified', 'acf_date' );
	}
	public static function orders() {
		return array( 'ASC', 'DESC' );
	}
	public static function acf_date_scopes() {
		return array( 'all', 'upcoming', 'past' );
	}
	public static function thumb_ratios() {
		return array( 'auto', '1/1', '4/3', '3/2', '16/9', '3/4', '2/3' );
	}

	public function register() {
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );
		add_action( 'save_post_' . MRZ_DISPLAY_POST_EXP_CPT, array( $this, 'save' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
	}

	public function add_metaboxes() {
		add_meta_box( 'mrz_display_post_exp_source', __( 'Source des données', 'mrz-display-post-exp' ), array( $this, 'render' ), MRZ_DISPLAY_POST_EXP_CPT, 'normal', 'high', array( 'view' => 'source' ) );
		add_meta_box( 'mrz_display_post_exp_templates', __( 'Template HTML', 'mrz-display-post-exp' ), array( $this, 'render' ), MRZ_DISPLAY_POST_EXP_CPT, 'normal', 'high', array( 'view' => 'templates' ) );
		add_meta_box( 'mrz_display_post_exp_filters', __( 'Filtres & recherche', 'mrz-display-post-exp' ), array( $this, 'render' ), MRZ_DISPLAY_POST_EXP_CPT, 'normal', 'high', array( 'view' => 'filters' ) );
		add_meta_box( 'mrz_display_post_exp_display', __( 'Affichage', 'mrz-display-post-exp' ), array( $this, 'render' ), MRZ_DISPLAY_POST_EXP_CPT, 'normal', 'high', array( 'view' => 'display' ) );
		add_meta_box( 'mrz_display_post_exp_shortcode', __( 'Shortcode', 'mrz-display-post-exp' ), array( $this, 'render' ), MRZ_DISPLAY_POST_EXP_CPT, 'side', 'high', array( 'view' => 'shortcode' ) );
	}

	public function render( $post, $metabox ) {
		static $nonce_printed = false;
		if ( ! $nonce_printed ) {
			wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );
			$nonce_printed = true;
		}

		$view = isset( $metabox['args']['view'] ) ? $metabox['args']['view'] : '';
		$file = MRZ_DISPLAY_POST_EXP_DIR . 'admin/views/metabox-' . $view . '.php';

		if ( ! file_exists( $file ) ) {
			return;
		}

		$values = self::get_values( $post->ID );
		include $file;
	}

	/**
	 * Charge toutes les valeurs de configuration d'un bloc, avec defaults.
	 */
	public static function get_values( $post_id ) {
		$defaults = array(
			// Source.
			'source_pt'             => 'post',
			'orderby'               => 'date',
			'orderby_acf_field'     => '',
			'acf_date_scope'        => 'all',
			'order'                 => 'DESC',
			'limit'                 => 0,
			'per_page'              => 0,
			// Filtres.
			'taxonomies'            => array(),
			'taxo_modes'            => array(),
			'taxo_logic'            => array(),
			'taxo_labels'           => array(),
			'acf_filters'           => array(),
			'show_filter_counts'    => 1,
			'url_filters_enabled'   => 0,
			// Recherche.
			'search_enabled'        => 0,
			'search_acf_fields'     => '',
			'search_label'          => '',
			'search_placeholder'    => '',
			'search_layout'         => 'inline',
			// Affichage.
			'format'                => 'list',
			'layout_filters'        => 'above',
			'item_click_action'     => 'none',
			'show_clear_btn'        => 1,
			'clear_btn_text'        => '',
			'grid_min_width'        => 240,
			'grid_gap'              => 12,
			'thumb_ratio'           => 'auto',
			// Slider.
			'slider_per_view'        => 3,
			'slider_per_view_tablet' => 2,
			'slider_per_view_mobile' => 1,
			'slider_gap'             => 16,
			'slider_speed'           => 500,
			'slider_autoplay'        => 0,
			'slider_autoplay_delay'  => 4000,
			'slider_loop'            => 0,
			'slider_show_arrows'     => 1,
			'slider_arrows_position' => 'sides',
			'slider_show_dots'       => 1,
			// Template.
			'tpl_item'              => "<div class=\"mrz-display-post-exp-item\">\n  {#if post_thumbnail}<div class=\"mrz-dpe-thumb\">{post_thumbnail}</div>{/if}\n  <div class=\"mrz-dpe-body\">\n    {#if taxonomy:category}<span class=\"mrz-dpe-cat\">{taxonomy:category:first}</span>{/if}\n    <h3 class=\"mrz-dpe-title\">{post_title}</h3>\n    {#if post_excerpt}<p class=\"mrz-dpe-excerpt\">{post_excerpt:25}</p>{/if}\n  </div>\n</div>",
		);

		$out = array();
		foreach ( $defaults as $key => $default ) {
			$stored = get_post_meta( $post_id, '_mrz_display_post_exp_' . $key, true );
			if ( '' === $stored || null === $stored ) {
				$out[ $key ] = $default;
			} elseif ( is_array( $default ) ) {
				$out[ $key ] = is_array( $stored ) ? $stored : $default;
			} elseif ( is_int( $default ) ) {
				$out[ $key ] = (int) $stored;
			} else {
				$out[ $key ] = $stored;
			}
		}

		return $out;
	}

	public function save( $post_id, $post ) {
		if ( ! isset( $_POST[ self::NONCE_NAME ] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_NAME ] ) ), self::NONCE_ACTION ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$raw = isset( $_POST['mrz_display_post_exp'] ) && is_array( $_POST['mrz_display_post_exp'] )
			? wp_unslash( $_POST['mrz_display_post_exp'] )
			: array();

		$clean = array();

		// Source.
		$public_pts         = array_keys( get_post_types( array( 'public' => true ), 'names' ) );
		$clean['source_pt'] = ( isset( $raw['source_pt'] ) && in_array( $raw['source_pt'], $public_pts, true ) )
			? $raw['source_pt']
			: 'post';
		$clean['orderby']           = ( isset( $raw['orderby'] ) && in_array( $raw['orderby'], self::orderbys(), true ) ) ? $raw['orderby'] : 'date';
		$clean['orderby_acf_field'] = isset( $raw['orderby_acf_field'] ) ? sanitize_key( $raw['orderby_acf_field'] ) : '';
		$clean['acf_date_scope']    = ( isset( $raw['acf_date_scope'] ) && in_array( $raw['acf_date_scope'], self::acf_date_scopes(), true ) ) ? $raw['acf_date_scope'] : 'all';
		$clean['order']             = ( isset( $raw['order'] ) && in_array( $raw['order'], self::orders(), true ) ) ? $raw['order'] : 'DESC';
		$clean['limit']     = isset( $raw['limit'] ) ? max( 0, absint( $raw['limit'] ) ) : 0;
		$clean['per_page']  = isset( $raw['per_page'] ) ? max( 0, min( 200, absint( $raw['per_page'] ) ) ) : 0;

		// Taxonomies de filtre.
		$all_tax = array_keys( get_taxonomies( array( 'public' => true ), 'names' ) );
		$taxo    = array();
		if ( isset( $raw['taxonomies'] ) && is_array( $raw['taxonomies'] ) ) {
			foreach ( $raw['taxonomies'] as $slug ) {
				$slug = sanitize_key( $slug );
				if ( in_array( $slug, $all_tax, true ) ) {
					$taxo[] = $slug;
				}
			}
		}
		$clean['taxonomies'] = $taxo;

		$modes = array();
		if ( isset( $raw['taxo_modes'] ) && is_array( $raw['taxo_modes'] ) ) {
			foreach ( $raw['taxo_modes'] as $slug => $mode ) {
				$slug = sanitize_key( $slug );
				if ( in_array( $slug, $taxo, true ) && in_array( $mode, self::taxo_modes(), true ) ) {
					$modes[ $slug ] = $mode;
				}
			}
		}
		$clean['taxo_modes'] = $modes;

		$logics = array();
		if ( isset( $raw['taxo_logic'] ) && is_array( $raw['taxo_logic'] ) ) {
			foreach ( $raw['taxo_logic'] as $slug => $logic ) {
				$slug = sanitize_key( $slug );
				if ( in_array( $slug, $taxo, true ) && in_array( $logic, self::filter_logics(), true ) ) {
					$logics[ $slug ] = $logic;
				}
			}
		}
		$clean['taxo_logic'] = $logics;

		$labels = array();
		if ( isset( $raw['taxo_labels'] ) && is_array( $raw['taxo_labels'] ) ) {
			foreach ( $raw['taxo_labels'] as $slug => $label ) {
				$slug = sanitize_key( $slug );
				if ( in_array( $slug, $taxo, true ) ) {
					$clean_label = sanitize_text_field( (string) $label );
					if ( '' !== $clean_label ) {
						$labels[ $slug ] = $clean_label;
					}
				}
			}
		}
		$clean['taxo_labels'] = $labels;

		// Filtres ACF.
		$acf_filters = array();
		if ( isset( $raw['acf_filters'] ) && is_array( $raw['acf_filters'] ) ) {
			foreach ( $raw['acf_filters'] as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				$field = isset( $row['field'] ) ? sanitize_key( $row['field'] ) : '';
				if ( '' === $field ) {
					continue;
				}
				$label_raw     = isset( $row['label'] ) ? (string) $row['label'] : '';
				$mode          = ( isset( $row['mode'] ) && in_array( $row['mode'], self::taxo_modes(), true ) )
					? $row['mode']
					: 'dropdown';
				$logic         = ( isset( $row['logic'] ) && in_array( $row['logic'], self::filter_logics(), true ) )
					? $row['logic']
					: 'or';
				$acf_filters[] = array(
					'field' => $field,
					'label' => sanitize_text_field( $label_raw ),
					'mode'  => $mode,
					'logic' => $logic,
				);
			}
		}
		$clean['acf_filters'] = $acf_filters;

		$clean['show_filter_counts']  = ! empty( $raw['show_filter_counts'] ) ? 1 : 0;
		$clean['url_filters_enabled'] = ! empty( $raw['url_filters_enabled'] ) ? 1 : 0;

		// Recherche.
		$clean['search_enabled']     = ! empty( $raw['search_enabled'] ) ? 1 : 0;
		$clean['search_acf_fields']  = isset( $raw['search_acf_fields'] ) ? self::sanitize_field_list( (string) $raw['search_acf_fields'] ) : '';
		$clean['search_label']       = isset( $raw['search_label'] ) ? sanitize_text_field( (string) $raw['search_label'] ) : '';
		$clean['search_placeholder'] = isset( $raw['search_placeholder'] ) ? sanitize_text_field( (string) $raw['search_placeholder'] ) : '';
		$clean['search_layout']      = ( isset( $raw['search_layout'] ) && in_array( $raw['search_layout'], self::search_layouts(), true ) )
			? $raw['search_layout']
			: 'inline';

		// Affichage.
		$clean['format']            = ( isset( $raw['format'] ) && in_array( $raw['format'], self::formats(), true ) ) ? $raw['format'] : 'list';
		$clean['layout_filters']    = ( isset( $raw['layout_filters'] ) && in_array( $raw['layout_filters'], self::layouts_filters(), true ) ) ? $raw['layout_filters'] : 'above';
		$clean['item_click_action'] = ( isset( $raw['item_click_action'] ) && in_array( $raw['item_click_action'], self::item_click_actions(), true ) ) ? $raw['item_click_action'] : 'none';
		$clean['show_clear_btn']    = ! empty( $raw['show_clear_btn'] ) ? 1 : 0;
		$clean['clear_btn_text']    = isset( $raw['clear_btn_text'] ) ? sanitize_text_field( (string) $raw['clear_btn_text'] ) : '';
		$clean['grid_min_width']    = isset( $raw['grid_min_width'] ) ? max( 120, min( 600, absint( $raw['grid_min_width'] ) ) ) : 240;
		$clean['grid_gap']          = isset( $raw['grid_gap'] ) ? max( 0, min( 64, absint( $raw['grid_gap'] ) ) ) : 12;
		$clean['thumb_ratio']       = ( isset( $raw['thumb_ratio'] ) && in_array( $raw['thumb_ratio'], self::thumb_ratios(), true ) ) ? $raw['thumb_ratio'] : 'auto';

		// Slider.
		$clean['slider_per_view']        = isset( $raw['slider_per_view'] ) ? max( 1, min( 8, absint( $raw['slider_per_view'] ) ) ) : 3;
		$clean['slider_per_view_tablet'] = isset( $raw['slider_per_view_tablet'] ) ? max( 1, min( 8, absint( $raw['slider_per_view_tablet'] ) ) ) : 2;
		$clean['slider_per_view_mobile'] = isset( $raw['slider_per_view_mobile'] ) ? max( 1, min( 4, absint( $raw['slider_per_view_mobile'] ) ) ) : 1;
		$clean['slider_gap']             = isset( $raw['slider_gap'] ) ? max( 0, min( 64, absint( $raw['slider_gap'] ) ) ) : 16;
		$clean['slider_speed']           = isset( $raw['slider_speed'] ) ? max( 100, min( 2000, absint( $raw['slider_speed'] ) ) ) : 500;
		$clean['slider_autoplay']        = ! empty( $raw['slider_autoplay'] ) ? 1 : 0;
		$clean['slider_autoplay_delay']  = isset( $raw['slider_autoplay_delay'] ) ? max( 1000, min( 15000, absint( $raw['slider_autoplay_delay'] ) ) ) : 4000;
		$clean['slider_loop']            = ! empty( $raw['slider_loop'] ) ? 1 : 0;
		$clean['slider_show_arrows']     = ! empty( $raw['slider_show_arrows'] ) ? 1 : 0;
		$clean['slider_arrows_position'] = ( isset( $raw['slider_arrows_position'] ) && in_array( $raw['slider_arrows_position'], self::slider_arrows_positions(), true ) ) ? $raw['slider_arrows_position'] : 'sides';
		$clean['slider_show_dots']       = ! empty( $raw['slider_show_dots'] ) ? 1 : 0;

		// Template.
		$allowed           = self::allowed_html_for_templates();
		$clean['tpl_item'] = isset( $raw['tpl_item'] ) ? wp_kses( (string) $raw['tpl_item'], $allowed ) : '';

		foreach ( $clean as $key => $value ) {
			update_post_meta( $post_id, '_mrz_display_post_exp_' . $key, $value );
		}

		// Invalide le cache transient.
		delete_transient( 'mrz_display_post_exp_' . (int) $post_id );
	}

	/**
	 * Normalise une liste de noms de champs ACF saisie en CSV.
	 */
	private static function sanitize_field_list( $raw ) {
		$parts = array_filter( array_map( 'sanitize_key', array_map( 'trim', explode( ',', $raw ) ) ) );
		return implode( ',', array_unique( $parts ) );
	}

	/**
	 * Allowlist HTML pour les templates utilisateur.
	 */
	public static function allowed_html_for_templates() {
		$allowed = wp_kses_allowed_html( 'post' );

		// Ajoute les attributs class/id/data-* sur les balises courantes.
		// 'style' est volontairement exclu (défense en profondeur : évite le
		// CSS tracking type background:url(...) par un éditeur hostile).
		$common_attrs = array(
			'class'  => true,
			'id'     => true,
			'data-*' => true,
		);

		foreach ( array( 'div', 'span', 'p', 'a', 'img', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'strong', 'em', 'br', 'i', 'b' ) as $tag ) {
			if ( ! isset( $allowed[ $tag ] ) ) {
				$allowed[ $tag ] = array();
			}
			foreach ( $common_attrs as $attr => $val ) {
				$allowed[ $tag ][ $attr ] = $val;
			}
		}

		return apply_filters( 'mrz_display_post_exp_template_kses_allowed', $allowed );
	}

	/**
	 * Enqueue admin.js / admin.css uniquement sur l'écran du CPT.
	 */
	public function enqueue_admin( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || MRZ_DISPLAY_POST_EXP_CPT !== $screen->post_type ) {
			return;
		}

		wp_enqueue_style(
			'mrz-display-post-exp-admin',
			MRZ_DISPLAY_POST_EXP_URL . 'admin/css/admin.css',
			array(),
			MRZ_DISPLAY_POST_EXP_VERSION
		);

		wp_enqueue_script(
			'mrz-display-post-exp-admin',
			MRZ_DISPLAY_POST_EXP_URL . 'admin/js/admin.js',
			array( 'jquery' ),
			MRZ_DISPLAY_POST_EXP_VERSION,
			true
		);
	}
}
