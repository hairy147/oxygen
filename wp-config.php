<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'oxy' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'xm/84^V{[R3?s.s;RY?f~T6D+{@.pMW:{PB0UscA&0a#:s@B<%x3,;xyNYxx_@dx' );
define( 'SECURE_AUTH_KEY',  'vDM*&az+ezv9jt/sKy<k5u4iFwBa`3v(|.Yo},LFyL=>ax3:#Rk D%5=c884hzf`' );
define( 'LOGGED_IN_KEY',    'f$.]~T*f&.4 _kY?)dV!Jcr:Zv4wGYA/a:lA]X tQPJq)*bc?0<87C(azwI9D;SX' );
define( 'NONCE_KEY',        'S7_Ag}%$G J/OJF37HrWKB/4qJRL~a8{?h^4@xp2HbMwhFDXUPiv!0MA/BH-h=Ul' );
define( 'AUTH_SALT',        't)T .[do; 0(V*6iNo4K@7j?}&d._h^6HkE/WjyN)p,-dbx|8>OL:1BUYtjXO+z,' );
define( 'SECURE_AUTH_SALT', 'RuuJ;m_|9;S=|q)hoksCwA/XHkjT[RA(j?hDenW,o{lulBpjZj666<k8kWPcJI8{' );
define( 'LOGGED_IN_SALT',   '?K_d09xiPSPUuE5cy?0+G356]4fmQ*b:bQ6~vEu ghQ^3tXQ<]t |PZTsdVqv:_9' );
define( 'NONCE_SALT',       'H{=50lb=[dibzo&Pr@S_!>v>>UM96XHZ@YAq_6rGgGg{n?wyp;?!=VgFidp+9y3$' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);

/* Add any custom values between this line and the "stop editing" line. */

// Enable WordPress Multisite configuration
define( 'WP_ALLOW_MULTISITE', true );
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
define( 'DOMAIN_CURRENT_SITE', 'localhost' );
define( 'PATH_CURRENT_SITE', '/oxygen/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
