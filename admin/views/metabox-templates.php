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
				<label for="mrzdpe_tpl_item"><?php esc_html_e( 'Item template', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<textarea name="mrzdpe[tpl_item]" id="mrzdpe_tpl_item" rows="10" class="large-text code"><?php echo esc_textarea( $values['tpl_item'] ); ?></textarea>
				<p class="description"><?php esc_html_e( 'HTML rendered for each entry (list, grid or slider). The style attribute is not allowed: use your theme\'s CSS for styling.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
	</tbody>
</table>

<?php
$placeholders = array(
	array( '{post_title}', __( 'Post title.', 'mrz-display-post-exp' ) ),
	array( '{post_url}', __( 'Post link (permalink).', 'mrz-display-post-exp' ) ),
	array( '{post_excerpt}', __( 'Full excerpt.', 'mrz-display-post-exp' ) ),
	array( '{post_excerpt:25}', __( 'Excerpt trimmed to N words (25 here).', 'mrz-display-post-exp' ) ),
	array( '{post_thumbnail}', __( 'Featured image (<img> tag).', 'mrz-display-post-exp' ) ),
	array( '{post_thumbnail_url}', __( 'Featured image URL.', 'mrz-display-post-exp' ) ),
	array( '{post_id}', __( 'Post ID.', 'mrz-display-post-exp' ) ),
	array( '{post_date}', __( 'Post publish date (site date format).', 'mrz-display-post-exp' ) ),
	array( '{%nom_champ_acf%}', __( 'Value of an ACF field (replace “nom_champ_acf” with your field name).', 'mrz-display-post-exp' ) ),
	array( '{taxonomy:slug}', __( 'All terms of the taxonomy, each wrapped in a <span class="mrz-dpe-term">.', 'mrz-display-post-exp' ) ),
	array( '{taxonomy:slug:first}', __( 'Name of the first term only (plain text).', 'mrz-display-post-exp' ) ),
	array( '{taxonomy:slug:slug}', __( 'Slug of the first term (e.g. “research-development”) — handy as a CSS class modifier.', 'mrz-display-post-exp' ) ),
	array( '{acf_date:champ}', __( 'ACF date field split into 3 spans: day / month / year (see formats below).', 'mrz-display-post-exp' ) ),
);

$date_formats = array(
	array( '{acf_date:champ}', __( 'Month as a number: 08 (default).', 'mrz-display-post-exp' ) ),
	array( '{acf_date:champ:n}', __( 'Month as a number without leading zero: 8.', 'mrz-display-post-exp' ) ),
	array( '{acf_date:champ:F}', __( 'Full month name, localized: August.', 'mrz-display-post-exp' ) ),
	array( '{acf_date:champ:M}', __( 'Abbreviated month name, localized: Aug.', 'mrz-display-post-exp' ) ),
);
?>

<details class="mrz-display-post-exp-help-toggle">
<summary><?php esc_html_e( 'Available placeholders, date formats & conditionals', 'mrz-display-post-exp' ); ?></summary>

<h4 class="mrz-display-post-exp-section-title"><?php esc_html_e( 'Available placeholders', 'mrz-display-post-exp' ); ?></h4>
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

<h4 class="mrz-display-post-exp-section-title"><?php esc_html_e( 'Date formats ({acf_date})', 'mrz-display-post-exp' ); ?></h4>
<p class="description">
	<?php esc_html_e( 'The day (01) and year (2026) are fixed; only the month changes based on the suffix:', 'mrz-display-post-exp' ); ?>
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
		__( 'Each part is wrapped in its own span: <code>.mrz-dpe-day</code>, <code>.mrz-dpe-month</code>, <code>.mrz-dpe-year</code> — style them with your theme\'s CSS.', 'mrz-display-post-exp' ),
		array( 'code' => array() )
	);
	?>
</p>

<h4 class="mrz-display-post-exp-section-title"><?php esc_html_e( 'Conditionals', 'mrz-display-post-exp' ); ?></h4>
<p class="description">
	<?php
	echo wp_kses(
		__( 'Displays a block only if the field has a value: <code>{#if %mon_champ%}&lt;div&gt;...&lt;/div&gt;{/if}</code>. Also works with <code>{#if post_thumbnail}…{/if}</code> or <code>{#if taxonomy:category}…{/if}</code>.', 'mrz-display-post-exp' ),
		array( 'code' => array() )
	);
	?>
</p>
</details>
