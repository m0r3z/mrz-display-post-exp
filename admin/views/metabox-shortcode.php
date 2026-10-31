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
$shortcode = '[mrzdpe id="' . (int) $post->ID . '"]';
?>
<p><?php esc_html_e( 'Paste this shortcode into your content:', 'mrz-display-post-exp' ); ?></p>
<input type="text" readonly value="<?php echo esc_attr( $shortcode ); ?>" onclick="this.select();" class="widefat code" />
<p class="description"><?php esc_html_e( 'Example with a forced filter:', 'mrz-display-post-exp' ); ?>
	<code>[mrzdpe id="<?php echo (int) $post->ID; ?>" filter_taxonomy="category" filter_term="5"]</code>
</p>
