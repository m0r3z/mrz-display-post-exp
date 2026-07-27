<?php
/**
 * Actions exécutées à la désactivation du plugin.
 */

namespace Mrzdpe;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Deactivator {

	public static function deactivate() {
		flush_rewrite_rules();
	}
}
