<?php
/**
 * Fonctions utilitaires globales.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Vérifie si ACF (Pro ou Free) est actif.
 */
function mrzdpe_has_acf() {
	return function_exists( 'get_field' ) && function_exists( 'acf_get_setting' );
}
