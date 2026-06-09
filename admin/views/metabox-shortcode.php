<?php
/**
 * Métabox : affichage du shortcode prêt-à-copier.
 *
 * @var array $values
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;
$shortcode = '[mrz_display_post_exp id="' . (int) $post->ID . '"]';
?>
<p><?php esc_html_e( 'Collez ce shortcode dans vos contenus :', 'mrz-display-post-exp' ); ?></p>
<input type="text" readonly value="<?php echo esc_attr( $shortcode ); ?>" onclick="this.select();" class="widefat code" />
<p class="description"><?php esc_html_e( 'Exemple avec filtre forcé :', 'mrz-display-post-exp' ); ?>
	<code>[mrz_display_post_exp id="<?php echo (int) $post->ID; ?>" filter_taxonomy="category" filter_term="5"]</code>
</p>
