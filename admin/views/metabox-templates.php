<?php
/**
 * Métabox : template HTML de l'item.
 *
 * @var array $values
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$allowed_code = array( 'code' => array() );
?>
<p>
	<?php esc_html_e( 'Placeholders disponibles :', 'mrz-display-post-exp' ); ?>
	<?php
	echo ' ';
	echo wp_kses(
		'<code>{post_title}</code>, <code>{post_url}</code>, <code>{post_excerpt}</code>, <code>{post_excerpt:25}</code> (tronqué à 25 mots), <code>{post_thumbnail}</code>, <code>{post_thumbnail_url}</code>, <code>{post_id}</code>, <code>{%nom_champ_acf%}</code>, <code>{taxonomy:slug}</code> (tous les termes) , <code>{taxonomy:slug:first}</code> (premier terme).',
		$allowed_code
	);
	?>
	<br />
	<?php esc_html_e( 'Conditionnels :', 'mrz-display-post-exp' ); ?>
	<?php
	echo ' ';
	echo wp_kses(
		'<code>{#if %mon_champ%}&lt;div&gt;...&lt;/div&gt;{/if}</code>',
		$allowed_code
	);
	?>
</p>
<table class="form-table mrz-display-post-exp-table">
	<tbody>
		<tr>
			<th scope="row">
				<label for="mrz_display_post_exp_tpl_item"><?php esc_html_e( 'Template de l\'item', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<textarea name="mrz_display_post_exp[tpl_item]" id="mrz_display_post_exp_tpl_item" rows="10" class="large-text code"><?php echo esc_textarea( $values['tpl_item'] ); ?></textarea>
				<p class="description"><?php esc_html_e( 'HTML affiché pour chaque entrée (liste, grille ou slider). L\'attribut style n\'est pas autorisé : mettez en forme via le CSS de votre thème.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
	</tbody>
</table>
