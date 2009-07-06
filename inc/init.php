<?php
#
# Initialize
#

if ( !defined('sem_version') )
	define('sem_version', '6.0-beta1');

if ( !defined('sem_debug') )
	define('sem_debug', isset($_GET['debug']) );

if ( !defined('sem_widget_cache_debug') )
	define('sem_widget_cache_debug', false);

if ( !defined('sem_header_cache_debug') )
	define('sem_header_cache_debug', false);

if ( function_exists('memory_get_usage') && ( (int) @ini_get('memory_limit') < 48 ) )
	@ini_set('memory_limit', '48M');

define('sem_path', dirname(dirname(__FILE__)));
define('sem_url', get_stylesheet_directory_uri());


#
# extra functions
#

if ( !function_exists('true') ) :
/**
 * true()
 *
 * @return bool true
 **/

function true($bool = null) {
	return true;
} # true()
endif;


if ( !function_exists('false') ) :
/**
 * false()
 *
 * @return bool false
 **/

function false($bool = null) {
	return false;
} # false()
endif;


if ( !function_exists('is_letter') ) :
/**
 * is_letter()
 *
 * @return bool $is_letter
 **/

function is_letter() {
	return is_page() && get_post_meta(get_the_ID(), '_wp_page_template', true) == 'letter.php';
} # is_letter()
endif;


if ( !function_exists('dump') ) :
/**
 * dump()
 *
 * @param mixed $in
 * @return mixed $in
 **/

function dump($in = null) {
	echo '<pre style="margin-left: 0px; margin-right: 0px; padding: 10px; border: solid 1px black; background-color: ghostwhite; color: black;">';
	foreach ( func_get_args() as $var ) {
		echo "\n";
		if ( is_string($var) ) {
			echo "$var\n";
		} else {
			var_dump($var);
		}
	}
	echo '</pre>' . "\n";
	
	return $in;
} # dump()
endif;

if ( sem_debug )
	include sem_path . '/inc/debug.php';


#
# Initialize options
#

$sem_options = get_option('sem5_options');

# autoinstall test
#$sem_options = false;


#
# install / upgrade
#

if ( !isset($sem_options['version']) ) {
	# try sem4_options
	$old_options = get_option('semiologic');
	
	if ( $old_options ) {
		$sem_options = array('version' => '4.0');
		if ( !defined('DOING_CRON') )
			include sem_path . '/inc/upgrade.php';
	} else {
		if ( !defined('DOING_CRON') )
			include sem_path . '/inc/install.php';
	}
} elseif ( $sem_options['version'] != sem_version ) {
	if ( !defined('DOING_CRON') )
		include sem_path . '/inc/upgrade.php';
}
?>