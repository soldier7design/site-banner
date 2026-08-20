<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class SB_Frontend {

	protected $rendered = false;

	public function __construct() {
		add_action( 'wp_body_open', [ $this, 'render' ] );
		add_action( 'wp_footer', [ $this, 'render_fallback' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	public function should_display() {
		$o = sb_get_settings();
		if ( empty( $o['enabled'] ) ) return false;
		if ( trim( $o['title'] ) === '' && trim( $o['text'] ) === '' ) return false;

		if ( ! empty( $o['schedule_enabled'] ) ) {
			$now = current_time( 'timestamp' );

			if ( $o['publish_mode'] === 'scheduled' && ! empty( $o['publish_datetime'] ) ) {
				$publish_ts = strtotime( $o['publish_datetime'] );
				if ( $publish_ts && $now < $publish_ts ) return false;
			}

			if ( ! empty( $o['expiration_datetime'] ) ) {
				$exp_ts = strtotime( $o['expiration_datetime'] );
				if ( $exp_ts && $now >= $exp_ts ) return false;
			}
		}

		return true;
	}

	public function enqueue() {
		if ( ! $this->should_display() ) return;

		wp_enqueue_style( 'sb-frontend', SB_URL . 'assets/css/frontend.css', [], SB_VERSION );
		wp_enqueue_script( 'sb-frontend', SB_URL . 'assets/js/frontend.js', [], SB_VERSION, true );

		$o    = sb_get_settings();
		$hash = substr( md5( $o['title'] . '|' . $o['text'] . '|' . $o['link_url'] . '|' . $o['bg_color'] ), 0, 10 );

		wp_localize_script( 'sb-frontend', 'SiteBannerData', [
			'cookieName' => 'sb_dismissed_' . $hash,
		] );
	}

	// Primary hook. Most themes since WP 5.2 call wp_body_open right after <body>.
	public function render() {
		$this->rendered = true;
		$this->output();
	}

	// Fallback for older/unsupported themes so the banner still shows up somewhere.
	public function render_fallback() {
		if ( $this->rendered ) return;
		$this->output();
	}

	protected function output() {
		if ( ! $this->should_display() ) return;

		$o = sb_get_settings();

		$has_link = ! empty( $o['link_url'] );
		$tag      = $has_link ? 'a' : 'div';
		$attrs    = '';
		if ( $has_link ) {
			$attrs .= ' href="' . esc_url( $o['link_url'] ) . '"';
			if ( ! empty( $o['link_new_tab'] ) ) {
				$attrs .= ' target="_blank" rel="noopener noreferrer"';
			}
		}
		?>
		<div id="sb-banner" class="sb-banner<?php echo $has_link ? ' sb-has-link' : ''; ?>" style="background:<?php echo esc_attr( $o['bg_color'] ); ?>;" role="region" aria-label="Site notification">
			<<?php echo $tag . $attrs; ?> class="sb-banner-inner">
				<span class="sb-banner-content">
					<?php if ( $o['title'] ) : ?>
						<span class="sb-banner-title" style="color:<?php echo esc_attr( $o['title_color'] ); ?>;"><?php echo esc_html( $o['title'] ); ?></span>
					<?php endif; ?>
					<?php if ( $o['title'] && $o['text'] ) : ?>
						<span class="sb-banner-sep" style="color:<?php echo esc_attr( $o['text_color'] ); ?>;"> - </span>
					<?php endif; ?>
					<?php if ( $o['text'] ) : ?>
						<span class="sb-banner-text" style="color:<?php echo esc_attr( $o['text_color'] ); ?>;"><?php echo esc_html( $o['text'] ); ?></span>
					<?php endif; ?>
					<?php if ( $has_link ) : ?>
						<span class="sb-banner-arrow" style="color:<?php echo esc_attr( $o['text_color'] ); ?>;" aria-hidden="true"><?php echo $this->arrow_svg(); ?></span>
					<?php endif; ?>
				</span>
			</<?php echo $tag; ?>>
			<div class="sb-banner-close" role="button" tabindex="0" aria-label="Close notification" style="color:<?php echo esc_attr( $o['text_color'] ); ?>;">&times;</div>
		</div>
		<div id="sb-reopen" class="sb-reopen" role="button" tabindex="0" aria-label="Show notification" style="background:<?php echo esc_attr( $o['bg_color'] ); ?>; color:<?php echo esc_attr( $o['text_color'] ); ?>;">
			<?php echo $this->megaphone_svg(); ?>
		</div>
		<?php
	}

	protected function arrow_svg() {
		return '<svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M4 12H20M20 12L14 6M20 12L14 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	}

	protected function megaphone_svg() {
		return '<svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 10v4a1 1 0 0 0 1 1h2l9 5V4l-9 5H4a1 1 0 0 0-1 1z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 15v4a2 2 0 0 1-4 0v-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 8a5 5 0 0 1 0 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	}
}
