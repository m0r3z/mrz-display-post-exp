<?php
/**
 * Wrapper HTML du shortcode [mrz_display_post_exp].
 *
 * @var string $uid
 * @var array  $data
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$config   = $data['config'];
$filters  = $data['filters'];
$forced   = isset( $data['forced'] ) ? $data['forced'] : null;
$layout_f = $config['layoutFilters'];
$format   = $config['format'];

$search_enabled = ! empty( $config['search']['enabled'] );
$search_layout  = isset( $config['search']['layout'] ) ? (string) $config['search']['layout'] : 'inline';
$search_top     = $search_enabled && 'top' === $search_layout;
$search_inline  = $search_enabled && ! $search_top;

$wrapper_cls = sprintf(
	'mrz-display-post-exp-wrapper mrz-display-post-exp-filters-%s mrz-display-post-exp-fmt-%s%s',
	$layout_f,
	$format,
	$search_top ? ' mrz-display-post-exp-has-search-top' : ''
);

/**
 * Retourne la clé unique d'un filtre (taxonomie ou champ ACF).
 */
$filter_key = static function ( $filter ) {
	return 'acf' === $filter['type']
		? 'acf:' . $filter['field']
		: 'tax:' . $filter['taxonomy'];
};

/**
 * Retourne les attributs data-* communs à un input de filtre.
 */
$filter_data_attrs = static function ( $filter ) {
	if ( 'acf' === $filter['type'] ) {
		return 'data-filter-type="acf" data-field="' . esc_attr( $filter['field'] ) . '"';
	}
	return 'data-filter-type="tax" data-taxonomy="' . esc_attr( $filter['taxonomy'] ) . '"';
};

$show_counts = ! empty( $config['showFilterCounts'] );

/**
 * Retourne le libellé affiché pour une option de filtre (avec ou sans compteur).
 */
$format_option = static function ( $opt ) use ( $show_counts ) {
	return $show_counts
		? $opt['name'] . ' (' . $opt['count'] . ')'
		: $opt['name'];
};

$dropdown_id = $uid . '-search-dropdown';

/**
 * Rend le champ de recherche — réutilisé en mode inline et top.
 */
