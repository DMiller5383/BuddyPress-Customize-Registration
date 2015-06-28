<?php

/**
 * Plugin Name: BuddyPress Custom Registration Fields Display
 * Plugin URI:  http://buddypresser.com
 * Description: Select which fields to add to Buddypress registration.
 * Author:      Daniel Miller
 * Author URI:  http://buddypresser.com
 * Text Domain: 
 */

/**
 * Set up database field in bp_xprofile_fields table to indicate whether to display on registration page.
 *
 * @return wpdb->query
 * 
 */

function crfd_install() {

	global $wpdb;

	$table_name = $wpdb->prefix . 'bp_xprofile_fields';

	 $sql = "ALTER TABLE " . $table_name . " ADD crfd_reg_display boolean;";

	/**
	* Filters sql query which adds crfd_reg_display field to database when plugin is initialized.
	* @param string $sql sql query to add_crfd_reg_display_field.
	*/ 

	 $sql = apply_filters('crfd_install', $sql);

	 return $wpdb->query($sql);
}

register_activation_hook( __FILE__, 'crfd_install' );

/**
* After user saves field update the crfd_reg_display field based on user selection.
*
* @param object BP_XProfile_Field
* @return integer 0 or 1 
*/

function crfd_update_display_field($field) {
	
	$field = $field;
	
	global $wpdb;

	$table_name = $wpdb->prefix . 'bp_xprofile_fields';

	if ( $_POST['crfd_reg_display'] ) {

		$sql = 'update ' . $table_name . ' set crfd_reg_display=true WHERE id="' . $field->id .'";';

	} else {

		$sql = 'update ' . $table_name . ' set crfd_reg_display=false WHERE id="' . $field->id .'";';

	}

	/**
	* Filters the sql query that updates the crfd_reg_display field.
	* @param string $sql sql query to update the crfd_reg_display field
	*/ 
	
	$sql = apply_filters('crfd_update_display_field', $sql);
	
	return $wpdb->query($sql);

}

add_action('xprofile_field_before_save', 'crfd_update_display_field', 10, 1);
add_action( 'xprofile_field_after_contentbox', 'crfd_display_checkbox_for_admin', 10, 1 );


/**
* Adds checkbox to give user option to display field on registration.  Runs when admin screen is loaded for profile field.
* @param object BP_XProfile_Field
*/

function crfd_display_checkbox_for_admin($field) {
	
	$field_id = $field->id;
	ob_start();
	?>

	<?php do_action('crfd_before_crfd_postbox'); ?>
	
	<div class="postbox">
		<div class="inside">
			<?php do_action('before_crfd_checkbox'); ?>
			<input type="checkbox" name="crfd_reg_display" id = "crfd_reg_display" value="crfd_reg_display" <?= crfd_is_displayed_on_registration($field_id) ? 'checked' : ''; ?>>Display this field on registration page.<br>
			<?php do_action('after_crfd_checkbox'); ?>
		</div>
	</div>
	

	<?php do_action('crfd_after_crfd_postbox'); ?>

	<?php 

	$crfd_checkbox = ob_get_clean();

	echo $crfd_checkbox;

}

/**
* Get database bp_xprofile_fields table
* @return string
*/

function crfd_reg_display_field() {

	global $wpdb;
	$table_name = $wpdb->prefix . 'bp_xprofile_fields';

	return $table_name;
}

/**
 * Whether the field is to be displayed on registration page
 *
 * @return True if displayed on registration, otherwise False.
 */

function crfd_is_displayed_on_registration( $field_id ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'bp_xprofile_fields';
	$sql = 'SELECT crfd_reg_display FROM ' . $table_name .  ' WHERE id = '. $field_id;

	/**
	* Filters the sql query to determine whether to display the field on registration page.
	* @param string $sql sql query to get the crfd_reg_display field
	*/

	apply_filters('crfd_is_displayed_on_registration', $sql);
	
	$result = $wpdb->get_row($sql);

	$reg_display = $result->crfd_reg_display;

	if ($reg_display)
		$reg_display = true;

	return $reg_display;

}



add_filter('bp_template_stack', 'crfd_register_form_location', 10, 1);


/**
 *
 * Registers the location of new register.php template file included with plugin.
 *
 * @return string directory of custom register.php file
 * @param $location_callback string ( callback function )
 * @param $priority integer 
 */

function crfd_register_form_location($location_callback ='', $priority = 10) {

	
	$template_location = plugin_dir_path(__FILE__);
	$location_callback = $template_location;
	return $location_callback;
}











