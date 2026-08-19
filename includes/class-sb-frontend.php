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
						<span class="sb-banner-arrow" style="color:<?php echo esc_attr( $o['text_color'] ); ?>;" aria-hidden="true">&#8594;</span>
					<?php endif; ?>
				</span>
			</<?php echo $tag; ?>>
			<button type="button" class="sb-banner-close" aria-label="Close notification" style="color:<?php echo esc_attr( $o['text_color'] ); ?>;">&times;</button>
		</div>
		<button type="button" id="sb-reopen" class="sb-reopen" style="background:<?php echo esc_attr( $o['bg_color'] ); ?>; color:<?php echo esc_attr( $o['text_color'] ); ?>;" aria-label="Show notification">!</button>
		<?php
	}
}
