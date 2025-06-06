<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'oxygen' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY', 'cnqT8QexVigEdMrpIQUe87Q2szD7LZ0Eqqj+lbSwmHA=' );
define( 'SECURE_AUTH_KEY', 'FJKbYory2fIBrrA7FwSsR3SaT6m66Agxsc/KvvgbK7w=' );
define( 'LOGGED_IN_KEY', 'gZz20xgVp3PoXfdRQSmYiiMYhTeNUMs+Q0FRO1GR4d0=' );
define( 'NONCE_KEY', 'v/19FUCrxMDgKXK6hxbnJ42s6zVpVGB6lGQsDGCiNSU=' );
define( 'AUTH_SALT', '/tCfDZUYd1Vy9ijgBiNchgyObDPLmInD3CJHlvwa7vw=' );
define( 'SECURE_AUTH_SALT', 'NZWfhrn3utsLzX8EJ63Pfxw15MqBFE16HOGmIjkzdok=' );
define( 'LOGGED_IN_SALT', 'n7Bg5MRAIyVDEPbkFWtRAE1zEuZ+sdpSaHIPDtQUrFY=' );
define( 'NONCE_SALT', 'T3AX+yIOn/kRBlLipFMVJ6Q2UVrk2v6COlx3ALvEsr0=' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

define( 'SUNRISE', '1' );                         // Automatically injected by WP Multisite WaaS;

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
define( 'DOMAIN_CURRENT_SITE', 'localhost' );
define( 'PATH_CURRENT_SITE', '/oxygen/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );



/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
