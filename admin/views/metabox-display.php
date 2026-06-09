<?php
/**
 * Métabox : affichage (format, layout, options grille/slider).
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
			<th scope="row"><?php esc_html_e( 'Format d\'affichage', 'mrz-display-post-exp' ); ?></th>
			<td>
				<?php
				$formats = array(
					'list'   => __( 'Liste', 'mrz-display-post-exp' ),
					'grid'   => __( 'Grille', 'mrz-display-post-exp' ),
					'slider' => __( 'Slider', 'mrz-display-post-exp' ),
				);
				foreach ( $formats as $val => $label ) :
					?>
					<label style="margin-right:16px;">
						<input type="radio" name="mrz_display_post_exp[format]" class="mrz-display-post-exp-format-input" value="<?php echo esc_attr( $val ); ?>" <?php checked( $values['format'], $val ); ?> />
						<?php echo esc_html( $label ); ?>
					</label>
				<?php endforeach; ?>
			</td>
		</tr>

		<tr class="mrz-display-post-exp-when-grid">
			<th scope="row">
				<label for="mrz_display_post_exp_grid_min_width"><?php esc_html_e( 'Largeur min. d\'une colonne (px)', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<input type="number" name="mrz_display_post_exp[grid_min_width]" id="mrz_display_post_exp_grid_min_width" value="<?php echo esc_attr( $values['grid_min_width'] ); ?>" min="120" max="600" step="10" />
				<p class="description"><?php esc_html_e( 'La grille remplit automatiquement autant de colonnes que possible à partir de cette largeur minimale.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>

		<tr class="mrz-display-post-exp-when-slider">
			<th scope="row"><?php esc_html_e( 'Items visibles (slider)', 'mrz-display-post-exp' ); ?></th>
			<td>
				<label style="margin-right:12px;">
					<?php esc_html_e( 'Bureau :', 'mrz-display-post-exp' ); ?>
					<input type="number" name="mrz_display_post_exp[slider_per_view]" value="<?php echo esc_attr( $values['slider_per_view'] ); ?>" min="1" max="8" step="1" class="small-text" />
				</label>
				<label style="margin-right:12px;">
					<?php esc_html_e( 'Tablette :', 'mrz-display-post-exp' ); ?>
					<input type="number" name="mrz_display_post_exp[slider_per_view_tablet]" value="<?php echo esc_attr( $values['slider_per_view_tablet'] ); ?>" min="1" max="8" step="1" class="small-text" />
				</label>
				<label>
					<?php esc_html_e( 'Mobile :', 'mrz-display-post-exp' ); ?>
					<input type="number" name="mrz_display_post_exp[slider_per_view_mobile]" value="<?php echo esc_attr( $values['slider_per_view_mobile'] ); ?>" min="1" max="4" step="1" class="small-text" />
				</label>
				<p class="description"><?php esc_html_e( 'Bureau > 1024px, tablette ≤ 1024px, mobile ≤ 768px.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr class="mrz-display-post-exp-when-slider">
			<th scope="row">
				<label for="mrz_display_post_exp_slider_gap"><?php esc_html_e( 'Espacement entre items (px)', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<input type="number" name="mrz_display_post_exp[slider_gap]" id="mrz_display_post_exp_slider_gap" value="<?php echo esc_attr( $values['slider_gap'] ); ?>" min="0" max="64" step="1" />
			</td>
		</tr>
		<tr class="mrz-display-post-exp-when-slider">
			<th scope="row">
				<label for="mrz_display_post_exp_slider_speed"><?php esc_html_e( 'Vitesse de transition (ms)', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<input type="number" name="mrz_display_post_exp[slider_speed]" id="mrz_display_post_exp_slider_speed" value="<?php echo esc_attr( $values['slider_speed'] ); ?>" min="100" max="2000" step="50" />
				<p class="description"><?php esc_html_e( 'Durée du glissement au clic des flèches / puces et en défilement automatique. Plus la valeur est élevée, plus la transition est lente et douce (défaut : 500 ms). Le glissement au doigt reste natif.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr class="mrz-display-post-exp-when-slider">
			<th scope="row"><?php esc_html_e( 'Options du slider', 'mrz-display-post-exp' ); ?></th>
			<td>
				<p>
					<label>
						<input type="checkbox" name="mrz_display_post_exp[slider_show_arrows]" value="1" <?php checked( ! empty( $values['slider_show_arrows'] ) ); ?> />
						<?php esc_html_e( 'Afficher les flèches précédent / suivant.', 'mrz-display-post-exp' ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" name="mrz_display_post_exp[slider_show_dots]" value="1" <?php checked( ! empty( $values['slider_show_dots'] ) ); ?> />
						<?php esc_html_e( 'Afficher les puces de pagination.', 'mrz-display-post-exp' ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" name="mrz_display_post_exp[slider_loop]" value="1" <?php checked( ! empty( $values['slider_loop'] ) ); ?> />
						<?php esc_html_e( 'Boucler (revenir au début après le dernier item).', 'mrz-display-post-exp' ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" name="mrz_display_post_exp[slider_autoplay]" value="1" <?php checked( ! empty( $values['slider_autoplay'] ) ); ?> />
						<?php esc_html_e( 'Défilement automatique.', 'mrz-display-post-exp' ); ?>
					</label>
				</p>
				<p>
					<label>
						<?php esc_html_e( 'Délai entre transitions (ms) :', 'mrz-display-post-exp' ); ?>
						<input type="number" name="mrz_display_post_exp[slider_autoplay_delay]" value="<?php echo esc_attr( $values['slider_autoplay_delay'] ); ?>" min="1000" max="15000" step="500" />
					</label>
					<br />
					<span class="description"><?php esc_html_e( 'Le défilement automatique respecte la préférence « réduire les animations » du visiteur et se met en pause au survol.', 'mrz-display-post-exp' ); ?></span>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row"><?php esc_html_e( 'Layout des filtres', 'mrz-display-post-exp' ); ?></th>
			<td>
				<?php
				$filter_layouts = array(
					'above'      => __( 'Au-dessus', 'mrz-display-post-exp' ),
					'side-left'  => __( 'À gauche', 'mrz-display-post-exp' ),
					'side-right' => __( 'À droite', 'mrz-display-post-exp' ),
				);
				foreach ( $filter_layouts as $val => $label ) :
					?>
					<label style="margin-right:16px;">
						<input type="radio" name="mrz_display_post_exp[layout_filters]" value="<?php echo esc_attr( $val ); ?>" <?php checked( $values['layout_filters'], $val ); ?> />
						<?php echo esc_html( $label ); ?>
					</label>
				<?php endforeach; ?>
				<p class="description"><?php esc_html_e( 'N\'a d\'effet que si des filtres ou la recherche sont activés.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Clic sur un item', 'mrz-display-post-exp' ); ?></th>
			<td>
				<?php
				$click_actions = array(
					'none' => __( 'Ne rien faire', 'mrz-display-post-exp' ),
					'link' => __( 'Ouvrir la page du post', 'mrz-display-post-exp' ),
				);
				foreach ( $click_actions as $val => $label ) :
					?>
					<label style="display:block;margin-bottom:4px;">
						<input type="radio" name="mrz_display_post_exp[item_click_action]" value="<?php echo esc_attr( $val ); ?>" <?php checked( $values['item_click_action'], $val ); ?> />
						<?php echo esc_html( $label ); ?>
					</label>
				<?php endforeach; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Bouton de réinitialisation', 'mrz-display-post-exp' ); ?></th>
			<td>
				<p>
					<label>
						<input type="checkbox" name="mrz_display_post_exp[show_clear_btn]" value="1" <?php checked( ! empty( $values['show_clear_btn'] ) ); ?> />
						<?php esc_html_e( 'Afficher un bouton de réinitialisation de tous les filtres.', 'mrz-display-post-exp' ); ?>
					</label>
				</p>
				<p>
					<label>
						<?php esc_html_e( 'Texte du bouton :', 'mrz-display-post-exp' ); ?>
						<input type="text" name="mrz_display_post_exp[clear_btn_text]" value="<?php echo esc_attr( $values['clear_btn_text'] ); ?>" placeholder="<?php esc_attr_e( 'Effacer', 'mrz-display-post-exp' ); ?>" class="regular-text" />
					</label>
				</p>
			</td>
		</tr>
	</tbody>
</table>