$render_search_field = static function () use ( $config, $dropdown_id ) {
	?>
	<div class="mrz-display-post-exp-search-wrapper">
		<input type="text"
			class="mrz-display-post-exp-search"
			aria-label="<?php echo esc_attr( $config['search']['label'] ); ?>"
			autocomplete="off"
			placeholder="<?php echo esc_attr( $config['search']['placeholder'] ); ?>" />
	</div>
	<?php
};
?>
<div id="<?php echo esc_attr( $uid ); ?>" class="<?php echo esc_attr( $wrapper_cls ); ?>" data-mrz-display-post-exp="1">

	<?php if ( $search_top ) : ?>
		<div class="mrz-display-post-exp-search-top">
			<?php if ( '' !== (string) $config['search']['label'] ) : ?>
				<div class="mrz-display-post-exp-filter-label"><?php echo esc_html( $config['search']['label'] ); ?></div>
			<?php endif; ?>
			<?php $render_search_field(); ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $filters ) || $search_inline ) : ?>
		<div class="mrz-display-post-exp-filters">

			<?php if ( $search_inline ) : ?>
				<div class="mrz-display-post-exp-filter mrz-display-post-exp-filter-search">
					<div class="mrz-display-post-exp-filter-label"><?php echo esc_html( $config['search']['label'] ); ?></div>
					<?php $render_search_field(); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $filters ) || ! empty( $config['showClearBtn'] ) ) : ?>
				<button type="button" class="mrz-display-post-exp-filters-toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $uid ); ?>-filters-body">
					<?php esc_html_e( 'Filtres', 'mrz-display-post-exp' ); ?>
				</button>
			<?php endif; ?>

			<div class="mrz-display-post-exp-filters-body" id="<?php echo esc_attr( $uid ); ?>-filters-body">

			<?php foreach ( $filters as $filter ) : ?>
				<?php
				// Masquer le filtre forcé via shortcode si demandé.
				$hide_this = false;
				if ( $forced && 'tax' === $filter['type'] && $forced['taxonomy'] === $filter['taxonomy'] && $forced['hide'] ) {
					$hide_this = true;
				}
				if ( $hide_this ) {
					continue;
				}
				$fkey  = $filter_key( $filter );
				$dattr = $filter_data_attrs( $filter );
				?>
				<div class="mrz-display-post-exp-filter mrz-display-post-exp-filter-<?php echo esc_attr( $filter['mode'] ); ?>" data-filter-key="<?php echo esc_attr( $fkey ); ?>">
					<div class="mrz-display-post-exp-filter-label"><?php echo esc_html( $filter['label'] ); ?></div>

					<?php if ( 'dropdown' === $filter['mode'] ) : ?>
						<select class="mrz-display-post-exp-filter-input" <?php echo $dattr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — attributs déjà échappés ?>>
							<option value=""><?php esc_html_e( 'Tous', 'mrz-display-post-exp' ); ?></option>
							<?php foreach ( $filter['options'] as $opt ) : ?>
								<?php
								$is_forced = ( $forced && 'tax' === $filter['type']
									&& $forced['taxonomy'] === $filter['taxonomy']
									&& (int) $forced['term'] === (int) $opt['id'] );
								?>
								<option value="<?php echo esc_attr( $opt['id'] ); ?>" <?php selected( $is_forced ); ?>>
									<?php echo esc_html( $format_option( $opt ) ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					<?php elseif ( 'radio' === $filter['mode'] ) : ?>
						<div class="mrz-display-post-exp-filter-group">
							<label>
								<input type="radio" name="<?php echo esc_attr( $uid . '-' . $fkey ); ?>" class="mrz-display-post-exp-filter-input" <?php echo $dattr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> value="" <?php checked( ! $forced || 'tax' !== $filter['type'] || $forced['taxonomy'] !== $filter['taxonomy'] ); ?> />
								<?php esc_html_e( 'Tous', 'mrz-display-post-exp' ); ?>
							</label>
							<?php foreach ( $filter['options'] as $opt ) : ?>
								<?php
								$is_forced = ( $forced && 'tax' === $filter['type']
									&& $forced['taxonomy'] === $filter['taxonomy']
									&& (int) $forced['term'] === (int) $opt['id'] );
								?>
								<label>
									<input type="radio" name="<?php echo esc_attr( $uid . '-' . $fkey ); ?>" class="mrz-display-post-exp-filter-input" <?php echo $dattr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> value="<?php echo esc_attr( $opt['id'] ); ?>" <?php checked( $is_forced ); ?> />
									<?php echo esc_html( $format_option( $opt ) ); ?>
								</label>
							<?php endforeach; ?>
						</div>
					<?php else : // checkbox ?>
						<div class="mrz-display-post-exp-filter-group">
							<?php foreach ( $filter['options'] as $opt ) : ?>
								<?php
								$is_forced = ( $forced && 'tax' === $filter['type']
									&& $forced['taxonomy'] === $filter['taxonomy']
									&& (int) $forced['term'] === (int) $opt['id'] );
								?>
								<label>
									<input type="checkbox" class="mrz-display-post-exp-filter-input" <?php echo $dattr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> value="<?php echo esc_attr( $opt['id'] ); ?>" <?php checked( $is_forced ); ?> />
									<?php echo esc_html( $format_option( $opt ) ); ?>
								</label>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>

			<?php if ( ! empty( $config['showClearBtn'] ) ) : ?>
				<div class="mrz-display-post-exp-filter mrz-display-post-exp-filter-reset">
					<button type="button" class="mrz-display-post-exp-search-clear"><?php echo esc_html( $config['clearBtnText'] ); ?></button>
				</div>
			<?php endif; ?>

			</div><!-- /.mrz-display-post-exp-filters-body -->

		</div>
	<?php endif; ?>

	<div class="mrz-display-post-exp-main">
		<?php if ( 'slider' === $format ) : ?>
			<?php $s = $config['slider']; ?>
			<div class="mrz-display-post-exp-slider"
				data-per-view="<?php echo (int) $s['perView']; ?>"
				data-per-view-tablet="<?php echo (int) $s['perViewTablet']; ?>"
				data-per-view-mobile="<?php echo (int) $s['perViewMobile']; ?>"
				data-gap="<?php echo (int) $s['gap']; ?>"
				data-loop="<?php echo $s['loop'] ? '1' : '0'; ?>"
				data-autoplay="<?php echo $s['autoplay'] ? '1' : '0'; ?>"
				data-autoplay-delay="<?php echo (int) $s['autoplayDelay']; ?>">
				<?php if ( ! empty( $s['showArrows'] ) ) : ?>
					<button type="button" class="mrz-display-post-exp-slider-prev" aria-label="<?php esc_attr_e( 'Précédent', 'mrz-display-post-exp' ); ?>">&lsaquo;</button>
					<button type="button" class="mrz-display-post-exp-slider-next" aria-label="<?php esc_attr_e( 'Suivant', 'mrz-display-post-exp' ); ?>">&rsaquo;</button>
				<?php endif; ?>
				<div class="mrz-display-post-exp-track mrz-display-post-exp-list" tabindex="0"></div>
				<?php if ( ! empty( $s['showDots'] ) ) : ?>
					<div class="mrz-display-post-exp-dots" role="tablist"></div>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<div class="mrz-display-post-exp-list-wrap">
				<?php
				$list_style = '';
				if ( 'grid' === $format ) {
					$list_style = ' style="--mrz-grid-min:' . (int) $config['grid']['minWidth'] . 'px;"';
				}
				?>
				<div class="mrz-display-post-exp-list"<?php echo $list_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — valeur entière interpolée ?>></div>
				<?php if ( (int) $config['perPage'] > 0 ) : ?>
					<nav class="mrz-display-post-exp-pagination" hidden>
						<button type="button" class="mrz-display-post-exp-page-prev" aria-label="<?php esc_attr_e( 'Page précédente', 'mrz-display-post-exp' ); ?>">&lsaquo;</button>
						<span class="mrz-display-post-exp-page-info">
							<span class="mrz-display-post-exp-page-current">1</span>
							 /
							<span class="mrz-display-post-exp-page-total">1</span>
						</span>
						<button type="button" class="mrz-display-post-exp-page-next" aria-label="<?php esc_attr_e( 'Page suivante', 'mrz-display-post-exp' ); ?>">&rsaquo;</button>
					</nav>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>

	<script type="application/json" class="mrz-display-post-exp-data"><?php echo wp_json_encode( $data ); ?></script>
</div>
