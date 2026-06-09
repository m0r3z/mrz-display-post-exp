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
