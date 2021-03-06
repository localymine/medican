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
define('DB_NAME', 'webnhath_webnhathuoc');

/** MySQL database username */
define('DB_USER', 'webnhath_webn');

/** MySQL database password */
define('DB_PASSWORD', '),GR2vlc&,u=');

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
define('AUTH_KEY',         '9%&[cnwg1Zm:}AL`dO^s zBx7>S4Ji1tx87zcFdM7NsB2H*5s%X5yAFnt|P054?O');
define('SECURE_AUTH_KEY',  'Q)(s8!hZ-PX&<boVPG-&LK-oP5%C&h|!h.xSLcHi$V, {Sxtu82(otAeg=,8GKEP');
define('LOGGED_IN_KEY',    '~5(l!q?-r~*w5@t/jK^|V8@8a{zY>3k]Y{TJFlXf`a+&N.mq66tl`bmTUG3/IF%S');
define('NONCE_KEY',        ':k9V0l@^)hVw5~/TRCL&k+DjkKdK*-{2@%[g*{Gf`tZdw{7|*dgRW%| VLyiY>3e');
define('AUTH_SALT',        '-!7|>~]&X~)jQkT$bVL^Pg=>b5J]G$h<-293+|f<=Xfsx&&zJce`b@AXc}u:[M^6');
define('SECURE_AUTH_SALT', '*-[{Mij/QDC7U7b@v-BRvi3((`2<qpt/o+|v~T~,Fp4h(vpsC+*(*B15qkzhjQ0Y');
define('LOGGED_IN_SALT',   'o408Ie$0L2`!<?:D~ozaaKnC!B1pl;TwG+ud6f)z.YD9 xpmzxsZy(Sj{$J]$(0t');
define('NONCE_SALT',       '1?]{cxvh|}Pr!tXw@;*E_22rff/k6;T ([C_+6}||U$tl=^szod0Ljn`S2nLe(do');

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
