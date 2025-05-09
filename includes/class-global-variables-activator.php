<?php

/**
 * Fired during plugin activation
 *
 * @link       https://https://github.com/Mqroon
 * @since      1.0.0
 *
 * @package    Global_Variables
 * @subpackage Global_Variables/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Global_Variables
 * @subpackage Global_Variables/includes
 * @author     Walker Alexander <walker@faciledesigns.com>
 */
class Global_Variables_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

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

}
