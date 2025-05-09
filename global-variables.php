<?php

/**
 * Plugin Name:       Global Variables
 * Plugin URI:        https://github.com/Mqroon/WP-Global-Variables
 * Description:       Enables global variables accessible via shortcodes.
 * Version:           0.1.0
 * Author:            Walker Alexander
 * Author URI:        https://github.com/Mqroon/
 * GitHub Plugin URI: https://github.com/Mqroon/WP-Global-Variables
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
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
define( 'GLOBAL_VARIABLES_VERSION', '0.0.1' );

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

register_activation_hook( __FILE__, 'activate_global_variables' );
register_deactivation_hook( __FILE__, 'deactivate_global_variables' );

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

?>