<?php
/**
 * Enregistre le Custom Post Type des blocs d'affichage.
 */

namespace MrzDisplayPostExp;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CPT {

	public function register() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_filter( 'manage_' . MRZ_DISPLAY_POST_EXP_CPT . '_posts_columns', array( $this, 'columns' ) );
		add_action( 'manage_' . MRZ_DISPLAY_POST_EXP_CPT . '_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_menu_icon_style' ) );
	}

	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'Affichages', 'post type general name', 'mrz-display-post-exp' ),
			'singular_name'      => _x( 'Affichage', 'post type singular name', 'mrz-display-post-exp' ),
			'menu_name'          => _x( 'MRZ Display Post', 'admin menu', 'mrz-display-post-exp' ),
			'name_admin_bar'     => _x( 'Affichage', 'add new on admin bar', 'mrz-display-post-exp' ),
			'add_new'            => _x( 'Ajouter', 'display block', 'mrz-display-post-exp' ),
			'add_new_item'       => __( 'Ajouter un affichage', 'mrz-display-post-exp' ),
			'new_item'           => __( 'Nouvel affichage', 'mrz-display-post-exp' ),
			'edit_item'          => __( 'Modifier l\'affichage', 'mrz-display-post-exp' ),
			'view_item'          => __( 'Voir l\'affichage', 'mrz-display-post-exp' ),
			'all_items'          => __( 'Tous les affichages', 'mrz-display-post-exp' ),
			'search_items'       => __( 'Rechercher un affichage', 'mrz-display-post-exp' ),
			'not_found'          => __( 'Aucun affichage trouvé.', 'mrz-display-post-exp' ),
			'not_found_in_trash' => __( 'Aucun affichage dans la corbeille.', 'mrz-display-post-exp' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => false,
			'show_in_admin_bar'  => false,
			'show_in_rest'       => false,
			'menu_icon'          => 'none',
			'menu_position'      => 90,
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'hierarchical'       => false,
			'supports'           => array( 'title' ),
			'has_archive'        => false,
			'rewrite'            => false,
			'query_var'          => false,
		);

		register_post_type( MRZ_DISPLAY_POST_EXP_CPT, $args );
	}

	/**
	 * Injecte l'icône du menu via CSS mask-image : pas de flash de la couleur
	 * native du SVG au chargement, couleur directement conforme au thème admin
	 * (gris 60% au repos, blanc au hover / submenu ouvert / page active).
	 */
	public function enqueue_menu_icon_style() {
		$handle = 'mrz-display-post-exp-menu-icon';
		wp_register_style( $handle, false, array(), MRZ_DISPLAY_POST_EXP_VERSION );
		wp_enqueue_style( $handle );

		$url = esc_url( MRZ_DISPLAY_POST_EXP_URL . 'assets/menu-icon.svg?ver=' . MRZ_DISPLAY_POST_EXP_VERSION );
		// L'ID exact du <li> varie selon la façon dont WP sanitise le menu_file.
		// Sélecteur tolérant : tout menu_top dont l'ID contient le slug du CPT.
		$sel = '#adminmenu li.menu-top[id*="' . MRZ_DISPLAY_POST_EXP_CPT . '"]';

		$css  = $sel . ' .wp-menu-image{background:none!important;background-color:rgba(240,246,252,.6)!important;';
		$css .= '-webkit-mask:url(\'' . $url . '\') no-repeat 9px 7px/20px;mask:url(\'' . $url . '\') no-repeat 9px 7px/20px;}';
		$css .= $sel . ' .wp-menu-image::before{display:none;}';
		$css .= $sel . ':hover .wp-menu-image,';
		$css .= $sel . '.wp-has-current-submenu .wp-menu-image,';
		$css .= $sel . '.current .wp-menu-image,';
		$css .= $sel . '.wp-menu-open .wp-menu-image{background-color:#fff!important;}';

		wp_add_inline_style( $handle, $css );
	}

	public function columns( $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['mrz_display_post_exp_shortcode'] = __( 'Shortcode', 'mrz-display-post-exp' );
			}
		}
		return $new;
	}

	public function column_content( $column, $post_id ) {
		if ( 'mrz_display_post_exp_shortcode' !== $column ) {
			return;
		}
		printf(
			'<code>[mrz_display_post_exp id="%d"]</code>',
			(int) $post_id
		);
	}
}
