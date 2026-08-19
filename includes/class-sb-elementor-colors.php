<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Pulls the site's Elementor global color palette (system + custom colors)
 * so the admin page can offer them as one-click swatches. Returns an empty
 * array gracefully if Elementor isn't installed or no kit is active.
 */
class SB_Elementor_Colors {

	public static function get_global_colors() {
		$colors = [];

		if ( ! did_action( 'elementor/loaded' ) && ! class_exists( '\Elementor\Plugin' ) ) {
			return $colors;
		}

		$kit_id = get_option( 'elementor_active_kit' );
		if ( ! $kit_id ) return $colors;

		$settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
		if ( ! is_array( $settings ) ) return $colors;

		foreach ( [ 'system_colors', 'custom_colors' ] as $group ) {
			if ( empty( $settings[ $group ] ) || ! is_array( $settings[ $group ] ) ) continue;
			foreach ( $settings[ $group ] as $c ) {
				if ( ! empty( $c['color'] ) ) {
					$colors[] = [
						'title' => ! empty( $c['title'] ) ? $c['title'] : '',
						'color' => $c['color'],
					];
				}
			}
		}

		return $colors;
	}
}
