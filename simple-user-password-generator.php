<?php
/**
 Plugin Name: Simple User Password Generator
 Plugin URI: http://www.get10up.com/plugins/simple-user-password-generator-wordpress/
 Description: Allows administrators to generate a secure password when adding new users.
 Version: 1.0
 Author: Jake Goldman (10up LLC)
 Author URI: http://get10up.com

    Plugin: Copyright 2011 10up LLC (email : jake@get10up.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * add field to user profiles
 */
 
class simple_user_password_generator {
	
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'wp_ajax_simple_user_generate_password', array( $this, 'ajax_generate_password' ) );
		add_action( 'admin_print_scripts-user-new.php', array( $this, 'enqueue_script' ) );
		add_action( 'user_register', array( $this, 'user_register' ) );
		add_action( 'load-profile.php', array( $this, 'load_profile' ) );
	}
	
	public function admin_init() {
		load_plugin_textdomain( 'simple-user-password-generator', false, dirname( plugin_basename( __FILE__ ) ) . '/localization/' );
	}
	
	public function enqueue_script() {
		if ( ! apply_filters('show_password_fields', true) )
			return;
			
		wp_enqueue_script( 'simple-user-password-generator', plugin_dir_url( __FILE__ ) . 'simple-user-password-generator.js', array( 'jquery' ), '1.0' );	
		
		$js_trans = array(
			'Generate' => esc_attr( __( 'Generate Password', 'simple-user-password-generator' ) ),
			'PassChange' => __( 'Encourage the user to change their password, once logged in.', 'simple-user-password-generator' )
		);
		wp_localize_script( 'simple-user-password-generator', 'simple_user_password_generator_l10n', $js_trans );	
	}
	
	public function ajax_generate_password() {
		die( wp_generate_password() );
	}
	
	public function user_register( $user_id ) {
		if ( current_user_can( 'add_users' ) && ! empty( $_POST['reset_password_notice'] ) )
			update_user_option( $user_id, 'default_password_nag', true, true );
	}
	
	public function load_profile() {
		if ( get_user_option( 'default_password_nag' ) )
			add_action( 'admin_notices', array( $this, 'default_password_nag_profile' ) );
	}
	
	public function default_password_nag_profile() {
		echo '<div class="error default-password-nag"><p><strong>' . __('Notice:') . '</strong> ';
		_e( 'You&rsquo;re using the auto-generated password for your account. Consider changing the password to something easier to remember.', 'simple-user-password-generator' );
		echo '</p><p>';
		echo '<a href="#password" onclick="jQuery(\'#pass1\').focus();">' . __('Edit my password','simple-user-password-generator') . '</a> | ';
		printf( '<a href="%s" id="default-password-nag-no">' . __('No thanks, do not remind me again') . '</a>', '?default_password_nag=0' );
		echo '</p></div>';
	}
}

$simple_local_avatars = new simple_user_password_generator;