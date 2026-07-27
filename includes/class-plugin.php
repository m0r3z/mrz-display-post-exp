<?php
/**
 * Classe principale : bootstrap des modules.
 */

namespace Mrzdpe;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Plugin {

	/**
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * @var bool
	 */
	private $booted = false;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function boot() {
		if ( $this->booted ) {
			return;
		}
		$this->booted = true;

		// Migration unique depuis l'ancien préfixe (mrz_display_post_exp / mrz_dpe).
		add_action( 'admin_init', array( $this, 'maybe_migrate_legacy' ) );

		// Pas de load_plugin_textdomain() : depuis WordPress 4.6, les traductions
		// hébergées sur translate.wordpress.org sont chargées automatiquement par
		// le cœur pour les plugins publiés sur le répertoire officiel.

		if ( ! mrzdpe_has_acf() ) {
			add_action( 'admin_notices', array( $this, 'notice_missing_acf' ) );
			return;
		}

		( new CPT() )->register();
		( new ListConfig() )->register();
		( new Assets() )->register();
		( new Shortcode() )->register();
	}

	/**
	 * Migration unique des données créées avant le renommage du préfixe
	 * (ancien : CPT « mrz_dpe_list », metas « _mrz_display_post_exp_ »,
	 * shortcode « [mrz_display_post_exp] » → nouveau préfixe « mrzdpe »).
	 * Idempotent : ne s'exécute qu'une fois, ne touche que les données héritées.
	 */
	public function maybe_migrate_legacy() {
		if ( get_option( 'mrzdpe_migrated_1' ) ) {
			return;
		}

		global $wpdb;

		// CPT : mrz_dpe_list → mrzdpe_list.
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->posts} SET post_type = %s WHERE post_type = %s",
				'mrzdpe_list',
				'mrz_dpe_list'
			)
		);

		// Metas : _mrz_display_post_exp_* → _mrzdpe_*.
		$old_meta_prefix = '_mrz_display_post_exp_';
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->postmeta} SET meta_key = CONCAT('_mrzdpe_', SUBSTRING(meta_key, %d)) WHERE meta_key LIKE %s",
				strlen( $old_meta_prefix ) + 1,
				$wpdb->esc_like( $old_meta_prefix ) . '%'
			)
		);

		// Shortcodes dans le contenu : [mrz_display_post_exp → [mrzdpe.
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s) WHERE post_content LIKE %s",
				'[mrz_display_post_exp',
				'[mrzdpe',
				'%' . $wpdb->esc_like( '[mrz_display_post_exp' ) . '%'
			)
		);

		wp_cache_flush();
		update_option( 'mrzdpe_migrated_1', 1, false );
	}

	public function notice_missing_acf() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		printf(
			'<div class="notice notice-error"><p>%s</p></div>',
			esc_html__( 'MRZ Display Post nécessite Advanced Custom Fields (Pro recommandé) pour fonctionner.', 'mrz-display-post-exp' )
		);
	}
}
