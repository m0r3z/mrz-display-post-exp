<?php
/**
 * Métabox : source des données (post type, tri, limite, pagination).
 *
 * @var array $values
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Mrzdpe\ListConfig;

$public_pts = get_post_types( array( 'public' => true ), 'objects' );

$orderby_labels = array(
	'date'       => __( 'Publish date', 'mrz-display-post-exp' ),
	'title'      => __( 'Title', 'mrz-display-post-exp' ),
	'menu_order' => __( 'Manual order (menu_order)', 'mrz-display-post-exp' ),
	'rand'       => __( 'Random', 'mrz-display-post-exp' ),
	'modified'   => __( 'Last modified date', 'mrz-display-post-exp' ),
	'acf_date'   => __( 'ACF date field (events)', 'mrz-display-post-exp' ),
);
?>
<table class="form-table mrz-display-post-exp-table">
	<tbody>
		<tr>
			<th scope="row">
				<label for="mrzdpe_source_pt"><?php esc_html_e( 'Source post type', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<select name="mrzdpe[source_pt]" id="mrzdpe_source_pt">
					<?php foreach ( $public_pts as $pt ) : ?>
						<option value="<?php echo esc_attr( $pt->name ); ?>" <?php selected( $values['source_pt'], $pt->name ); ?>>
							<?php echo esc_html( $pt->labels->singular_name . ' (' . $pt->name . ')' ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p class="description"><?php esc_html_e( 'The post type whose entries will be displayed.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="mrzdpe_orderby"><?php esc_html_e( 'Order by', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<select name="mrzdpe[orderby]" id="mrzdpe_orderby">
					<?php foreach ( ListConfig::orderbys() as $ob ) : ?>
						<option value="<?php echo esc_attr( $ob ); ?>" <?php selected( $values['orderby'], $ob ); ?>>
							<?php echo esc_html( isset( $orderby_labels[ $ob ] ) ? $orderby_labels[ $ob ] : $ob ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<select name="mrzdpe[order]" id="mrzdpe_order">
					<option value="DESC" <?php selected( $values['order'], 'DESC' ); ?>><?php esc_html_e( 'Descending', 'mrz-display-post-exp' ); ?></option>
					<option value="ASC" <?php selected( $values['order'], 'ASC' ); ?>><?php esc_html_e( 'Ascending', 'mrz-display-post-exp' ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="mrz-display-post-exp-when-acfdate">
			<th scope="row">
				<label for="mrzdpe_orderby_acf_field"><?php esc_html_e( 'ACF date field', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<input type="text" name="mrzdpe[orderby_acf_field]" id="mrzdpe_orderby_acf_field" value="<?php echo esc_attr( $values['orderby_acf_field'] ); ?>" class="regular-text" placeholder="date_evenement" />
				<p class="description"><?php esc_html_e( 'Name of the ACF Date Picker (or Date Time Picker) field used for sorting. Only entries that have this field will be displayed.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr class="mrz-display-post-exp-when-acfdate">
			<th scope="row">
				<label for="mrzdpe_acf_date_scope"><?php esc_html_e( 'Show', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<select name="mrzdpe[acf_date_scope]" id="mrzdpe_acf_date_scope">
					<option value="all" <?php selected( $values['acf_date_scope'], 'all' ); ?>><?php esc_html_e( 'All dates', 'mrz-display-post-exp' ); ?></option>
					<option value="upcoming" <?php selected( $values['acf_date_scope'], 'upcoming' ); ?>><?php esc_html_e( 'Upcoming only (≥ today)', 'mrz-display-post-exp' ); ?></option>
					<option value="past" <?php selected( $values['acf_date_scope'], 'past' ); ?>><?php esc_html_e( 'Past only (< today)', 'mrz-display-post-exp' ); ?></option>
				</select>
				<p class="description"><?php esc_html_e( 'Server-side filter on the date field above. “Upcoming” hides past events (ideal for an events calendar).', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="mrzdpe_limit"><?php esc_html_e( 'Maximum number of posts', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<input type="number" name="mrzdpe[limit]" id="mrzdpe_limit" value="<?php echo esc_attr( $values['limit'] ); ?>" min="0" step="1" />
				<p class="description"><?php esc_html_e( 'Server-side loading limit. 0 = unlimited.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="mrzdpe_per_page"><?php esc_html_e( 'Posts per page', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<input type="number" name="mrzdpe[per_page]" id="mrzdpe_per_page" value="<?php echo esc_attr( $values['per_page'] ); ?>" min="0" step="1" />
				<p class="description"><?php esc_html_e( 'Client-side pagination (list and grid): number of posts per page. 0 = show all. Ignored in slider mode.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Current context', 'mrz-display-post-exp' ); ?></th>
			<td>
				<label>
					<input type="checkbox" class="mrz-display-post-exp-current-term-toggle" name="mrzdpe[restrict_current_term]" value="1" <?php checked( ! empty( $values['restrict_current_term'] ) ); ?> />
					<?php esc_html_e( 'Only show content sharing the category of the current page (single post or category archive).', 'mrz-display-post-exp' ); ?>
				</label>
				<p class="description"><?php esc_html_e( 'Ideal for a “related content” block placed in a post: on a single post, the current item is excluded. Without a category context (e.g. a page), all content is displayed.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr class="mrz-display-post-exp-when-current-term">
			<th scope="row">
				<label for="mrzdpe_current_term_taxonomy"><?php esc_html_e( 'Context taxonomy', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<select name="mrzdpe[current_term_taxonomy]" id="mrzdpe_current_term_taxonomy">
					<?php foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $tx ) : ?>
						<option value="<?php echo esc_attr( $tx->name ); ?>" <?php selected( $values['current_term_taxonomy'], $tx->name ); ?>>
							<?php echo esc_html( $tx->labels->singular_name . ' (' . $tx->name . ')' ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p class="description"><?php esc_html_e( 'Taxonomy shared by the current page and the displayed content (default: Category).', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
	</tbody>
</table>
