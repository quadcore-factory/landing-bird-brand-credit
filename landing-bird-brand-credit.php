<?php
/**
 * Plugin Name: Landing Bird Brand Credit
 * Plugin URI: https://landingbird.mx
 * Description: A small, configurable and whitelabel footer or header credit for Landing Bird.
 * Version: 0.1.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Author: Landing Bird
 * Author URI: https://landingbird.mx
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: landing-bird-brand-credit
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/includes/class-landing-bird-brand-credit.php';

add_action('plugins_loaded', static function () {
    Landing_Bird_Brand_Credit::boot();
});
