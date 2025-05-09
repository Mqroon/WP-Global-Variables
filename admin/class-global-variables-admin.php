<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://github.com/Mqroon
 * @since      1.0.0
 *
 * @package    Global_Variables
 * @subpackage Global_Variables/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Global_Variables
 * @subpackage Global_Variables/admin
 * @author     Walker Alexander <walker@faciledesigns.com>
 */
class Global_Variables_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Global_Variables_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Global_Variables_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style('dashicons');

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/global-variables-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Global_Variables_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Global_Variables_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_dequeue_script('gv-form-script');
		wp_deregister_script('gv-form-script');
	
		wp_register_script('gv-form-script', plugins_url() . '/global-variables/admin/js/global-variables-admin.js', array('jquery'), $this->version, true);
		wp_enqueue_script('gv-form-script');
		wp_localize_script('gv-form-script', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
	}

}
