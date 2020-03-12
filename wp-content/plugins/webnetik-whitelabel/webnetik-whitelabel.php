<?php

/*
Plugin Name: Webnetik Whitelabel
Plugin URI: http://webnetik.hu
Description: Whitelabel plugin for Webnetik website
Version: 1.0.0
Author: Dávid Csejtei
Author URI: http://webnetik.hu
License: GPL2
*/

function hideHelpTabForAllUsers() {
	?>
	<style type="text/css">
		#contextual-help-link-wrap {
			display: none !important;
		}
	</style>
	<?php
}
add_action('admin_head', 'hideHelpTabForAllUsers');

function hideAvatarForCustomers() {
	?>
	<style type="text/css">
		.avatar {
			display: none !important;
		}

		#wpadminbar #wp-admin-bar-my-account.with-avatar #wp-admin-bar-user-actions > li {
			margin-left: 10px !important;
		}
	</style>
	<?php
}
//add_action('admin_head', 'hideAvatarForCustomers');

function removeLogoFromAdminBar() {
	global $wp_admin_bar;

	$wp_admin_bar->remove_menu('wp-logo');
}
add_action('wp_before_admin_bar_render', 'removeLogoFromAdminBar');

function getAdminFooterText() {
	return 'Teampannon Kft.';
}
add_filter('admin_footer_text', 'getAdminFooterText', 9999);

function getFooterVersion() {
    return '1.0';
}
add_filter('update_footer', 'getFooterVersion', 9999);

function hasCurrentUserRole($role, $user = null) {
	if ( ! $user) {
		$user = \wp_get_current_user();
	}

	return in_array($role, $user->roles);
}

function hideDashboardMenuForUsers() {
	if (hasCurrentUserRole('user') || hasCurrentUserRole('administrator')) {
		remove_menu_page('index.php');
	}
}
add_action('admin_menu', 'hideDashboardMenuForUsers');

function changeLoginFormLogo() {
	echo '<style type="text/css">
	h1 a {background-image: url('.plugin_dir_url(__FILE__ ).'img/login_logo.png) !important; height: 48px !important; }
	</style>';
}
add_action('login_head', 'changeLoginFormLogo');

add_filter('show_admin_bar', '__return_false');

function my_image_quality( $quality ) {
    return 100;
}
add_filter( 'jpeg_quality', 'my_image_quality' );
add_filter( 'wp_editor_set_quality', 'my_image_quality' );


/* WPML */

function hideExternalHelpButtonForCustomers() {
	?>
	<style type="text/css">
		#wp-admin-bar-WPML_ALS,
		#wpml_als_help_link,
		#wp-admin-bar-new-content,
		#wp-admin-bar-archive {
			display: none !important;
		}
	</style>
	<?php
}
add_action('admin_head', 'hideExternalHelpButtonForCustomers');

function get_user_role() {
	global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	if ($user_role != "administrator"){
	    add_action( 'init', create_function( '$a', "remove_action( 'init', 'wp_version_check' );" ), 2 );
	    add_filter( 'pre_option_update_core', create_function( '$a', "return null;" ) );
	};
};

add_action( 'admin_head', 'get_user_role' );

function wpb_stop_update_emails( $send, $type, $core_update, $result ) {
	if ( ! empty( $type ) && $type == 'success' ) {
		return false;
	}
	return true;
}
add_filter( 'auto_core_update_send_email', 'wpb_stop_update_emails', 10, 4 );

function show_updated_only_to_admins() {
    if (!current_user_can('update_core')) {
		remove_action( 'admin_notices', 'update_nag', 3 );
		remove_action( 'network_admin_notices', 'update_nag', 3 );
    }
}
add_action( 'admin_head', 'show_updated_only_to_admins', 1 );

