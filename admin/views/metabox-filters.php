<?php
/**
 * Métabox : filtres (taxonomies + champs ACF) et recherche.
 *
 * @var array $values
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$all_tax     = get_taxonomies( array( 'public' => true ), 'objects' );
$active_tax  = (array) $values['taxonomies'];
$acf_filters = (array) $values['acf_filters'];

// Liste unique des lignes de filtre, dans l'ordre d'affichage admin :
// 1. filtres actifs selon filters_order, 2. actifs absents de l'ordre
// (rétrocompat : taxonomies puis ACF), 3. taxonomies non cochées.
$filter_rows = array();
$placed      = array();

$acf_index_by_key = array();
foreach ( $acf_filters as $i => $row ) {
	if ( ! empty( $row['field'] ) && ! isset( $acf_index_by_key[ 'acf:' . $row['field'] ] ) ) {
		$acf_index_by_key[ 'acf:' . $row['field'] ] = $i;
	}
}

$place_tax = static function ( $slug ) use ( &$filter_rows, &$placed, $all_tax ) {
	if ( isset( $all_tax[ $slug ] ) && ! isset( $placed[ 'tax:' . $slug ] ) ) {
		$filter_rows[]            = array( 'type' => 'tax', 'tax' => $all_tax[ $slug ] );
		$placed[ 'tax:' . $slug ] = true;
	}
};
$place_acf = static function ( $i ) use ( &$filter_rows, &$placed, $acf_filters ) {
	if ( isset( $acf_filters[ $i ] ) && ! isset( $placed[ 'acf#' . $i ] ) ) {
		$filter_rows[]         = array( 'type' => 'acf', 'index' => $i, 'row' => $acf_filters[ $i ] );
		$placed[ 'acf#' . $i ] = true;
	}
};

foreach ( (array) $values['filters_order'] as $key ) {
	if ( 0 === strpos( $key, 'tax:' ) && in_array( substr( $key, 4 ), $active_tax, true ) ) {
		$place_tax( substr( $key, 4 ) );
	} elseif ( isset( $acf_index_by_key[ $key ] ) ) {
		$place_acf( $acf_index_by_key[ $key ] );
	}
}
foreach ( $active_tax as $slug ) {
	$place_tax( $slug );
}
foreach ( array_keys( $acf_filters ) as $i ) {
	$place_acf( $i );
}
foreach ( array_keys( $all_tax ) as $slug ) {
	$place_tax( $slug );
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

/**
 * Rend une ligne de filtre taxonomie.
 */
