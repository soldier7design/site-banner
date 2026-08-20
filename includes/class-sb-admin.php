<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class SB_Admin {

	protected $hook = '';

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
		add_action( 'admin_init', [ $this, 'maybe_auto_disable' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	public function add_menu() {
		$this->hook = add_menu_page(
			'Site Banner',
			'Site Banner',
			'manage_options',
			'site-banner',
			[ $this, 'render_page' ],
			'dashicons-megaphone',
			80
		);
	}

	public function enqueue( $hook ) {
		if ( $hook !== $this->hook ) return;
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'sb-admin', SB_URL . 'assets/css/admin.css', [], SB_VERSION );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'sb-admin', SB_URL . 'assets/js/admin.js', [ 'jquery', 'wp-color-picker' ], SB_VERSION, true );
	}

	/**
	 * Keeps the on/off toggle honest. If a schedule was set and the
	 * expiration date has passed, flip enabled off in the database so the
	 * admin screen reflects reality the next time someone loads it. The
	 * frontend also checks expiration independently, so visitors never see
	 * a stale banner even if nobody visits this screen right away.
	 */
	public function maybe_auto_disable() {
		$o = sb_get_settings();
		if ( empty( $o['enabled'] ) ) return;
		if ( empty( $o['schedule_enabled'] ) || empty( $o['expiration_datetime'] ) ) return;

		$exp_ts = strtotime( $o['expiration_datetime'] );
		if ( $exp_ts && current_time( 'timestamp' ) >= $exp_ts ) {
			$o['enabled'] = 0;
			update_option( 'site_banner_settings', $o );
		}
	}

	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) return;

		$notice = '';
		if ( isset( $_POST['sb_save'] ) && check_admin_referer( 'sb_save_settings', 'sb_nonce' ) ) {
			$this->save();
			$notice = 'Settings saved.';
		}

		$o             = sb_get_settings();
		$global_colors = SB_Elementor_Colors::get_global_colors();

		include SB_PATH . 'includes/views/admin-page.php';
	}

	private function save() {
		$o = sb_default_settings();

		$o['enabled']      = isset( $_POST['enabled'] ) ? 1 : 0;
		$o['title']        = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
		$o['text']         = isset( $_POST['text'] ) ? sanitize_text_field( wp_unslash( $_POST['text'] ) ) : '';
		$o['link_url']     = isset( $_POST['link_url'] ) ? esc_url_raw( wp_unslash( $_POST['link_url'] ) ) : '';
		$o['link_new_tab'] = isset( $_POST['link_new_tab'] ) ? 1 : 0;

		$o['bg_color']    = $this->sanitize_color( $_POST['bg_color'] ?? '', '#1a1a1a' );
		$o['title_color'] = $this->sanitize_color( $_POST['title_color'] ?? '', '#ffffff' );
		$o['text_color']  = $this->sanitize_color( $_POST['text_color'] ?? '', '#ffffff' );

		$o['schedule_enabled']    = isset( $_POST['schedule_enabled'] ) ? 1 : 0;
		$o['publish_mode']        = ( isset( $_POST['publish_mode'] ) && $_POST['publish_mode'] === 'scheduled' ) ? 'scheduled' : 'now';
		$o['publish_datetime']    = isset( $_POST['publish_datetime'] ) ? sanitize_text_field( wp_unslash( $_POST['publish_datetime'] ) ) : '';
		$o['expiration_datetime'] = isset( $_POST['expiration_datetime'] ) ? sanitize_text_field( wp_unslash( $_POST['expiration_datetime'] ) ) : '';
		$o['fixed_position']      = isset( $_POST['fixed_position'] ) ? 1 : 0;

		update_option( 'site_banner_settings', $o );
	}

	private function sanitize_color( $value, $fallback ) {
		$value = sanitize_text_field( wp_unslash( $value ) );
		if ( preg_match( '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $value ) ) return $value;
		if ( preg_match( '/^rgba?\([0-9,.\s%]+\)$/', $value ) ) return $value;
		return $fallback;
	}
}
