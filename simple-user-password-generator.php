<?php
/**
 Plugin Name: Simple User Password Generator
 Plugin URI: http://10up.com/plugins/simple-user-password-generator-wordpress/
 Description: Allows administrators to generate a secure password when adding new users.
 Version: 2.0.1
 Author: Jake Goldman (10up LLC)
 Author URI: http://10up.com

    Plugin: Copyright 2011 10up LLC (email : jake@10up.com)

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
		add_action( 'admin_print_scripts-user-edit.php', array( $this, 'enqueue_script' ) );
		add_action( 'user_register', array( $this, 'user_register' ) );
		add_action( 'edit_user_profile', array( $this, 'edit_user_profile' ), 1 );
		add_action( 'user_profile_update_errors', array( $this, 'user_profile_update_errors' ), 10, 3 );
	}
	
	public function admin_init() {
		load_plugin_textdomain( 'simple-user-password-generator', false, dirname( plugin_basename( __FILE__ ) ) . '/localization/' );
	}
	
	public function enqueue_script() {
		if ( !apply_filters( 'show_password_fields', true ) || !current_user_can( 'edit_users' ) )
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
		if ( current_user_can( 'add_users' ) && !empty( $_POST['reset_password_notice'] ) )
			update_user_option( $user_id, 'default_password_nag', true, true );
	}

	public function edit_user_profile( $profileuser ) {
		wp_nonce_field( 'simple-user-password-generator-reset', '_simple_user_password_generator' );
	?>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="send_password"><?php _e('Send Password?') ?></label></th>
			<td><label for="send_password"><input type="checkbox" name="send_password" id="send_password" disabled="disabled" /> <?php _e('Send this password to the user by email.','simple-user-password-generator'); ?></label></td>
		</tr>
	</table>
	<script type="text/javascript">
		jQuery('#pass1').keyup(function(){
			var passval = jQuery(this).val();
			if ( passval == '' ) jQuery('#send_password').attr('disabled','disabled');
			else jQuery('#send_password').removeAttr('disabled');
		});
	</script>
	<?php
	}

	public function user_profile_update_errors( $errors, $update, $user ) {
		if ( !current_user_can( 'edit_users' ) || empty( $_POST['_simple_user_password_generator'] ) || !wp_verify_nonce( $_POST['_simple_user_password_generator'], 'simple-user-password-generator-reset' ) )
			return;

		$this->user_register( $user->ID );

		if ( !$update || empty( $user->user_pass ) || empty( $_POST['send_password'] ) )
			return;

		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		$message  = sprintf(__('Username: %s'), $user->user_login) . "\r\n";
		$message .= sprintf(__('Password: %s'), $user->user_pass) . "\r\n";
		$message .= wp_login_url() . "\r\n";

		wp_mail($user->user_email, sprintf(__('[%s] Your username and password'), $blogname), $message);
	}
}

$simple_local_avatars = new simple_user_password_generator;