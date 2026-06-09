<?php
/**
 * Enregistrement centralisé des assets front.
 */

namespace MrzDisplayPostExp;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Assets {

	const HANDLE_SCRIPT = 'mrz-display-post-exp';
	const HANDLE_STYLE  = 'mrz-display-post-exp';

	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
		// Invalide le cache en cas de changement.
		add_action( 'save_post', array( $this, 'invalidate_on_post_save' ), 10, 2 );
		add_action( 'edited_term', array( $this, 'invalidate_all' ) );
		add_action( 'deleted_term', array( $this, 'invalidate_all' ) );
	}

	public function register_assets() {
		wp_register_style(
			self::HANDLE_STYLE,
			MRZ_DISPLAY_POST_EXP_URL . 'public/css/public.css',
			array(),
			MRZ_DISPLAY_POST_EXP_VERSION
		);

		wp_register_script(
			self::HANDLE_SCRIPT,
			MRZ_DISPLAY_POST_EXP_URL . 'public/js/mrz-display-post-exp.js',
			array(),
			MRZ_DISPLAY_POST_EXP_VERSION,
			true
		);
	}

	/**
	 * Garantit que les assets sont enregistrés (au cas où le shortcode
	 * s'exécute avant wp_enqueue_scripts, ex: widgets, rendu AJAX).
	 */
	public function ensure_registered() {
		if ( ! wp_script_is( self::HANDLE_SCRIPT, 'registered' ) ) {
			$this->register_assets();
		}
	}

	/**
	 * Enqueue les assets pour un shortcode.
	 */
	public function enqueue_for_shortcode() {
		$this->ensure_registered();
		wp_enqueue_script( self::HANDLE_SCRIPT );
		wp_enqueue_style( self::HANDLE_STYLE );
	}

	public function invalidate_on_post_save( $post_id, $post ) {
		if ( MRZ_DISPLAY_POST_EXP_CPT === $post->post_type ) {
			DataProvider::invalidate( $post_id );
			return;
		}
		// Un post source a changé : invalide tous les blocs.
		$this->invalidate_all();
	}

	public function invalidate_all() {
		$lists = get_posts(
			array(
				'post_type'      => MRZ_DISPLAY_POST_EXP_CPT,
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		foreach ( $lists as $id ) {
			DataProvider::invalidate( (int) $id );
		}
	}
}
