<?php
/**
 * Plugin Name: Role Based Hide Adminbar
 * Description: This plugin provides the functionality to hide and show the admin bar based on your desired user role.
 * Author: MohammedYasar Khalifa
 * Author URI: https://myasark.wordpress.com/
 * Text Domain: role-based-hide-adminbar
 * Domain Path: /languages
 * Version: 1.0.6
 * License: GPLv2
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) exit;
class RBHAbar {
    function __construct() {
        add_action('admin_init', array($this, 'rbhabar_register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'load_admin_style'));
        add_action('admin_menu', array($this, 'rbhabar_register_options_page'));
        add_action('after_setup_theme', array($this, 'rbhabar_remove_admin_bar'));
        add_action( 'init', array($this,'rbhabar_load_textdomain' ));
    }
    function rbhabar_register_settings() {
        add_option('rbhabar_options_name');
        register_setting('rbhabar_options_group', 'rbhabar_options_name');
    }
   function load_admin_style($hook) { 
        if( $hook == 'settings_page_rbhabar-setting' ) {
        wp_register_style('rbhabar_css', plugins_url('/assets/css/custom.css', __FILE__));
        wp_enqueue_style('rbhabar_css');
     }
    }
    function rbhabar_register_options_page() {
        add_options_page('Hide Adminbar', 'Hide Adminbar', 'manage_options', 'rbhabar-setting', array($this, 'rbhabar_options_page'));
    }
    function rbhabar_options_page() {
    ?>
	  <div class="container"> 
	   <h2><?php _e( 'Hide Adminbar', 'role-based-hide-adminbar' ); ?></h2>
		  <form method="post" action="options.php">
		  <?php settings_fields('rbhabar_options_group'); ?>
		  <p><?php _e( 'Select Role for hide admin bar', 'role-based-hide-adminbar' ); ?></p>  
		  <?php global $wp_roles;
				$role_exists = get_option('rbhabar_options_name');
				foreach ($wp_roles->roles as $key => $value): ?>
			   <div><input type="checkbox" name="rbhabar_options_name[]" id="<?php echo $value['name']; ?>" value="<?php echo $key; ?>"<?php if ($role_exists) { if (in_array($key, $role_exists)) { echo "checked='checked'";}	} ?>>
				   <label for="<?php echo $value['name']; ?>"><?php echo $value['name']; ?></label></div>
					<br/>
			<?php endforeach; ?>
		  <?php submit_button(); ?>
		  </form>
	  </div>
    <?php
    }
    function rbhabar_remove_admin_bar() {
        $role_exists = get_option('rbhabar_options_name');
        if (is_array($role_exists) || is_object($role_exists)) {
            foreach ($role_exists as $role_exist) {
                if (current_user_can($role_exist)) {
                    add_filter( 'show_admin_bar', '__return_false', PHP_INT_MAX );
                }
            }
        }
    }
   function rbhabar_load_textdomain() {
     load_plugin_textdomain( 'role-based-hide-adminbar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
    }
}
$RBHAbar = new RBHAbar();
function rbhabar_remove() {
    delete_option('rbhabar_options_name');
    unregister_setting('rbhabar_options_group', 'rbhabar_options_name');
}
register_deactivation_hook(__FILE__, 'rbhabar_remove');