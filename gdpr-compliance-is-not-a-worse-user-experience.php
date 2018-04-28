<?php
/**
 * Plugin Name: GDPR is not a worse user experience.
 * Plugin URI: https://github.com/imath/gdpr-compliance-is-not-a-worse-user-experience/
 * Description: RGPD, ce n'est pas parce qu'un utilisateur décide de ne pas stocker ses données personnelles dans des cookies qu'il mérite une plus mauvaise expérience de WordPress.
 * Version: 1.0.0
 * Requires at least: 4.9.6
 * Tested up to: 5.0.0
 * License: GPLv2 or later
 * Author: imath
 * Author URI: https://imathi.eu/
 * Text Domain: gdpr-compliance-is-not-a-worse-user-experience
 * Domain Path: /languages/
 * GitHub Plugin URI: https://github.com/imath/gdpr-compliance-is-not-a-worse-user-experience/
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'GDPR_Is_Not_A_Worse_User_Experience' ) ) :

/**
 * Main Plugin Class
 *
 * @since  1.0.0
 */
final class GDPR_Is_Not_A_Worse_User_Experience {
	/**
	 * Plugin's main instance
	 *
	 * @var object
	 */
	protected static $instance;

	/**
	 * Initialize the plugin
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->globals();
		$this->inc();

		// Load translations
		add_action( 'init', array( $this, 'load_textdomain' ), 9 );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return object A single instance of this class.
	 */
	public static function start() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Setups plugin's globals
	 *
	 * @since 1.0.0
	 */
	private function globals() {
		// Version
		$this->version = '1.0.0';

		// Domain
		$this->domain = 'gdpr-compliance-is-not-a-worse-user-experience';

		// Base name
		$this->file      = __FILE__;
		$this->basename  = plugin_basename( $this->file );

		// Path and URL
		$this->dir        = plugin_dir_path( $this->file );
		$this->url        = plugin_dir_url ( $this->file );
		$this->lang_dir   = trailingslashit( $this->dir . 'languages' );
		$this->inc_dir    = trailingslashit( $this->dir . 'inc' );
	}

	/**
	 * Includes plugin's needed files
	 *
	 * @since 1.0.0
	 */
	private function inc() {
		/**
		 * This plugin is only needed untill the ticket #43857 is fixed
		 *
		 * @see https://core.trac.wordpress.org/ticket/43857
		 */
		if ( version_compare( $GLOBALS['wp_version'], '4.9.6', '<' ) ) {
			return;
		}

		require $this->inc_dir . 'functions.php';
	}

	/**
	 * Loads the translation files
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( $this->domain, false, trailingslashit( basename( $this->dir ) ) . 'languages' );
	}
}

endif;

/**
 * Boot the plugin.
 *
 * @since 1.0.0
 */
function gcinawue() {
	return GDPR_Is_Not_A_Worse_User_Experience::start();
}
add_action( 'plugins_loaded', 'gcinawue', 9 );
