<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://https://github.com/Mqroon
 * @since      1.0.0
 *
 * @package    Global_Variables
 * @subpackage Global_Variables/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Global_Variables
 * @subpackage Global_Variables/includes
 * @author     Walker Alexander <walker@faciledesigns.com>
 */
class Global_Variables {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Global_Variables_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'GLOBAL_VARIABLES_VERSION' ) ) {
			$this->version = GLOBAL_VARIABLES_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'global-variables';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Global_Variables_Loader. Orchestrates the hooks of the plugin.
	 * - Global_Variables_i18n. Defines internationalization functionality.
	 * - Global_Variables_Admin. Defines all hooks for the admin area.
	 * - Global_Variables_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-global-variables-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-global-variables-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-global-variables-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-global-variables-public.php';

		$this->loader = new Global_Variables_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Global_Variables_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Global_Variables_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Global_Variables_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		add_action('admin_menu', 'gv_admin_menu');

		add_filter('plugins_api', 'github_plugin_info', 20, 3);
		add_filter('site_transient_update_plugins', 'check_github_update');

		add_action('wp_ajax_add_new_variable', 'gv_add_new_variable');
		add_action('wp_ajax_delete_variable', 'gv_delete_variable');
		add_action('wp_ajax_update_variable', 'gv_update_variable');
		add_action('wp_ajax_refresh_variable_data', 'gv_refresh_variable_data');


		function gv_admin_page(){
			global $wpdb;
			$table_name = $wpdb->prefix . "globalVariables"; 
		
			$query = $wpdb->prepare("SELECT * FROM $table_name", []);
			$results = $wpdb->get_results($query);
		
			include plugin_dir_path(__DIR__) . 'admin/partials/admin-wrapper.php';
			include plugin_dir_path(__DIR__) . 'admin/partials/form-wrapper.php';
			include plugin_dir_path(__DIR__) . 'admin/partials/variables-wrapper.php';
		}
		function gv_admin_menu() {
			add_menu_page( 'Global Variables Settings Page', 'Global Variables', 'manage_options', 'globalvariables.php', 'gv_admin_page', 'dashicons-database', 6  );
		}


		function check_github_update($transient) {
			if (!$transient || !is_object($transient)) {
				$transient = new stdClass(); // Ensure `$transient` is an object
			}

			$cache_key = 'gv_github_plugin_update_data';
			$cached_data = get_transient($cache_key);
		
			$plugin_file = "global-variables/global-variables.php";
			$plugin_slug = 'global-variables';
			$plugin_data = get_plugin_data(WP_PLUGIN_DIR . "/$plugin_slug/$plugin_slug.php");
			$current_version = $plugin_data['Version'];
		
			if ($cached_data === "APICALLRATES") {
				return $transient;
			} else if ($cached_data !== false) {
				$transient->response[$plugin_file] = $cached_data;
				return $transient; // Use cached response if available
			}
		
			$github_api_url = "https://api.github.com/repos/Mqroon/WP-Global-Variables/releases/latest";
			$response = wp_remote_get($github_api_url);
		
			if (is_wp_error($response)) {
				return $transient;
			}
		
			$body = json_decode(wp_remote_retrieve_body($response), true);
		
			if (!isset($body["tag_name"]) || empty($body["tag_name"])) {
				set_transient($cache_key, "APICALLRATES", 20);
				return $transient;
			}
		
			$new_version = ltrim($body["tag_name"], "v");

			if (version_compare($current_version, $new_version, '<')) {
				$zip_url = false;
				foreach ($body['assets'] as $asset) {
					if (strpos($asset['name'], 'global-variables.zip') !== false) {
						$zip_url = $asset['browser_download_url'];
						break;
					}
				}
				if (!$zip_url) {
					set_transient($cache_key, "APICALLRATES", 100);
					return $transient;
				}
				error_log($zip_url);

				$update_data = (object) [
					'slug' => $plugin_slug,
					'plugin' => "/$plugin_slug/$plugin_slug.php",
					'new_version' => $new_version,
					'package' => $zip_url,
					'url' => 'https://github.com/Mqroon/WP-Global-Variables',
					'name' => 'Global Variables',
					'version' => $new_version,
					'tested' => '6.3', // Adjust based on latest WordPress version compatibility
					'requires' => '5.8', // Minimum WP version
					'author' => 'Walker Alexander',
					'homepage' => 'https://github.com/Mqroon/WP-Global-Variables',
				];
			} else {
				set_transient($cache_key, "APICALLRATES", 100);
				return $transient;
			}
			$transient->response[$plugin_file] = $update_data;
			// Cache the update response for 12 hours (43200 seconds)
			set_transient($cache_key, $update_data, 100);
		
			return $transient;
		}
		
		function github_plugin_info($result, $action, $args) {
			if ($action !== 'plugin_information' || $args->slug !== 'global-variables') {
				return $result;
			}
		
			$github_api_url = "https://api.github.com/repos/Mqroon/WP-Global-Variables/releases/latest";
			$response = wp_remote_get($github_api_url);
		
			if (is_wp_error($response)) {
				return $result;
			}
			$body = json_decode(wp_remote_retrieve_body($response), true);
		
			if (!isset($body["tag_name"]) || empty($body["tag_name"])) {
				return $result;
			}
		
			$result = (object) [
				'name' => 'Global Variables',
				'slug' => 'global-variables',
				'version' => $body["tag_name"],
				'author' => 'Walker Alexander',
				'homepage' => 'https://github.com/Mqroon/WP-Global-Variables',
				'download_link' => $body["zipball_url"],
				'sections' => [
					'description' => 'Global Variables allows you to define reusable variables accessible via shortcodes.',
					'installation' => '1. Upload the plugin folder to `/wp-content/plugins/`.<br>
									   2. Activate the plugin from the "Plugins" menu in WordPress.',
					'faq' => 'Q: How do I reference a variable?<br>
							  A: Use the shortcode `[gv name="example"]`.<br>
							  Q: How do I create a variable?<br>
							  A: Visit the admin page and create a variable with a name and value.',
					'changelog' => $body["body"],
				]		
			];
		
			return $result;
		}


		function gv_add_new_variable() {
			if (!current_user_can('administrator')) {
				wp_die('You do not have permission to access this action');
				return;
			}
			
			global $wpdb;
			$table_name = $wpdb->prefix . "globalVariables"; 
			$custom_input = sanitize_text_field($_POST['custom_input']);
			$custom_name = sanitize_text_field($_POST['name']);
		
			$query = $wpdb->prepare("INSERT INTO $table_name (name, text) VALUES (%s, %s)", [$custom_name, $custom_input]);
			
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
			
			$query = $wpdb->prepare("DELETE FROM $table_name WHERE id = %d", [$item_id]);
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
			
			$query = $wpdb->prepare("UPDATE $table_name SET name = %s, text = %s WHERE id = %d", [$new_custom_name, $new_custom_input, $item_id]);
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
		
			$query = $wpdb->prepare("SELECT * FROM $table_name", []);
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
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Global_Variables_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		add_shortcode( 'gv', 'gv_shortcode_handler' );
		
		function gv_shortcode_handler ( $atts ) {
			global $wpdb;
			$table_name = $wpdb->prefix . "globalVariables"; 
		
		
			$a = shortcode_atts( array(
				'name' => 'name',
			), $atts );
		
			$query = $wpdb->prepare("SELECT * FROM $table_name WHERE name = %s", [$a['name']]);
			$results = $wpdb->get_results($query);
		
			if(count($results) > 0) {
				return $results[0]->text;
			} else {
				return "Error: No variable found \"[{$a['name']}]\"";
			}
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Global_Variables_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
