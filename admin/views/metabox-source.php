<?php
/**
 * Métabox : source des données (post type, tri, limite, pagination).
 *
 * @var array $values
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use MrzDisplayPostExp\ListConfig;

$public_pts = get_post_types( array( 'public' => true ), 'objects' );

$orderby_labels = array(
	'date'       => __( 'Date de publication', 'mrz-display-post-exp' ),
	'title'      => __( 'Titre', 'mrz-display-post-exp' ),
	'menu_order' => __( 'Ordre manuel (menu_order)', 'mrz-display-post-exp' ),
	'rand'       => __( 'Aléatoire', 'mrz-display-post-exp' ),
	'modified'   => __( 'Date de modification', 'mrz-display-post-exp' ),
	'acf_date'   => __( 'Champ ACF de date (agenda)', 'mrz-display-post-exp' ),
);
?>
<table class="form-table mrz-display-post-exp-table">
	<tbody>
		<tr>
			<th scope="row">
				<label for="mrz_display_post_exp_source_pt"><?php esc_html_e( 'Post type source', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<select name="mrz_display_post_exp[source_pt]" id="mrz_display_post_exp_source_pt">
					<?php foreach ( $public_pts as $pt ) : ?>
						<option value="<?php echo esc_attr( $pt->name ); ?>" <?php selected( $values['source_pt'], $pt->name ); ?>>
							<?php echo esc_html( $pt->labels->singular_name . ' (' . $pt->name . ')' ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p class="description"><?php esc_html_e( 'Le post type dont les entrées seront affichées.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="mrz_display_post_exp_orderby"><?php esc_html_e( 'Trier par', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<select name="mrz_display_post_exp[orderby]" id="mrz_display_post_exp_orderby">
					<?php foreach ( ListConfig::orderbys() as $ob ) : ?>
						<option value="<?php echo esc_attr( $ob ); ?>" <?php selected( $values['orderby'], $ob ); ?>>
							<?php echo esc_html( isset( $orderby_labels[ $ob ] ) ? $orderby_labels[ $ob ] : $ob ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<select name="mrz_display_post_exp[order]" id="mrz_display_post_exp_order">
					<option value="DESC" <?php selected( $values['order'], 'DESC' ); ?>><?php esc_html_e( 'Décroissant', 'mrz-display-post-exp' ); ?></option>
					<option value="ASC" <?php selected( $values['order'], 'ASC' ); ?>><?php esc_html_e( 'Croissant', 'mrz-display-post-exp' ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="mrz-display-post-exp-when-acfdate">
			<th scope="row">
				<label for="mrz_display_post_exp_orderby_acf_field"><?php esc_html_e( 'Champ ACF de date', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<input type="text" name="mrz_display_post_exp[orderby_acf_field]" id="mrz_display_post_exp_orderby_acf_field" value="<?php echo esc_attr( $values['orderby_acf_field'] ); ?>" class="regular-text" placeholder="date_evenement" />
				<p class="description"><?php esc_html_e( 'Nom du champ ACF de type Date (ou Date/Heure) servant au tri. Seules les entrées possédant ce champ seront affichées.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr class="mrz-display-post-exp-when-acfdate">
			<th scope="row">
				<label for="mrz_display_post_exp_acf_date_scope"><?php esc_html_e( 'Afficher', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<select name="mrz_display_post_exp[acf_date_scope]" id="mrz_display_post_exp_acf_date_scope">
					<option value="all" <?php selected( $values['acf_date_scope'], 'all' ); ?>><?php esc_html_e( 'Toutes les dates', 'mrz-display-post-exp' ); ?></option>
					<option value="upcoming" <?php selected( $values['acf_date_scope'], 'upcoming' ); ?>><?php esc_html_e( 'À venir uniquement (≥ aujourd\'hui)', 'mrz-display-post-exp' ); ?></option>
					<option value="past" <?php selected( $values['acf_date_scope'], 'past' ); ?>><?php esc_html_e( 'Passées uniquement (< aujourd\'hui)', 'mrz-display-post-exp' ); ?></option>
				</select>
				<p class="description"><?php esc_html_e( 'Filtre côté serveur sur le champ de date ci-dessus. « À venir » masque les événements passés (idéal agenda).', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="mrz_display_post_exp_limit"><?php esc_html_e( 'Nombre max de posts', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<input type="number" name="mrz_display_post_exp[limit]" id="mrz_display_post_exp_limit" value="<?php echo esc_attr( $values['limit'] ); ?>" min="0" step="1" />
				<p class="description"><?php esc_html_e( 'Limite de chargement côté serveur. 0 = illimité.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="mrz_display_post_exp_per_page"><?php esc_html_e( 'Posts par page', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<input type="number" name="mrz_display_post_exp[per_page]" id="mrz_display_post_exp_per_page" value="<?php echo esc_attr( $values['per_page'] ); ?>" min="0" step="1" />
				<p class="description"><?php esc_html_e( 'Pagination côté utilisateur (liste et grille) : nombre de posts par page. 0 = tous affichés. Ignoré en mode slider.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
	</tbody>
</table>
