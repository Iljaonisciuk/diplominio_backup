<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'silius_baze');

/** MySQL database username */
define('DB_USER', 'silius_user');

/** MySQL database password */
define('DB_PASSWORD', 'VirbasUztukintiVirbas');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '}KD*|tBf+fIR92yEiY^$Gi76+V!`zl:$.9M(D,|:^d+N!Rrt@}DAxE|]}(PDFKbU');
define('SECURE_AUTH_KEY',  'y>/Mv`~tMH}/=Qo@GzYnR{)qK4BS*|ok``TUt |Nc^cmF5Ost/qJ1qo7Zlyq$>KE');
define('LOGGED_IN_KEY',    'X<@8g;dNpe;]B7idmm8czwQqO$,ND;R:_&MvUdiZsg,F7wCKQ<p EW1uUAV}H|%n');
define('NONCE_KEY',        'ijD8^bd->!%`KvLch)cpAGw5>SZ#]7L9Mo3Ayf8r8AmKKrZenkwTGMu}T}y^TMwX');
define('AUTH_SALT',        '4/qFP;;j`97_ZeW;}#$Lg1/h`pF,qYrENu#@!GopHCJ@voP##-QT(mHIA%-7AoOs');
define('SECURE_AUTH_SALT', '01(N{NXiAEQ990XHo-JN~~<~b;kA@maz*deLw/NcEB1Ix*!ph~.p,x<SKOQ}]Eos');
define('LOGGED_IN_SALT',   '&(MCH4k+VBw YI`P(BZ28k8Sn9G%.;4:yWWNC3@mX.n3[8]4{GfKNgaSdHg=fIWR');
define('NONCE_SALT',       'G]Pfzt$KjgKNJKKu_~s 3upA`4PI4F@c(rU;E1 O6n8%Hr%C_n!Ab9h^`bDMe8id');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
