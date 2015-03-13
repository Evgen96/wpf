<?php





/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'theftpne_wp1');

/** MySQL database username */
define('DB_USER', 'theftpne_wp1');

/** MySQL database password */
define('DB_PASSWORD', 'w79sO!5S-P');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'dmsyjn1jxckf4tqrxyaaywvaatoeq59ooofjfnsgepgvi92jhiwjk2litilqyfpt');
define('SECURE_AUTH_KEY',  'v9e0xqzr1envz2ovfkibn4dfofctzlnftvdi5pywbpunehnnlbvc6a7mzbdkmiok');
define('LOGGED_IN_KEY',    'dv8jelqsxps3nfrljqqmw5feo0oay4er1h0dey3xctheuqzoukfdujgsaxmwzifu');
define('NONCE_KEY',        'xjegfyk0eosndg3xzbfxjs32iak5egcxtc7yu8kct7kjx21epty8xgxanphq74ib');
define('AUTH_SALT',        'cn4jfkbkmtm2d8duiwyu0tplcognryljtfpdfzm4vpnlmpgm2s1mau9uyu6r32mb');
define('SECURE_AUTH_SALT', 'ihdzk2nujtbdwybjeleul5ryezgdgwtd9jz0lqrux6biaa2fd3n0cxjxfmdqmbfh');
define('LOGGED_IN_SALT',   'yomt5sjjqvwtrdpk0ogec7ja0m2yihst4dnjfl5mb7zpkgfkfcv2urjcy6z4ekrc');
define('NONCE_SALT',       'lgttlsrqnfrxsovfkj1j1u1wv0jetumhn9jzqbgfuzsfxyptwrzxdvzfh7psw2zf');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp1_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
