<?php
/**
 * Plugin Name:       MRZ Display Post Exp
 * Plugin URI:        https://github.com/m0r3z/mrz-display-post-exp
 * Description:       Affiche des posts et custom posts avec leurs champs ACF en liste, grille ou slider. Templates HTML personnalisables, filtres et recherche côté client.
 * Version:           1.1.4
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            Morez.co
 * Author URI:        https://morez.co
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       mrz-display-post-exp
 * Domain Path:       /languages
 *
 * MRZ Display Post Exp — Copyright (C) 2026 Morez.co <hello@morez.co>
 * "MRZ Display Post Exp" is a trademark of Morez.co. See LICENSE for full terms.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v3 as published by
 * the Free Software Foundation. See LICENSE for the full license text.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MRZDPE_VERSION', '1.1.4' );
define( 'MRZDPE_FILE', __FILE__ );
define( 'MRZDPE_DIR', plugin_dir_path( __FILE__ ) );
define( 'MRZDPE_URL', plugin_dir_url( __FILE__ ) );
define( 'MRZDPE_BASENAME', plugin_basename( __FILE__ ) );
// Le nom d'un post type est limité à 20 caractères par WordPress, d'où le slug court.
define( 'MRZDPE_CPT', 'mrzdpe_list' );

require_once MRZDPE_DIR . 'includes/helpers.php';

spl_autoload_register(
	static function ( $class ) {
		if ( strpos( $class, 'Mrzdpe\\' ) !== 0 ) {
			return;
		}

		$relative = substr( $class, strlen( 'Mrzdpe\\' ) );
		$relative = str_replace( '\\', '/', $relative );
		$parts    = explode( '/', $relative );
		$last     = array_pop( $parts );
		// Insère un tiret avant une majuscule qui suit une minuscule/chiffre,
		// ou avant une majuscule suivie d'une minuscule (fin d'acronyme).
		$last     = preg_replace( '/(?<=[a-z0-9])[A-Z]|(?<=[A-Z])[A-Z](?=[a-z])/', '-$0', $last );
		$last     = strtolower( $last );
		$prefix   = empty( $parts ) ? '' : strtolower( implode( '/', $parts ) ) . '/';
		$path     = MRZDPE_DIR . 'includes/' . $prefix . 'class-' . $last . '.php';

		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
);

register_activation_hook( __FILE__, array( 'Mrzdpe\\Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Mrzdpe\\Deactivator', 'deactivate' ) );

add_action(
	'plugins_loaded',
	static function () {
		Mrzdpe\Plugin::instance()->boot();
	}
);
