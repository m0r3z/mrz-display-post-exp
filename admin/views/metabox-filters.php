<?php
/**
 * Métabox : filtres (taxonomies + champs ACF) et recherche.
 *
 * @var array $values
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$all_tax      = get_taxonomies( array( 'public' => true ), 'objects' );

// Ordonne les taxonomies : sélectionnées d'abord (dans l'ordre sauvegardé), puis le reste.
$ordered_tax = array();
foreach ( (array) $values['taxonomies'] as $slug ) {
	if ( isset( $all_tax[ $slug ] ) ) {
		$ordered_tax[ $slug ] = $all_tax[ $slug ];
	}
}
foreach ( $all_tax as $slug => $tax ) {
	if ( ! isset( $ordered_tax[ $slug ] ) ) {
		$ordered_tax[ $slug ] = $tax;
	}
}

$sort_handle = '<span class="mrz-display-post-exp-sort-handle" aria-hidden="true" title="' . esc_attr__( 'Glisser pour réordonner', 'mrz-display-post-exp' ) . '">⠿</span>';
$modes_labels = array(
	'dropdown' => __( 'Menu déroulant', 'mrz-display-post-exp' ),
	'radio'    => __( 'Boutons radio', 'mrz-display-post-exp' ),
	'checkbox' => __( 'Cases à cocher', 'mrz-display-post-exp' ),
);
$logic_labels = array(
	'or'  => __( 'OU', 'mrz-display-post-exp' ),
	'and' => __( 'ET', 'mrz-display-post-exp' ),
);

$acf_filters = (array) $values['acf_filters'];
?>

<h3 class="mrz-display-post-exp-section-title"><?php esc_html_e( 'Recherche', 'mrz-display-post-exp' ); ?></h3>
<p>
	<label>
		<input type="checkbox" name="mrz_display_post_exp[search_enabled]" value="1" <?php checked( ! empty( $values['search_enabled'] ) ); ?> />
		<?php esc_html_e( 'Afficher un champ de recherche (filtrage instantané côté client).', 'mrz-display-post-exp' ); ?>
	</label>
</p>
<p>
	<label>
		<?php esc_html_e( 'Champs ACF à inclure dans la recherche :', 'mrz-display-post-exp' ); ?>
		<input type="text" name="mrz_display_post_exp[search_acf_fields]" value="<?php echo esc_attr( $values['search_acf_fields'] ); ?>" class="regular-text" placeholder="sous_titre, resume" />
	</label>
	<br />
	<span class="description"><?php esc_html_e( 'Liste de noms de champs ACF texte séparés par des virgules. Le titre du post est toujours inclus.', 'mrz-display-post-exp' ); ?></span>
</p>
<p>
	<?php esc_html_e( 'Position du champ :', 'mrz-display-post-exp' ); ?>
	<?php
	$search_layouts_labels = array(
		'inline' => __( 'Dans le bloc filtres', 'mrz-display-post-exp' ),
		'top'    => __( 'En haut, pleine largeur', 'mrz-display-post-exp' ),
	);
	foreach ( $search_layouts_labels as $val => $label ) :
		?>
		<label style="margin-right:16px;">
			<input type="radio" name="mrz_display_post_exp[search_layout]" value="<?php echo esc_attr( $val ); ?>" <?php checked( $values['search_layout'], $val ); ?> />
			<?php echo esc_html( $label ); ?>
		</label>
	<?php endforeach; ?>
</p>
<p>
	<label>
		<?php esc_html_e( 'Libellé affiché au-dessus du champ :', 'mrz-display-post-exp' ); ?>
		<input type="text" name="mrz_display_post_exp[search_label]" value="<?php echo esc_attr( $values['search_label'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Rechercher', 'mrz-display-post-exp' ); ?>" />
	</label>
</p>
<p>
	<label>
		<?php esc_html_e( 'Placeholder du champ :', 'mrz-display-post-exp' ); ?>
		<input type="text" name="mrz_display_post_exp[search_placeholder]" value="<?php echo esc_attr( $values['search_placeholder'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Rechercher…', 'mrz-display-post-exp' ); ?>" />
	</label>
</p>

<hr />

<h3 class="mrz-display-post-exp-section-title"><?php esc_html_e( 'Options globales', 'mrz-display-post-exp' ); ?></h3>
<p>
	<label>
		<input type="checkbox" name="mrz_display_post_exp[show_filter_counts]" value="1" <?php checked( ! empty( $values['show_filter_counts'] ) ); ?> />
		<?php esc_html_e( 'Afficher le nombre de résultats à côté de chaque option de filtre.', 'mrz-display-post-exp' ); ?>
	</label>
</p>
<p>
	<label>
		<input type="checkbox" name="mrz_display_post_exp[url_filters_enabled]" value="1" <?php checked( ! empty( $values['url_filters_enabled'] ) ); ?> />
		<?php esc_html_e( 'Synchroniser les filtres avec l\'URL (lien partageable).', 'mrz-display-post-exp' ); ?>
	</label>
</p>

<hr />

<h3 class="mrz-display-post-exp-section-title"><?php esc_html_e( 'Filtres par taxonomie', 'mrz-display-post-exp' ); ?></h3>

<div class="mrz-display-post-exp-taxo-list">
	<?php foreach ( $ordered_tax as $tax ) : ?>
		<?php
		$slug         = $tax->name;
		$checked      = in_array( $slug, (array) $values['taxonomies'], true );
		$mode         = isset( $values['taxo_modes'][ $slug ] ) ? $values['taxo_modes'][ $slug ] : 'dropdown';
		$logic        = isset( $values['taxo_logic'][ $slug ] ) ? $values['taxo_logic'][ $slug ] : 'or';
		$custom_label = isset( $values['taxo_labels'][ $slug ] ) ? $values['taxo_labels'][ $slug ] : '';
		$object_type  = implode( ',', (array) $tax->object_type );
		?>
		<div class="mrz-display-post-exp-taxo-row" data-object-types="<?php echo esc_attr( $object_type ); ?>">
			<?php echo $sort_handle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — titre déjà échappé via esc_attr__ ?>
			<label class="mrz-display-post-exp-taxo-col mrz-display-post-exp-taxo-col-activate">
				<span><?php esc_html_e( 'Taxonomie', 'mrz-display-post-exp' ); ?></span>
				<span class="mrz-display-post-exp-taxo-activate-row">
					<input type="checkbox" name="mrz_display_post_exp[taxonomies][]" value="<?php echo esc_attr( $slug ); ?>" <?php checked( $checked ); ?> />
					<span class="mrz-display-post-exp-taxo-name"><?php echo esc_html( $tax->labels->singular_name . ' (' . $slug . ')' ); ?></span>
				</span>
			</label>
			<label class="mrz-display-post-exp-taxo-col">
				<span><?php esc_html_e( 'Libellé affiché', 'mrz-display-post-exp' ); ?></span>
				<input type="text" name="mrz_display_post_exp[taxo_labels][<?php echo esc_attr( $slug ); ?>]" value="<?php echo esc_attr( $custom_label ); ?>" class="regular-text" placeholder="<?php echo esc_attr( $tax->labels->singular_name ); ?>" />
			</label>
			<label class="mrz-display-post-exp-taxo-col">
				<span><?php esc_html_e( 'Type de filtre', 'mrz-display-post-exp' ); ?></span>
				<select name="mrz_display_post_exp[taxo_modes][<?php echo esc_attr( $slug ); ?>]" class="mrz-display-post-exp-taxo-mode">
					<?php foreach ( $modes_labels as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $mode, $value ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>
			<label class="mrz-display-post-exp-taxo-col">
				<span><?php esc_html_e( 'Logique', 'mrz-display-post-exp' ); ?></span>
				<select name="mrz_display_post_exp[taxo_logic][<?php echo esc_attr( $slug ); ?>]" class="mrz-display-post-exp-taxo-logic" title="<?php esc_attr_e( 'Combinaison entre cases cochées', 'mrz-display-post-exp' ); ?>">
					<?php foreach ( $logic_labels as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $logic, $value ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>
		</div>
	<?php endforeach; ?>
</div>
<p class="description"><?php esc_html_e( 'Seules les taxonomies liées au post type sélectionné sont affichées. Le libellé remplace le nom de la taxonomie affiché au-dessus du filtre sur le site.', 'mrz-display-post-exp' ); ?></p>

<hr />

<h3 class="mrz-display-post-exp-section-title"><?php esc_html_e( 'Filtres par champ ACF', 'mrz-display-post-exp' ); ?></h3>
<p class="description">
	<?php esc_html_e( 'Pour les champs Select, Radio, Checkbox ou Vrai/Faux, les options sont détectées automatiquement depuis la configuration ACF. Pour les autres types (texte, nombre), les valeurs distinctes des posts sont collectées dynamiquement.', 'mrz-display-post-exp' ); ?>
</p>

<div class="mrz-display-post-exp-acf-filters" data-next-index="<?php echo (int) count( $acf_filters ); ?>">
	<?php foreach ( $acf_filters as $i => $row ) : ?>
		<?php
		$field     = isset( $row['field'] ) ? $row['field'] : '';
		$label     = isset( $row['label'] ) ? $row['label'] : '';
		$mode      = isset( $row['mode'] ) ? $row['mode'] : 'dropdown';
		$row_logic = isset( $row['logic'] ) ? $row['logic'] : 'or';
		?>
		<div class="mrz-display-post-exp-acf-row" data-index="<?php echo (int) $i; ?>">
			<?php echo $sort_handle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — titre déjà échappé via esc_attr__ ?>
			<label class="mrz-display-post-exp-acf-col">
				<span><?php esc_html_e( 'Nom du champ ACF', 'mrz-display-post-exp' ); ?></span>
				<input type="text" name="mrz_display_post_exp[acf_filters][<?php echo (int) $i; ?>][field]" value="<?php echo esc_attr( $field ); ?>" class="regular-text" placeholder="type_annonce" />
			</label>
			<label class="mrz-display-post-exp-acf-col">
				<span><?php esc_html_e( 'Libellé affiché', 'mrz-display-post-exp' ); ?></span>
				<input type="text" name="mrz_display_post_exp[acf_filters][<?php echo (int) $i; ?>][label]" value="<?php echo esc_attr( $label ); ?>" class="regular-text" placeholder="Type d'annonce" />
			</label>
			<label class="mrz-display-post-exp-acf-col">
				<span><?php esc_html_e( 'Type de filtre', 'mrz-display-post-exp' ); ?></span>
				<select name="mrz_display_post_exp[acf_filters][<?php echo (int) $i; ?>][mode]">
					<?php foreach ( $modes_labels as $value => $mlabel ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $mode, $value ); ?>>
							<?php echo esc_html( $mlabel ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>
			<label class="mrz-display-post-exp-acf-col">
				<span><?php esc_html_e( 'Logique', 'mrz-display-post-exp' ); ?></span>
				<select name="mrz_display_post_exp[acf_filters][<?php echo (int) $i; ?>][logic]" title="<?php esc_attr_e( 'Combinaison entre cases cochées (sans effet en mode dropdown/radio)', 'mrz-display-post-exp' ); ?>">
					<?php foreach ( $logic_labels as $value => $llabel ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $row_logic, $value ); ?>>
							<?php echo esc_html( $llabel ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>
			<button type="button" class="button mrz-display-post-exp-acf-remove"><?php esc_html_e( 'Retirer', 'mrz-display-post-exp' ); ?></button>
		</div>
	<?php endforeach; ?>
</div>

<p>
	<button type="button" class="button mrz-display-post-exp-acf-add"><?php esc_html_e( 'Ajouter un filtre ACF', 'mrz-display-post-exp' ); ?></button>
</p>

<template id="mrz-display-post-exp-acf-row-template">
	<div class="mrz-display-post-exp-acf-row" data-index="__INDEX__">
		<?php echo $sort_handle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — titre déjà échappé via esc_attr__ ?>
		<label class="mrz-display-post-exp-acf-col">
			<span><?php esc_html_e( 'Nom du champ ACF', 'mrz-display-post-exp' ); ?></span>
			<input type="text" name="mrz_display_post_exp[acf_filters][__INDEX__][field]" value="" class="regular-text" placeholder="type_annonce" />
		</label>
		<label class="mrz-display-post-exp-acf-col">
			<span><?php esc_html_e( 'Libellé affiché', 'mrz-display-post-exp' ); ?></span>
			<input type="text" name="mrz_display_post_exp[acf_filters][__INDEX__][label]" value="" class="regular-text" placeholder="Type d'annonce" />
		</label>
		<label class="mrz-display-post-exp-acf-col">
			<span><?php esc_html_e( 'Type de filtre', 'mrz-display-post-exp' ); ?></span>
			<select name="mrz_display_post_exp[acf_filters][__INDEX__][mode]">
				<?php foreach ( $modes_labels as $value => $mlabel ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $mlabel ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<label class="mrz-display-post-exp-acf-col">
			<span><?php esc_html_e( 'Logique', 'mrz-display-post-exp' ); ?></span>
			<select name="mrz_display_post_exp[acf_filters][__INDEX__][logic]">
				<?php foreach ( $logic_labels as $value => $llabel ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $llabel ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<button type="button" class="button mrz-display-post-exp-acf-remove"><?php esc_html_e( 'Retirer', 'mrz-display-post-exp' ); ?></button>
	</div>
</template>
