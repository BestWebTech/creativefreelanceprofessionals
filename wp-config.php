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
define('DB_NAME', 'creativefreelanceprofessionals');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'hZ2_MnPIpUWOBPhPzOJ0u-gHR-rKsK-BZRMZvK0ExwxS_vh71WindhMNR3YEQNfc');
define('SECURE_AUTH_KEY',  'yqyzQmvjhCvMKnhHnvgJ+wxGT5i4bRwqXvwIr+-5+XDeg-fjXaoYkuEB0nPlbq*V');
define('LOGGED_IN_KEY',    'Ez*KcH544s54UbCSHfqdkBgNnwbGP_lxyARKmDPTObQTQOkQScoEjJk+td8pQSrg');
define('NONCE_KEY',        '_igh3_F7IDY3DEQ3zg7_hYqwWaU07wVeV6YOAlA1oC10eVGnoH9DGyyW8YQ+mbWF');
define('AUTH_SALT',        'lSGTvViYqn47esz6L5z1TgwS85MUIWsJO7Fxpq8*.8BAfvRMOkFq3Mta7xk04jIL');
define('SECURE_AUTH_SALT', 'kLR9_lyqoZYMql7GVrkEuNFLCSTQGdL1d62i.Un+lcq20D_D6gymlMH7uIqg18kk');
define('LOGGED_IN_SALT',   'KF1ZhFgHPwI.AUQJYRApUXM+IAPU_G68MfNs7LdIOA74ez31Lw*1CM9zJ4tn-XRi');
define('NONCE_SALT',       '8no6.USDx2AHX2xE2IDOPu1khWjmEqIbwDyFtNqgZ7+8AIDG4LddLfjE9qXVtonw');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'zqpr_';

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

function pr($data){
    
    echo '<pre>';
		print_r($data);
}

