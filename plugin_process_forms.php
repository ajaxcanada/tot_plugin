<?php
// *************************************************************
// load the hooks to deal with form data sent back from the user
add_action('init', 'process_post'); // This loads the function thats used to capture form information when they press a button

// *************************************************************
// PROCESS FIELDS FORMS AND GROUP FORMS


function change_group($user_id, $group_requested){
    global $wpdb;
     //global $myMsg;
    $db_table = $wpdb->prefix."tot_db_records";
    //$myMsg .= "db (". $db_table. ") user id=(". $user_id. ") function CHANGE_GROUP (".$group_requested.")";
    //$myMsg .= "<br>UPDATE $db_table SET group_selected = '$group_requested' WHERE user_id = '$user_id'" ;
    $wpdb->query("UPDATE $db_table SET group_selected = '$group_requested' WHERE user_id = '$user_id'");
}
        
if(!function_exists('process_post')) {
function process_post(){
    global $wpdb;
    global $myMsg;
    
    $form_nonce = filter_input(INPUT_POST, 'db_update_secure_nonce_field', FILTER_SANITIZE_STRING);
    $group_requested = filter_input(INPUT_POST, 'navigator', FILTER_SANITIZE_SPECIAL_CHARS);
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    $update_main_form = filter_input(INPUT_POST, 'UPDATE_MAIN_RECORD', FILTER_SANITIZE_SPECIAL_CHARS);

    if (! empty($form_nonce) && (wp_verify_nonce($form_nonce, 'db_update_nonce_field'))){
        if (isset( $group_requested )){
            $user_id = get_current_user_id();
            change_group($user_id, $group_requested);
        } if (isset( $update_main_form )){ 
            $myMsg .= " User SELECTED >".$update_main_form. "< ";
        }}
 //========================= GOOD CODE ABOVE THIS LINE. LOL ============================================================
        // check the hidden field on each form to get form name. check security
	// *************************************************************
	if (isset( $_POST['fields_form'] ) && wp_verify_nonce($_POST['db_update_secure_nonce_field'], 'db_update_nonce_field') ){
		$db_field_items = return_db_fields("fields"); // fetch table column names
		$db_table = $wpdb->prefix."tot_db_fields";//tot_db_fields
		$fields_array_data = ['field_name' => "enter_a_name", 'field_title'=> "enter_a_title" ]; // defaults for the fields table
		$fields_array_data_type = ['%s', '%s' ];
	} elseif (isset( $_POST['groups_form'] ) && wp_verify_nonce($_POST['db_update_secure_nonce_field'], 'db_update_nonce_field') ) {
		$db_table = $wpdb->prefix."tot_db_groups"; // set the table name to tot_db_groups
		//$db_items = return_db_fields('groups'); // get the group table field names
		$fields_array_data = ['Group_name' => "enter_a_name" ]; // defaults for the groups table
		$fields_array_data_type = ['%s'];
	} elseif (isset( $_POST['records_form'] ) && wp_verify_nonce($_POST['db_update_secure_nonce_field'], 'db_update_nonce_field') ) {
		$db_table = $wpdb->prefix."tot_db_records"; // set the table name to tot_db_groups
		
		$fields_array_data = ['user_name'=>$current_user , 'data_name' => "enter_a_name" ]; // records defaults 
		$fields_array_data_type = ['%s'];
	} elseif (isset( $_POST['navigator'] ) && wp_verify_nonce($_POST['db_update_secure_nonce_field'], 'db_update_nonce_field') ) {
		
            // not used any more. 
            //$data_group = $_POST['navigator'];
		// user pressed a navigation button
	}  elseif (isset( $_POST['record_field_form'] ) && wp_verify_nonce($_POST['db_update_secure_nonce_field'], 'db_update_nonce_field') ) {
		$db_table = $wpdb->prefix."tot_db_records"; // set the table name to tot_db_groups
		$db_field_table = $wpdb->prefix."tot_db_fields"; // set the table name to tot_db_groups
		
		$fields_array_data = ['user_name'=>$current_user , 'data_name' => "enter_a_name" ]; // records defaults 
		$fields_array_data_type = ['%s'];
		
	} elseif (isset( $_POST['send_sms_message'] ) && wp_verify_nonce($_POST['db_update_secure_nonce_field'], 'db_update_nonce_field') ) {
		$number = $_POST['phone']; // records defaults 
		$name = $_POST['sms_user_name']; // records defaults 
		$message = $_POST['message']; // records defaults 
		
		$return_message = send_sms($number,$name,$message);
	} 

		$field_id = $_POST[id];  //$field_names = explode(",",$db_field_items);

		// user selected delete
		// *************************************************************
		if (isset( $_POST['DELETE_RECORD'] ) && wp_verify_nonce($_POST['db_update_secure_nonce_field'], 'db_update_nonce_field') ){
			//echo " record id =" . $_POST['id'] . " button= " . $_POST[DELETE_RECORD] . "  == " . $field_id ;
			$wpdb->query("DELETE FROM $db_table WHERE id = '$field_id'");
			// may want to add a jquery pop up: are you sure?!
			// *************************************************************
		}

		// user selected UPDATE or SAVE
		// *************************************************************
		if (isset( $_POST['UPDATE_RECORD'] ) && wp_verify_nonce($_POST['db_update_secure_nonce_field'], 'db_update_nonce_field') ){
			// *************************************************************
			foreach ($_POST as $param_name => $param_val) {  					//cycle through the fields here
				$clean_param = rtrim($param_name, '_');							// strip off extra _ off the end opf the data

				switch($clean_param){											// jump over these pieces of data
				case 'fields_form': break;
				case 'groups_form': break;
				case 'id':break;
				case 'db_update_secure_nonce_field':break;
				case '_wp_http_referer':break;
				case 'UPDATE_RECORD':break;
				default:
					//echo "==> UPDATE $db_table SET $clean_param = '$param_val' WHERE id = '$field_id' <br />\n";
					$wpdb->query("UPDATE $db_table SET $clean_param = '$param_val' WHERE id = '$field_id'");
				} 	// end of switch
			} 	// end foreach
		} 	// end if

		// *************************************************************
		if (isset( $_POST['ADD_RECORD'] ) && wp_verify_nonce($_POST['db_update_secure_nonce_field'], 'db_update_nonce_field')){
			//echo "user selected add fields";
			$wpdb->INSERT( $db_table, $fields_array_data, $fields_array_data_type );
		} 	// end if
		
		// *************************************************************
		if (isset( $_POST['ADD_RECORD_COLUMN'] ) && wp_verify_nonce($_POST['db_update_secure_nonce_field'], 'db_update_nonce_field')){
			if (empty($_POST['new_record_name_input'])){
			$myMsg .= "name of the column field is blank >>". $new_field_name. "<< "; 
			
			}else{
			$new_field_name = $_POST['new_record_name_input'];
			$new_field_atributes = "text";
			
			$myMsg .= "Adding column; ". $new_field_name . " with atts; " .$new_field_atributes ;

			$proper_name = str_replace(' ', '_', $new_field_name);
			if (isset( $_POST['new_record_group'] )){
				$new_group = $_POST['new_record_group'];
				$myMsg .= $new_group;
			}
					
			mysql_query("ALTER TABLE " . $db_table . " ADD " . $proper_name . " " . $new_field_atributes);

			$fields_array_data = ['field_name' => $proper_name, 'field_title'=> $new_field_name, 'field_group' => $new_group ]; 
			
			$fields_array_data_type = ['%s', '%s', '%s' ];		
			$wpdb->INSERT( $db_field_table, $fields_array_data, $fields_array_data_type );

			}
		} 	// end if
		// *************************************************************
		if (isset( $_POST['DELETE_RECORD_COLUMN'] ) && wp_verify_nonce($_POST['db_update_secure_nonce_field'], 'db_update_nonce_field')){
			if (empty($_POST['new_record_name_input'])){
			//echo "user selected add new column named". 
			
			}else{
				$db_records = $wpdb->prefix."tot_db_records"; 
				$query_records = "SELECT * FROM {$db_records}"; // 
				$record_results= mysql_query($query_records) or die(mysql_error());// get db
		
				$new_field_name = $_POST['new_record_name_input'];
				$proper_name = str_replace(' ', '_', $new_field_name);
				
				// check if column exists and delete it
				while($row = mysql_fetch_assoc($record_results)){ 
					foreach($row as $cname => $cvalue){
					if ( $proper_name==$cname){
						mysql_query("ALTER TABLE " . $db_table . " DROP " . $proper_name);
						break 2;
					}
				}
				}
				//check if row exists and delete it
				$db_fields = $wpdb->prefix."tot_db_fields"; 
				$query_fields = "SELECT `field_name`, `id` FROM {$db_fields}"; // 
				$field_results= mysql_query($query_fields) or die(mysql_error());// get db
				global $myMsg;
				// something not working
				while($row = mysql_fetch_assoc($field_results)){ 
					foreach($row as $cname => $cvalue){
						//$myMsg .= $count++  . " w ". $proper_name . "  w ". $new_field_name . " | ";
						if ( $proper_name==$cvalue){
							$myMsg .= "proper Name". $proper_name;
							$wpdb->query("DELETE FROM $db_fields WHERE `field_name` = '" . $cvalue . "'");
							break 2;
						}
					}
				}
			}
		}}
}

?>