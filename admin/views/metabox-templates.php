<?php
/**
 * Métabox : template HTML de l'item.
 *
 * @var array $values
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
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

<?php
$placeholders = array(
	array( '{post_title}', __( 'Titre du post.', 'mrz-display-post-exp' ) ),
	array( '{post_url}', __( 'Lien (permalien) du post.', 'mrz-display-post-exp' ) ),
	array( '{post_excerpt}', __( 'Extrait complet.', 'mrz-display-post-exp' ) ),
	array( '{post_excerpt:25}', __( 'Extrait tronqué à N mots (ici 25).', 'mrz-display-post-exp' ) ),
	array( '{post_thumbnail}', __( 'Image à la une (balise <img>).', 'mrz-display-post-exp' ) ),
	array( '{post_thumbnail_url}', __( 'URL de l\'image à la une.', 'mrz-display-post-exp' ) ),
	array( '{post_id}', __( 'Identifiant du post.', 'mrz-display-post-exp' ) ),
	array( '{%nom_champ_acf%}', __( 'Valeur d\'un champ ACF (remplacez « nom_champ_acf » par le nom de votre champ).', 'mrz-display-post-exp' ) ),
	array( '{taxonomy:slug}', __( 'Tous les termes de la taxonomie, chacun dans un <span class="mrz-dpe-term">.', 'mrz-display-post-exp' ) ),
	array( '{taxonomy:slug:first}', __( 'Uniquement le premier terme (texte brut).', 'mrz-display-post-exp' ) ),
	array( '{acf_date:champ}', __( 'Champ ACF de date découpé en 3 spans : jour / mois / année (voir formats ci-dessous).', 'mrz-display-post-exp' ) ),
);

$date_formats = array(
	array( '{acf_date:champ}', __( 'Mois en numéro : 08 (par défaut).', 'mrz-display-post-exp' ) ),
	array( '{acf_date:champ:n}', __( 'Mois en numéro sans zéro : 8.', 'mrz-display-post-exp' ) ),
	array( '{acf_date:champ:F}', __( 'Mois en toutes lettres, localisé : août.', 'mrz-display-post-exp' ) ),
	array( '{acf_date:champ:M}', __( 'Mois abrégé, localisé : aoû.', 'mrz-display-post-exp' ) ),
);
?>

<h4 class="mrz-display-post-exp-section-title"><?php esc_html_e( 'Placeholders disponibles', 'mrz-display-post-exp' ); ?></h4>
<table class="widefat striped mrz-display-post-exp-help">
	<tbody>
		<?php foreach ( $placeholders as $row ) : ?>
			<tr>
				<td style="width:220px;"><code><?php echo esc_html( $row[0] ); ?></code></td>
				<td><?php echo esc_html( $row[1] ); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<h4 class="mrz-display-post-exp-section-title"><?php esc_html_e( 'Formats de date ({acf_date})', 'mrz-display-post-exp' ); ?></h4>
<p class="description">
	<?php esc_html_e( 'Le jour (01) et l\'année (2026) sont fixes ; seul le mois change selon le suffixe :', 'mrz-display-post-exp' ); ?>
</p>
<table class="widefat striped mrz-display-post-exp-help">
	<tbody>
		<?php foreach ( $date_formats as $row ) : ?>
			<tr>
				<td style="width:220px;"><code><?php echo esc_html( $row[0] ); ?></code></td>
				<td><?php echo esc_html( $row[1] ); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<p class="description">
	<?php
	echo wp_kses(
		__( 'Chaque partie est dans son span : <code>.mrz-dpe-day</code>, <code>.mrz-dpe-month</code>, <code>.mrz-dpe-year</code> — à styliser via le CSS de votre thème.', 'mrz-display-post-exp' ),
		array( 'code' => array() )
	);
	?>
</p>

<h4 class="mrz-display-post-exp-section-title"><?php esc_html_e( 'Conditionnels', 'mrz-display-post-exp' ); ?></h4>
<p class="description">
	<?php
	echo wp_kses(
		__( 'Affiche un bloc seulement si le champ est renseigné : <code>{#if %mon_champ%}&lt;div&gt;...&lt;/div&gt;{/if}</code>. Fonctionne aussi avec <code>{#if post_thumbnail}…{/if}</code> ou <code>{#if taxonomy:category}…{/if}</code>.', 'mrz-display-post-exp' ),
		array( 'code' => array() )
	);
	?>
</p>
