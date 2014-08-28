<?php
/**
 Plugin Name: Simple User Password Generator
 Plugin URI: http://10up.com/plugins/simple-user-password-generator-wordpress/
 Description: Allows administrators to generate a secure password when adding new users.
 Version: 3.0.1
 Author: Jake Goldman, 10up
 Author URI: http://10up.com
 License: GPLv2 or later
*/

if ( ! class_exists( 'Simple_User_Password_Generator' ) ) :
 
class Simple_User_Password_Generator {

	/**
	 * Handles initializing this class and returning the singleton instance after it's been cached.
	 *
	 * @return null|Simple_User_Password_Generator
	 */
	public static function get_instance() {
		// Store the instance locally to avoid private static replication
		static $instance = null;

		if ( null === $instance ) {
			$instance = new self();
			self::_add_actions();
		}

		return $instance;
	}

	/**
	 * An empty constructor
	 */
	public function __construct() { /* Purposely do nothing here */ }

	/**
	 * Handles registering hooks that initialize this plugin.
	 */
	public static function _add_actions() {
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		add_action( 'wp_ajax_simple_user_generate_password', array( __CLASS__, 'ajax_generate_password' ) );
		// add_action( 'user_register', array( __CLASS__, 'user_register' ) );
		add_action( 'user_profile_update_errors', array( __CLASS__, 'user_profile_update_errors' ), 10, 3 );
	}

	/**
	 * Setup localization and later admin hooks
	 */
	public static function admin_init() {
		load_plugin_textdomain( 'simple-user-password-generator', false, dirname( plugin_basename( __FILE__ ) ) . '/localization/' );

		add_action( 'admin_print_scripts-user-new.php', array( __CLASS__, 'enqueue_script' ) );
		add_action( 'admin_print_scripts-user-edit.php', array( __CLASS__, 'enqueue_script' ) );
		add_action( 'edit_user_profile', array( __CLASS__, 'edit_user_profile' ), 1 );
	}

	/**
	 * Load scripts for generate password button
	 */
	public static function enqueue_script() {
		if ( apply_filters( 'show_password_fields', true ) && current_user_can( 'edit_users' ) ) {
			wp_enqueue_script( 'simple-user-password-generator', plugin_dir_url( __FILE__ ) . 'simple-user-password-generator.js', array( 'jquery' ), '2.0', true );
			wp_localize_script( 'simple-user-password-generator', 'simple_user_password_generator_l10n', array(
				'Generate' => esc_attr__( 'Generate Password', 'simple-user-password-generator' ),
				'PassChange' => __( 'Encourage the user to change their password, once logged in.', 'simple-user-password-generator' )
			) );
		}
	}

	/**
	 * AJAX callback with generated password
	 */
	public static function ajax_generate_password() {
		die( wp_generate_password() );
	}

	/**
	 * Add new field to the edit user screen
	 *
	 * @param $profileuser
	 */
	public static function edit_user_profile( $profileuser ) {
		wp_nonce_field( 'simple-user-password-generator-reset', '_simple_user_password_generator' );
	?>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="send_password"><?php _e( 'Send Password?' ) ?></label></th>
			<td><label for="send_password"><input type="checkbox" name="send_password" id="send_password" disabled="disabled" /> <?php _e( 'Send this password to the user by email.', 'simple-user-password-generator' ); ?></label></td>
		</tr>
	</table>
	<?php
	}

	/**
	 * Set password nag option and send password to user after saving profile
	 *
	 * @param $errors
	 * @param $update
	 * @param $user
	 */
	public static function user_profile_update_errors( $errors, $update, $user ) {
		if ( current_user_can( 'edit_users' ) && !empty( $_POST['_simple_user_password_generator'] ) && wp_verify_nonce( $_POST['_simple_user_password_generator'], 'simple-user-password-generator-reset' ) ) {
			// enable password change nag
			if ( ! empty( $_POST['reset_password_notice'] ) ) {
				update_user_option( $user->ID, 'default_password_nag', true, true );
			}

			// send password to user by email
			if ( $update && !empty( $user->user_pass ) && !empty( $_POST['send_password'] ) ) {
				$blogname = wp_specialchars_decode( get_bloginfo('name'), ENT_QUOTES );
				$message  = sprintf( __('Username: %s'), $user->user_login ) . "\r\n";
				$message .= sprintf( __('Password: %s'), $user->user_pass ) . "\r\n";
				$message .= wp_login_url() . "\r\n";

				wp_mail( $user->user_email, sprintf( __('[%s] Your username and password'), $blogname ), $message );
			}
		}
	}
}

Simple_User_Password_Generator::get_instance();

endif;