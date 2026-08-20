<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap sb-wrap">
	<h1>Site Banner</h1>
	<p class="description">Enable a sitewide banner for important notifications.</p>

	<?php if ( $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
	<?php endif; ?>

	<form method="post" class="sb-form">
		<?php wp_nonce_field( 'sb_save_settings', 'sb_nonce' ); ?>

		<div class="sb-card">
			<label class="sb-toggle-row">
				<span class="sb-toggle">
					<input type="checkbox" name="enabled" id="sb-enabled" value="1" <?php checked( $o['enabled'], 1 ); ?> />
					<span class="sb-toggle-slider"></span>
				</span>
				<span class="sb-toggle-label">Banner is <strong><?php echo $o['enabled'] ? 'ON' : 'OFF'; ?></strong></span>
			</label>
		</div>

		<div class="sb-card">
			<h2>Content</h2>
			<table class="form-table">
				<tr>
					<th><label for="sb-title">Title</label></th>
					<td><input type="text" id="sb-title" name="title" value="<?php echo esc_attr( $o['title'] ); ?>" class="regular-text" placeholder="e.g. Notice" /></td>
				</tr>
				<tr>
					<th><label for="sb-text">Text</label></th>
					<td>
						<input type="text" id="sb-text" name="text" value="<?php echo esc_attr( $o['text'] ); ?>" class="regular-text" placeholder="Keep this to one short line" />
						<p class="description">Shown after the title, separated by a hyphen.</p>
					</td>
				</tr>
				<tr>
					<th><label for="sb-link">Link (optional)</label></th>
					<td>
						<input type="url" id="sb-link" name="link_url" value="<?php echo esc_attr( $o['link_url'] ); ?>" class="regular-text" placeholder="https://" />
						<label style="display:block;margin-top:8px;">
							<input type="checkbox" name="link_new_tab" value="1" <?php checked( $o['link_new_tab'], 1 ); ?> />
							Open link in a new tab
						</label>
						<p class="description">When a link is set, the whole banner becomes clickable and shows an arrow on the right.</p>
					</td>
				</tr>
			</table>
		</div>

		<div class="sb-card">
			<h2>Colors</h2>
			<?php
			$color_fields = [
				'bg_color'    => [ 'label' => 'Background Color', 'value' => $o['bg_color'] ],
				'title_color' => [ 'label' => 'Title Color', 'value' => $o['title_color'] ],
				'text_color'  => [ 'label' => 'Text Color', 'value' => $o['text_color'] ],
			];
			foreach ( $color_fields as $field_name => $field ) :
			?>
				<div class="sb-color-field">
					<label class="sb-color-label"><?php echo esc_html( $field['label'] ); ?></label>
					<div class="sb-swatches" data-field="<?php echo esc_attr( $field_name ); ?>">
						<?php foreach ( $global_colors as $c ) : ?>
							<button type="button" class="sb-swatch" style="background:<?php echo esc_attr( $c['color'] ); ?>" data-color="<?php echo esc_attr( $c['color'] ); ?>" title="<?php echo esc_attr( $c['title'] ); ?>"></button>
						<?php endforeach; ?>
						<button type="button" class="sb-swatch sb-swatch-other" data-color="other" title="Other">+</button>
					</div>
					<input type="text" class="sb-color-picker" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>" />
				</div>
			<?php endforeach; ?>
			<?php if ( empty( $global_colors ) ) : ?>
				<p class="description">No Elementor global colors were found on this site. Use the color picker for each field above.</p>
			<?php endif; ?>
		</div>

		<div class="sb-card">
			<h2>Positioning</h2>
			<label>
				<input type="checkbox" name="fixed_position" value="1" <?php checked( $o['fixed_position'], 1 ); ?> />
				Fix the banner to the top of the page
			</label>
			<p class="description">When on, the banner (and the small indicator that replaces it once closed) stays pinned to the top of the screen as visitors scroll, and pushes the site's own fixed header down out of the way. When off, it sits with the page content and scrolls away normally.</p>
		</div>

		<div class="sb-card">
			<h2>Display Schedule</h2>
			<label>
				<input type="checkbox" id="sb-schedule-enabled" name="schedule_enabled" value="1" <?php checked( $o['schedule_enabled'], 1 ); ?> />
				Set up a timed display
			</label>

			<div id="sb-schedule-fields" class="sb-schedule-fields" style="<?php echo $o['schedule_enabled'] ? '' : 'display:none;'; ?>">
				<table class="form-table">
					<tr>
						<th>Publish</th>
						<td>
							<label style="margin-right:16px;">
								<input type="radio" name="publish_mode" value="now" <?php checked( $o['publish_mode'], 'now' ); ?> />
								Now
							</label>
							<label>
								<input type="radio" name="publish_mode" value="scheduled" <?php checked( $o['publish_mode'], 'scheduled' ); ?> />
								Scheduled
							</label>
							<div id="sb-publish-datetime-row" style="margin-top:8px; <?php echo $o['publish_mode'] === 'scheduled' ? '' : 'display:none;'; ?>">
								<input type="datetime-local" name="publish_datetime" value="<?php echo esc_attr( $o['publish_datetime'] ); ?>" />
								<p class="description">Banner turns on at this date and time.</p>
							</div>
						</td>
					</tr>
					<tr>
						<th><label for="sb-expiration">Expiration date</label></th>
						<td>
							<input type="date" id="sb-expiration" name="expiration_datetime" value="<?php echo esc_attr( $o['expiration_datetime'] ); ?>" />
							<p class="description">At midnight on this date, the banner turns off automatically and the toggle above switches to OFF.</p>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<p class="submit">
			<button type="submit" name="sb_save" value="1" class="button button-primary button-hero">Save Settings</button>
		</p>
	</form>
</div>