$render_tax_row = static function ( $tax ) use ( $values, $sort_handle, $modes_labels, $logic_labels ) {
	$slug         = $tax->name;
	$checked      = in_array( $slug, (array) $values['taxonomies'], true );
	$mode         = isset( $values['taxo_modes'][ $slug ] ) ? $values['taxo_modes'][ $slug ] : 'dropdown';
	$logic        = isset( $values['taxo_logic'][ $slug ] ) ? $values['taxo_logic'][ $slug ] : 'or';
	$custom_label = isset( $values['taxo_labels'][ $slug ] ) ? $values['taxo_labels'][ $slug ] : '';
	$object_type  = implode( ',', (array) $tax->object_type );
	?>
	<div class="mrz-display-post-exp-taxo-row" data-object-types="<?php echo esc_attr( $object_type ); ?>">
		<?php echo $sort_handle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — titre déjà échappé via esc_attr__ ?>
		<input type="hidden" name="mrzdpe[filters_order][]" value="<?php echo esc_attr( 'tax:' . $slug ); ?>" />
		<label class="mrz-display-post-exp-taxo-col mrz-display-post-exp-taxo-col-activate">
			<span class="mrz-display-post-exp-filter-type"><?php esc_html_e( 'Taxonomie', 'mrz-display-post-exp' ); ?></span>
			<span class="mrz-display-post-exp-taxo-activate-row">
				<input type="checkbox" name="mrzdpe[taxonomies][]" value="<?php echo esc_attr( $slug ); ?>" <?php checked( $checked ); ?> />
				<span class="mrz-display-post-exp-taxo-name"><?php echo esc_html( $tax->labels->singular_name . ' (' . $slug . ')' ); ?></span>
			</span>
		</label>
		<label class="mrz-display-post-exp-taxo-col">
			<span><?php esc_html_e( 'Libellé affiché', 'mrz-display-post-exp' ); ?></span>
			<input type="text" name="mrzdpe[taxo_labels][<?php echo esc_attr( $slug ); ?>]" value="<?php echo esc_attr( $custom_label ); ?>" class="regular-text" placeholder="<?php echo esc_attr( $tax->labels->singular_name ); ?>" />
		</label>
		<label class="mrz-display-post-exp-taxo-col">
			<span><?php esc_html_e( 'Type de filtre', 'mrz-display-post-exp' ); ?></span>
			<select name="mrzdpe[taxo_modes][<?php echo esc_attr( $slug ); ?>]" class="mrz-display-post-exp-taxo-mode">
				<?php foreach ( $modes_labels as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $mode, $value ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</label>
		<label class="mrz-display-post-exp-taxo-col">
			<span><?php esc_html_e( 'Logique', 'mrz-display-post-exp' ); ?></span>
			<select name="mrzdpe[taxo_logic][<?php echo esc_attr( $slug ); ?>]" class="mrz-display-post-exp-taxo-logic" title="<?php esc_attr_e( 'Combinaison entre cases cochées', 'mrz-display-post-exp' ); ?>">
				<?php foreach ( $logic_labels as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $logic, $value ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</label>
	</div>
	<?php
};

/**
 * Rend une ligne de filtre ACF. $index vaut « __INDEX__ » pour le template JS.
 */
$render_acf_row = static function ( $index, $row ) use ( $sort_handle, $modes_labels, $logic_labels ) {
	$field     = isset( $row['field'] ) ? $row['field'] : '';
	$label     = isset( $row['label'] ) ? $row['label'] : '';
	$mode      = isset( $row['mode'] ) ? $row['mode'] : 'dropdown';
	$row_logic = isset( $row['logic'] ) ? $row['logic'] : 'or';
	$base_name = 'mrzdpe[acf_filters][' . $index . ']';
	?>
	<div class="mrz-display-post-exp-acf-row" data-index="<?php echo esc_attr( $index ); ?>">
		<?php echo $sort_handle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — titre déjà échappé via esc_attr__ ?>
		<input type="hidden" name="mrzdpe[filters_order][]" value="<?php echo esc_attr( 'acf:' . $index ); ?>" />
		<label class="mrz-display-post-exp-acf-col">
			<span class="mrz-display-post-exp-filter-type"><?php esc_html_e( 'Champ ACF', 'mrz-display-post-exp' ); ?></span>
			<input type="text" name="<?php echo esc_attr( $base_name . '[field]' ); ?>" value="<?php echo esc_attr( $field ); ?>" class="regular-text" placeholder="type_annonce" />
		</label>
		<label class="mrz-display-post-exp-acf-col">
			<span><?php esc_html_e( 'Libellé affiché', 'mrz-display-post-exp' ); ?></span>
			<input type="text" name="<?php echo esc_attr( $base_name . '[label]' ); ?>" value="<?php echo esc_attr( $label ); ?>" class="regular-text" placeholder="Type d'annonce" />
		</label>
		<label class="mrz-display-post-exp-acf-col">
			<span><?php esc_html_e( 'Type de filtre', 'mrz-display-post-exp' ); ?></span>
			<select name="<?php echo esc_attr( $base_name . '[mode]' ); ?>">
				<?php foreach ( $modes_labels as $value => $mlabel ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $mode, $value ); ?>>
						<?php echo esc_html( $mlabel ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</label>
		<label class="mrz-display-post-exp-acf-col">
			<span><?php esc_html_e( 'Logique', 'mrz-display-post-exp' ); ?></span>
			<select name="<?php echo esc_attr( $base_name . '[logic]' ); ?>" title="<?php esc_attr_e( 'Combinaison entre cases cochées (sans effet en mode dropdown/radio)', 'mrz-display-post-exp' ); ?>">
				<?php foreach ( $logic_labels as $value => $llabel ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $row_logic, $value ); ?>>
						<?php echo esc_html( $llabel ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</label>
		<button type="button" class="button mrz-display-post-exp-acf-remove"><?php esc_html_e( 'Retirer', 'mrz-display-post-exp' ); ?></button>
	</div>
	<?php
};
?>

<h3 class="mrz-display-post-exp-section-title"><?php esc_html_e( 'Recherche', 'mrz-display-post-exp' ); ?></h3>
<p>
	<label>
		<input type="checkbox" name="mrzdpe[search_enabled]" value="1" <?php checked( ! empty( $values['search_enabled'] ) ); ?> />
		<?php esc_html_e( 'Afficher un champ de recherche (filtrage instantané côté client).', 'mrz-display-post-exp' ); ?>
	</label>
</p>
<p>
	<label>
		<?php esc_html_e( 'Champs ACF à inclure dans la recherche :', 'mrz-display-post-exp' ); ?>
		<input type="text" name="mrzdpe[search_acf_fields]" value="<?php echo esc_attr( $values['search_acf_fields'] ); ?>" class="regular-text" placeholder="sous_titre, resume" />
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
			<input type="radio" name="mrzdpe[search_layout]" value="<?php echo esc_attr( $val ); ?>" <?php checked( $values['search_layout'], $val ); ?> />
			<?php echo esc_html( $label ); ?>
		</label>
	<?php endforeach; ?>
</p>
<p>
	<label>
		<?php esc_html_e( 'Libellé affiché au-dessus du champ :', 'mrz-display-post-exp' ); ?>
		<input type="text" name="mrzdpe[search_label]" value="<?php echo esc_attr( $values['search_label'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Rechercher', 'mrz-display-post-exp' ); ?>" />
	</label>
</p>
<p>
	<label>
		<?php esc_html_e( 'Placeholder du champ :', 'mrz-display-post-exp' ); ?>
		<input type="text" name="mrzdpe[search_placeholder]" value="<?php echo esc_attr( $values['search_placeholder'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Rechercher…', 'mrz-display-post-exp' ); ?>" />
	</label>
</p>

<hr />

<h3 class="mrz-display-post-exp-section-title"><?php esc_html_e( 'Options globales', 'mrz-display-post-exp' ); ?></h3>
<p>
	<label>
		<input type="checkbox" name="mrzdpe[show_filter_counts]" value="1" <?php checked( ! empty( $values['show_filter_counts'] ) ); ?> />
		<?php esc_html_e( 'Afficher le nombre de résultats à côté de chaque option de filtre.', 'mrz-display-post-exp' ); ?>
	</label>
</p>
<p>
	<label>
		<input type="checkbox" name="mrzdpe[url_filters_enabled]" value="1" <?php checked( ! empty( $values['url_filters_enabled'] ) ); ?> />
		<?php esc_html_e( 'Synchroniser les filtres avec l\'URL (lien partageable).', 'mrz-display-post-exp' ); ?>
	</label>
</p>

<hr />

<h3 class="mrz-display-post-exp-section-title"><?php esc_html_e( 'Filtres', 'mrz-display-post-exp' ); ?></h3>
<p class="description">
	<?php esc_html_e( 'Cochez les taxonomies et ajoutez les champs ACF à proposer en filtre, puis glissez les lignes (⠿) pour définir leur ordre d\'affichage sur le site.', 'mrz-display-post-exp' ); ?>
</p>

<div class="mrz-display-post-exp-filters-list" data-next-index="<?php echo (int) count( $acf_filters ); ?>">
	<?php
	foreach ( $filter_rows as $filter_row ) {
		if ( 'tax' === $filter_row['type'] ) {
			$render_tax_row( $filter_row['tax'] );
		} else {
			$render_acf_row( (int) $filter_row['index'], $filter_row['row'] );
		}
	}
	?>
</div>

<p>
	<button type="button" class="button mrz-display-post-exp-acf-add"><?php esc_html_e( 'Ajouter un filtre ACF', 'mrz-display-post-exp' ); ?></button>
</p>
<p class="description"><?php esc_html_e( 'Seules les taxonomies liées au post type sélectionné sont affichées. Le libellé remplace le nom de la taxonomie affiché au-dessus du filtre sur le site.', 'mrz-display-post-exp' ); ?></p>
<p class="description">
	<?php esc_html_e( 'Pour les champs Select, Radio, Checkbox ou Vrai/Faux, les options sont détectées automatiquement depuis la configuration ACF. Pour les autres types (texte, nombre), les valeurs distinctes des posts sont collectées dynamiquement.', 'mrz-display-post-exp' ); ?>
</p>

<template id="mrz-display-post-exp-acf-row-template">
	<?php $render_acf_row( '__INDEX__', array() ); ?>
</template>
