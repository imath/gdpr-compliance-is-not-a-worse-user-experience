<?php

require_once getenv( 'WP_DEVELOP_DIR' ) . '/tests/phpunit/includes/functions.php';

function _bootstrap_gcinawue() {
	// load the plugin.
	require dirname( __FILE__ ) . '/../../gdpr-compliance-is-not-a-worse-user-experience.php';
}
tests_add_filter( 'muplugins_loaded', '_bootstrap_gcinawue' );

require getenv( 'WP_DEVELOP_DIR' ) . '/tests/phpunit/includes/bootstrap.php';
