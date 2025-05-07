<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/Mqroon
 * @since             1.0.0
 * @package           Global_Variables
 *
 * @wordpress-plugin
 * Plugin Name:       Global Variables
 * Plugin URI:        https://github.com/Mqroon/WP-Global-Variables.git
 * Description:       A plugin that allows the creation of global variables and can be referred to by using a [shortcode].
 * Version:           1.0.0
 * Author:            Walker Alexander
 * Author URI:        https://github.com/Mqroon/
 * GitHub Plugin URI: https://github.com/Mqroon/WP-Global-Variables.git
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       global-variables
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'GLOBAL_VARIABLES_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-global-variables-activator.php
 */
function activate_global_variables() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-global-variables-activator.php';
	Global_Variables_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-global-variables-deactivator.php
 */
function deactivate_global_variables() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-global-variables-deactivator.php';
	Global_Variables_Deactivator::deactivate();
}

/**
 * The code that runs during plugin uninstallation.
 */
function uninstall_global_variables() {
     global $wpdb;
     $table_name = $wpdb->prefix . 'globalVariables';
     $sql = "DROP TABLE IF EXISTS $table_name";
     $wpdb->query($sql);
     delete_option("my_plugin_db_version");
}   

register_activation_hook( __FILE__, 'activate_global_variables' );
register_deactivation_hook( __FILE__, 'deactivate_global_variables' );
register_deactivation_hook( __FILE__, 'uninstall_global_variables' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-global-variables.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_global_variables() {

	$plugin = new Global_Variables();
	$plugin->run();

}
run_global_variables();

/**
 *
 * Custom code after boilerplate
 *
 */ 

function gv_admin_page(){
	global $wpdb;
	$table_name = $wpdb->prefix . "globalVariables"; 

	$query = $wpdb->prepare("SELECT * FROM $table_name");
	$results = $wpdb->get_results($query);

	$variable_list = '
	<div id="gv_variables_wrapper">
		<h2 class="gv_variables_h2">Saved Variables
			<span id="loading_icon" class="dashicons dashicons-update" style="display: none;"></span> <!-- Spinning icon -->
        	<span id="save_icon" class="dashicons dashicons-saved" style="display: block;"></span> <!-- Checkmark icon -->
		</h2>
		<div class="gv_variables_header">
			<p>Identifier</p><p>Value</p>
		</div>
	';
	
	if (!empty($results)) {
		foreach ($results as $row) {
			$cleanedText = stripslashes($row->text);
			$variable_list .= "
				<div class=\"variable_item\">
					<form class=\"gv_custom_action_handler\" method=\"post\">
						<input type=\"text\" name=\"name\" value=\"$row->name\">
						<input type=\"text\" name=\"custom_input\" value=\"$cleanedText\"/>
						<input type=\"text\" hidden name=\"id\" value=\"$row->id\">
						<input type=\"text\" hidden name=\"action\" value=\"update_variable\">
						<button type=\"submit\">Update</button>
					</form>
					<form class=\"gv_custom_action_handler\" method=\"post\">
						<input type=\"text\" hidden name=\"id\" value=\"$row->id\">
						<input type=\"text\" hidden name=\"action\" value=\"delete_variable\">
						<button type=\"submit\">Delete</button>
					</form>
				</div>
			";
		}
		$variable_list .= '</div>';
	} else {
		$variable_list .= '
		<div class=\"variable_item\">
			No variables found
		<div>';
	}
	
	echo '<div class="gv_admin_wrapper">';

	echo '<h1>Global Variables Admin Page</h1>';
	echo '<p>Welcome to the admin page!<br>Create some variables and reference them using [gv variable="identifier"] anywhere in your web page.</p>';

	echo '
	<div class="gv_form_wrapper">
		<form class="gv_custom_action_handler" method="post">
			<input type="text" name="name" placeholder="Identifier">
			<input type="text" name="custom_input" placeholder="Value"/>
			<input type="text" hidden name="action" value="add_new_variable">
			<button type="submit">Create New Variable</button>
		</form>
	</div>
	';

	echo $variable_list;

	echo '</div>';
}
function gv_admin_menu() {
    add_menu_page( 'Global Variables Settings Page', 'Global Variables', 'manage_options', 'globalvariables.php', 'gv_admin_page', 'dashicons-database', 6  );
}

function gv_admin_enqueue_scripts() {
	wp_dequeue_script('global-variables');
    wp_deregister_script('global-variables');
	
	wp_dequeue_script('gv-form-script');
    wp_deregister_script('gv-form-script');

    wp_register_script('gv-form-script', plugins_url() . '/global-variables/admin/js/global-variables-admin.js', array('jquery'), false, true);
    wp_enqueue_script('gv-form-script');
	wp_localize_script('gv-form-script', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
	wp_enqueue_style('my-plugin-admin-style', plugins_url() . '/global-variables/admin/css/global-variables-admin.css');

	wp_enqueue_style('dashicons');
}
add_action( 'admin_enqueue_scripts', 'gv_admin_enqueue_scripts' );

add_action('admin_menu', 'gv_admin_menu');


add_action('admin_print_scripts', function () {
    global $wp_scripts;
    foreach ($wp_scripts->registered as $handle => $script) {
        error_log("Enqueued script: " . $handle . " -> " . $script->src);
    }
});


function gv_shortcode_handler ( $atts ) {
	global $wpdb;
	$table_name = $wpdb->prefix . "globalVariables"; 


	$a = shortcode_atts( array(
		'variable' => 'default name',
	), $atts );

	$query = $wpdb->prepare("SELECT * FROM $table_name WHERE name = %s", $a['variable']);
	$results = $wpdb->get_results($query);

	if(count($results) > 0) {
		return $results[0]->text;
	} else {
		return "Error: No variable found \"[{$a['variable']}]\"";
	}
}
add_shortcode( 'gv', 'gv_shortcode_handler' );


function gv_dbtable_install() {
	global $wpdb;
 
	$table_name = $wpdb->prefix . "globalVariables"; 

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name tinytext NOT NULL,
		text text NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
function gv_dbtable_install_data() {
	global $wpdb;
 
	$table_name = $wpdb->prefix . "globalVariables"; 

	$welcome_name = 'Example Variable';
	$welcome_text = 'Variable value 123';

	$wpdb->insert( 
		$table_name, 
		array( 
			'name' => $welcome_name, 
			'text' => $welcome_text, 
		) 
	);
}
register_activation_hook( __FILE__, 'gv_dbtable_install' );
register_activation_hook( __FILE__, 'gv_dbtable_install_data' );


function gv_add_new_variable() {
	if (!current_user_can('administrator')) {
		wp_die('You do not have permission to access this action');
		return;
	}
	
	global $wpdb;
	$table_name = $wpdb->prefix . "globalVariables"; 
	$custom_input = sanitize_text_field($_POST['custom_input']);
	$custom_name = sanitize_text_field($_POST['name']);

	$query = $wpdb->prepare("INSERT INTO $table_name (name, text) VALUES (%s, %s)", $custom_name, $custom_input);
	
	$result = $wpdb->query($query);
	if ($result === false) {
		echo 'Action performed unsucessfully.';
		echo "\n($result)\n";
		wp_die('Error executing query');
	} else {
		echo 'Action performed successfully with input: ' . $custom_input;
		echo "\n";
		echo $result;
		echo 'Query executed successfully';
	}
	echo "test";
	wp_die();
}

function gv_delete_variable() {
	if (!current_user_can('administrator')) {
		wp_die('You do not have permission to access this action');
		return;
	}

	global $wpdb;
	$table_name = $wpdb->prefix . "globalVariables"; 
	$item_id = (int)$_POST['id'];
	
	$query = $wpdb->prepare("DELETE FROM $table_name WHERE id = %d", $item_id);
	$result = $wpdb->query($query);

	if ($result === false) {
		$wpdb->print_error();
		wp_die('Error executing delete query');
		return;
	} else {
		echo 'Item deleted successfully';
	}
	wp_die();
}

function gv_update_variable() {
	if (!current_user_can('administrator')) {
		wp_die('You do not have permission to access this action');
		return;
	}

	global $wpdb;
	$table_name = $wpdb->prefix . "globalVariables"; 
	$new_custom_input = sanitize_text_field($_POST['custom_input']);
	$new_custom_name = sanitize_text_field($_POST['name']);
	$item_id = (int)$_POST['id'];
	
	$query = $wpdb->prepare("UPDATE $table_name SET name = %s, text = %s WHERE id = %d", $new_custom_name, $new_custom_input, $item_id);
	$result = $wpdb->query($query);

	if ($result === false) {
		$wpdb->print_error();
		wp_die('Error executing update query');
	} else {
		echo $result;
		echo $query;
	}

	wp_die();
}

function gv_refresh_variable_data() {
	if (!current_user_can('administrator')) {
		wp_die('You do not have permission to access this action');
		return;
	}
	
	global $wpdb;
	$table_name = $wpdb->prefix . "globalVariables"; 

	$query = $wpdb->prepare("SELECT * FROM $table_name");
	$results = $wpdb->get_results($query);
	
	$variable_list = '
		<h2 class="gv_variables_h2">Saved Variables
			<span id="loading_icon" class="dashicons dashicons-update" style="display: none;"></span> <!-- Spinning icon -->
        	<span id="save_icon" class="dashicons dashicons-saved" style="display: block;"></span> <!-- Checkmark icon -->
		</h2>
		<div class="gv_variables_header">
			<p>Identifier</p><p>Value</p>
		</div>
	';
	
	if (!empty($results)) {
		foreach ($results as $row) {
			$cleanedText = stripslashes($row->text);
			$variable_list .= "
				<div class=\"variable_item\">
					<form class=\"gv_custom_action_handler\" method=\"post\">
						<input type=\"text\" name=\"name\" value=\"$row->name\">
						<input type=\"text\" name=\"custom_input\" value=\"$cleanedText\"/>
						<input type=\"text\" hidden name=\"id\" value=\"$row->id\">
						<input type=\"text\" hidden name=\"action\" value=\"update_variable\">
						<button type=\"submit\">Update</button>
					</form>
					<form class=\"gv_custom_action_handler\" method=\"post\">
						<input type=\"text\" hidden name=\"id\" value=\"$row->id\">
						<input type=\"text\" hidden name=\"action\" value=\"delete_variable\">
						<button type=\"submit\">Delete</button>
					</form>
				</div>
			";
		}
	} else {
		$variable_list .= '<div>No variables found</div>';
	}

	$variable_list .= '</div>';
	echo $variable_list;
	wp_die();
}

add_action('wp_ajax_add_new_variable', 'gv_add_new_variable');
add_action('wp_ajax_delete_variable', 'gv_delete_variable');
add_action('wp_ajax_update_variable', 'gv_update_variable');
add_action('wp_ajax_refresh_variable_data', 'gv_refresh_variable_data');
?>