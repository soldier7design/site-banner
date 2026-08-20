<?php
/**
 * Plugin Name: Site Banner
 * Description: Enable a sitewide banner for important notifications.
 * Version: 1.0.5
 * Author: Ben Coyour Design
 * License: GPL v2 or later
 * Text Domain: site-banner
 */

if ( ! defined( 'ABSPATH' ) ) exit;

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

define( 'SB_VERSION', '1.0.5' );
define( 'SB_PATH', plugin_dir_path( __FILE__ ) );
define( 'SB_URL', plugin_dir_url( __FILE__ ) );

require_once SB_PATH . 'includes/plugin-update-checker/plugin-update-checker.php';
require_once SB_PATH . 'includes/class-sb-elementor-colors.php';
require_once SB_PATH . 'includes/class-sb-admin.php';
require_once SB_PATH . 'includes/class-sb-frontend.php';

/**
 * Update source. Swap 'yourusername/site-banner' for your real GitHub repo.
 * As long as this repo has a public "Releases" tag matching the Version
 * number in this file's header (e.g. tag "1.0.1" for Version: 1.0.1),
 * every site running this plugin will see an update in wp-admin.
 */
$sbUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/soldier7design/site-banner/',
	__FILE__,
	'site-banner'
);
// Point at the "main" branch's releases/tags. If you rename your default
// branch, update this to match.
$sbUpdateChecker->setBranch( 'main' );

/**
 * Private repo authentication. Define SB_GH_TOKEN in each site's
 * wp-config.php (never commit a real token into this file, since this file
 * itself lives in the repo). One token works across every site:
 *
 *   define( 'SB_GH_TOKEN', 'github_pat_xxxxxxxxxxxxxxxxxxxx' );
 */
if ( defined( 'SB_GH_TOKEN' ) && SB_GH_TOKEN ) {
	$sbUpdateChecker->setAuthentication( SB_GH_TOKEN );
}

/**
 * Default settings shape. Every setting the plugin knows about lives here
 * so the rest of the code can always assume a complete array.
 */
function sb_default_settings() {
	return [
		'enabled'             => 0,
		'title'               => '',
		'text'                => '',
		'link_url'            => '',
		'link_new_tab'        => 0,
		'bg_color'            => '#1a1a1a',
		'title_color'         => '#ffffff',
		'text_color'          => '#ffffff',
		'schedule_enabled'    => 0,
		'publish_mode'        => 'now',
		'publish_datetime'    => '',
		'expiration_datetime' => '',
	];
}

function sb_get_settings() {
	$saved = get_option( 'site_banner_settings', [] );
	if ( ! is_array( $saved ) ) $saved = [];
	return wp_parse_args( $saved, sb_default_settings() );
}

register_activation_hook( __FILE__, function () {
	if ( false === get_option( 'site_banner_settings' ) ) {
		add_option( 'site_banner_settings', sb_default_settings() );
	}
} );

new SB_Admin();
new SB_Frontend();
