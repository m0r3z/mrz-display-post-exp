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
			<th scope="row"><?php esc_html_e( 'Display format', 'mrz-display-post-exp' ); ?></th>
			<td>
				<?php
				$formats = array(
					'list'   => __( 'List', 'mrz-display-post-exp' ),
					'grid'   => __( 'Grid', 'mrz-display-post-exp' ),
					'slider' => __( 'Slider', 'mrz-display-post-exp' ),
				);
				foreach ( $formats as $val => $label ) :
					?>
					<label style="margin-right:16px;">
						<input type="radio" name="mrzdpe[format]" class="mrz-display-post-exp-format-input" value="<?php echo esc_attr( $val ); ?>" <?php checked( $values['format'], $val ); ?> />
						<?php echo esc_html( $label ); ?>
					</label>
				<?php endforeach; ?>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="mrzdpe_thumb_ratio"><?php esc_html_e( 'Featured image aspect ratio', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<select name="mrzdpe[thumb_ratio]" id="mrzdpe_thumb_ratio">
					<?php
					$ratio_labels = array(
						'auto'  => __( 'Auto (original image)', 'mrz-display-post-exp' ),
						'1/1'   => __( 'Square (1:1)', 'mrz-display-post-exp' ),
						'4/3'   => __( 'Landscape 4:3', 'mrz-display-post-exp' ),
						'3/2'   => __( 'Landscape 3:2', 'mrz-display-post-exp' ),
						'16/9'  => __( 'Landscape 16:9', 'mrz-display-post-exp' ),
						'3/4'   => __( 'Portrait 3:4', 'mrz-display-post-exp' ),
						'2/3'   => __( 'Portrait 2:3', 'mrz-display-post-exp' ),
					);
					foreach ( $ratio_labels as $val => $label ) :
						?>
						<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $values['thumb_ratio'], $val ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p class="description"><?php esc_html_e( 'Applies to the featured image ({post_thumbnail}). The image fills the area and is cleanly cropped (object-fit) without distortion and without altering the file.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>

		<tr class="mrz-display-post-exp-when-grid">
			<th scope="row">
				<label for="mrzdpe_grid_min_width"><?php esc_html_e( 'Minimum column width (px)', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<input type="number" name="mrzdpe[grid_min_width]" id="mrzdpe_grid_min_width" value="<?php echo esc_attr( $values['grid_min_width'] ); ?>" min="120" max="600" step="10" />
				<p class="description"><?php esc_html_e( 'The grid automatically fits as many columns as possible based on this minimum width.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr class="mrz-display-post-exp-when-grid">
			<th scope="row">
				<label for="mrzdpe_grid_gap"><?php esc_html_e( 'Gap between items (px)', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<input type="number" name="mrzdpe[grid_gap]" id="mrzdpe_grid_gap" value="<?php echo esc_attr( $values['grid_gap'] ); ?>" min="0" max="64" step="1" />
				<p class="description"><?php esc_html_e( 'Horizontal and vertical spacing between grid items.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>

		<tr class="mrz-display-post-exp-when-slider">
			<th scope="row"><?php esc_html_e( 'Visible items (slider)', 'mrz-display-post-exp' ); ?></th>
			<td>
				<label style="margin-right:12px;">
					<?php esc_html_e( 'Desktop:', 'mrz-display-post-exp' ); ?>
					<input type="number" name="mrzdpe[slider_per_view]" value="<?php echo esc_attr( $values['slider_per_view'] ); ?>" min="1" max="8" step="1" class="small-text" />
				</label>
				<label style="margin-right:12px;">
					<?php esc_html_e( 'Tablet:', 'mrz-display-post-exp' ); ?>
					<input type="number" name="mrzdpe[slider_per_view_tablet]" value="<?php echo esc_attr( $values['slider_per_view_tablet'] ); ?>" min="1" max="8" step="1" class="small-text" />
				</label>
				<label>
					<?php esc_html_e( 'Mobile:', 'mrz-display-post-exp' ); ?>
					<input type="number" name="mrzdpe[slider_per_view_mobile]" value="<?php echo esc_attr( $values['slider_per_view_mobile'] ); ?>" min="1" max="4" step="1" class="small-text" />
				</label>
				<p class="description"><?php esc_html_e( 'Desktop > 1024px, tablet ≤ 1024px, mobile ≤ 768px.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr class="mrz-display-post-exp-when-slider">
			<th scope="row">
				<label for="mrzdpe_slider_gap"><?php esc_html_e( 'Gap between items (px)', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<input type="number" name="mrzdpe[slider_gap]" id="mrzdpe_slider_gap" value="<?php echo esc_attr( $values['slider_gap'] ); ?>" min="0" max="64" step="1" />
			</td>
		</tr>
		<tr class="mrz-display-post-exp-when-slider">
			<th scope="row">
				<label for="mrzdpe_slider_speed"><?php esc_html_e( 'Transition speed (ms)', 'mrz-display-post-exp' ); ?></label>
			</th>
			<td>
				<input type="number" name="mrzdpe[slider_speed]" id="mrzdpe_slider_speed" value="<?php echo esc_attr( $values['slider_speed'] ); ?>" min="100" max="2000" step="50" />
				<p class="description"><?php esc_html_e( 'Duration of the slide animation when clicking the arrows / dots and during autoplay. The higher the value, the slower and smoother the transition (default: 500 ms). Touch swiping remains native.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr class="mrz-display-post-exp-when-slider">
			<th scope="row"><?php esc_html_e( 'Slider options', 'mrz-display-post-exp' ); ?></th>
			<td>
				<p>
					<label>
						<input type="checkbox" name="mrzdpe[slider_show_arrows]" value="1" <?php checked( ! empty( $values['slider_show_arrows'] ) ); ?> />
						<?php esc_html_e( 'Show previous / next arrows.', 'mrz-display-post-exp' ); ?>
					</label>
				</p>
				<p>
					<?php esc_html_e( 'Arrow position:', 'mrz-display-post-exp' ); ?>
					<?php
					$arrow_positions = array(
						'sides' => __( 'On the sides', 'mrz-display-post-exp' ),
						'top'   => __( 'Above (left-aligned)', 'mrz-display-post-exp' ),
					);
					foreach ( $arrow_positions as $val => $label ) :
						?>
						<label style="margin-right:16px;">
							<input type="radio" name="mrzdpe[slider_arrows_position]" value="<?php echo esc_attr( $val ); ?>" <?php checked( $values['slider_arrows_position'], $val ); ?> />
							<?php echo esc_html( $label ); ?>
						</label>
					<?php endforeach; ?>
				</p>
				<p>
					<label>
						<input type="checkbox" name="mrzdpe[slider_show_dots]" value="1" <?php checked( ! empty( $values['slider_show_dots'] ) ); ?> />
						<?php esc_html_e( 'Show pagination dots.', 'mrz-display-post-exp' ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" name="mrzdpe[slider_loop]" value="1" <?php checked( ! empty( $values['slider_loop'] ) ); ?> />
						<?php esc_html_e( 'Loop (go back to the start after the last item).', 'mrz-display-post-exp' ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" name="mrzdpe[slider_autoplay]" value="1" <?php checked( ! empty( $values['slider_autoplay'] ) ); ?> />
						<?php esc_html_e( 'Autoplay.', 'mrz-display-post-exp' ); ?>
					</label>
				</p>
				<p>
					<label>
						<?php esc_html_e( 'Delay between transitions (ms):', 'mrz-display-post-exp' ); ?>
						<input type="number" name="mrzdpe[slider_autoplay_delay]" value="<?php echo esc_attr( $values['slider_autoplay_delay'] ); ?>" min="1000" max="15000" step="500" />
					</label>
					<br />
					<span class="description"><?php esc_html_e( 'Autoplay respects the visitor\'s “reduce motion” preference and pauses on hover.', 'mrz-display-post-exp' ); ?></span>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row"><?php esc_html_e( 'Filters layout', 'mrz-display-post-exp' ); ?></th>
			<td>
				<?php
				$filter_layouts = array(
					'above'      => __( 'Above', 'mrz-display-post-exp' ),
					'side-left'  => __( 'Left', 'mrz-display-post-exp' ),
					'side-right' => __( 'Right', 'mrz-display-post-exp' ),
				);
				foreach ( $filter_layouts as $val => $label ) :
					?>
					<label style="margin-right:16px;">
						<input type="radio" name="mrzdpe[layout_filters]" value="<?php echo esc_attr( $val ); ?>" <?php checked( $values['layout_filters'], $val ); ?> />
						<?php echo esc_html( $label ); ?>
					</label>
				<?php endforeach; ?>
				<p class="description"><?php esc_html_e( 'Only applies when filters or search are enabled.', 'mrz-display-post-exp' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'On item click', 'mrz-display-post-exp' ); ?></th>
			<td>
				<?php
				$click_actions = array(
					'none' => __( 'Do nothing', 'mrz-display-post-exp' ),
					'link' => __( 'Open the post page', 'mrz-display-post-exp' ),
				);
				foreach ( $click_actions as $val => $label ) :
					?>
					<label style="display:block;margin-bottom:4px;">
						<input type="radio" name="mrzdpe[item_click_action]" value="<?php echo esc_attr( $val ); ?>" <?php checked( $values['item_click_action'], $val ); ?> />
						<?php echo esc_html( $label ); ?>
					</label>
				<?php endforeach; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Reset button', 'mrz-display-post-exp' ); ?></th>
			<td>
				<p>
					<label>
						<input type="checkbox" name="mrzdpe[show_clear_btn]" value="1" <?php checked( ! empty( $values['show_clear_btn'] ) ); ?> />
						<?php esc_html_e( 'Show a button that resets all filters.', 'mrz-display-post-exp' ); ?>
					</label>
				</p>
				<p>
					<label>
						<?php esc_html_e( 'Button text:', 'mrz-display-post-exp' ); ?>
						<input type="text" name="mrzdpe[clear_btn_text]" value="<?php echo esc_attr( $values['clear_btn_text'] ); ?>" placeholder="<?php esc_attr_e( 'Clear', 'mrz-display-post-exp' ); ?>" class="regular-text" />
					</label>
				</p>
			</td>
		</tr>
	</tbody>
</table>
