<?php
// *************************************************************
// HIDE THE HEADER AND TITLE OFF THE PAGE
function hide_header_on_this_page(){
    $css_out .= "<style>";
    $css_out .= "#header {display: none; }";
    $css_out .= ".entry-title {display: none;}";
    $css_out .= "</style>";
    return $css_out;
}

// *************************************************************
// CREATE THE MAIN FORM HERE

if(!function_exists(view_user_main_form)) {
function view_user_main_form(){
    //global $user_nav_selection;
    $start = microtime(TRUE);  // starts a microtimer called start
    // SET THE DEAFULT NAVIGATION PAGE
    $out .= hide_header_on_this_page();
    $out .= Create_main_styles();
    $current_user_id = check_user_id();

    $out .= "<html><body>"; // start of the html
    $out .= "<div id='data_set'>"; // DIV_PAGE ==================================
    if ($current_user_id == "") {
        echo "you need to be logged in!". $current_user_id;
        exit;
    } else {
        $group = check_user_last_access($current_user_id);
    
        $out .= create_message_area(); // MESSAGE DIV CODE
        $out .= create_navigation(); // NAVIGATION DIV CODE
        $out .= create_main_area($group); // MAIN DIV CODE
        $out .= create_main_footer($start); // FOOTER CODE
    }	
    $out .= "</div> ";	// </ END OF MAIN FORM DIV
    $out .= "</body></html> ";	// </ end of our html
    return $out;
    }
}

// *************************************************************
// CREATE THE MAIN FORM HERE
function create_main_area($user_nav_selection){
    global $wpdb;  // wordpress database connection
    
    // FIRST LOAD THE FIELD TABLE BASED ON THE USER SELECTION OR DEFAULT
    $db_fields = $wpdb->prefix."tot_db_fields"; // load fields records
    $query_fields = "SELECT `field_name` FROM {$db_fields} where field_group = '".$user_nav_selection."'"; //fields string to pass to mysql query
    $fields_results= mysql_query($query_fields) or die(mysql_error());// get fields from database

    $num_cols = mysql_num_fields($fields_results);
    $num_rows = mysql_num_rows($fields_results);
    $values = array();

    // INIT ARRAY
    for ($c=1;$c<=$num_cols;$c++) { for ($r=1;$r<=$num_rows;$r++) { $values['col_'.$c][$r] = array(); }}
    $c = 1;  $r = 1; // INIT VARIABLES
    // LOAD THE FIELDNAMES INTO THE ARRAY
    while($fieldrow = mysql_fetch_assoc($fields_results)){  // load the group_rows of fields data 
        $c=1; // reset back to column 1
        foreach($fieldrow as $field_name => $field_value){
            $values['col'.$c][$r] = $field_value;
            $c++;
        }
        $r++;
    }

    // USE THE ARRAY DATA TO CREATE A SEARCH STRING
    $db_fields_names .= "`id`, `user_id`, ";
    for ($r=1;$r<=$num_rows;$r++) { 
	for ($c=1;$c<=$num_cols;$c++) {
            $db_fields_names .= "`" . $values['col'.$c][$r] . "`, "; 
        }
    }

    // add id, username, date_recorded (add modified) this onto the end of the array    
    $db_fields_names .= "`date_recorded`";
    // send the field name out for debug

    $db_records = $wpdb->prefix."tot_db_records"; // load db records
    $query_records = "SELECT ". $db_fields_names ." FROM {$db_records}"; // records string to pass to mysql query
    $records_results= mysql_query($query_records) or die(mysql_error()); // get records from database

    // DIV_MAIN_FORM ============================
    $out .= "<div id='TOT_MAIN_USER_FORM'>"; 
    $out .= "<form name='main_form_data' method='POST'>"; 

    $out .= "<input type='hidden' name='main_form'>"; // unique identifier for this form
    $out .= wp_nonce_field('db_update_nonce_field','db_update_secure_nonce_field'); // SECURITY

//  $out .= " Record Name:<span title = '$data[1]'><input type='text' tooltip='test' name='new_record_name_input'></span><br>"; // capture the new record name
  
    // OUTPUT THE DATA ==========================
    $r_count = 0;
    while($row = mysql_fetch_assoc($records_results)){  // load the group_rows of fields data 
        $out .= "<div id='roz'>"; //_" . $r_count++."'>" ;
        foreach($row as $fieldname => $fieldvalue){
            switch($fieldname){
                case 'id':
                //case 'user_name':
                case 'date_recorded':
                    $out .= "<input type='hidden' name='$fieldname' value='$fieldvalue'>"; break;
                default:
                    $out .= "<label for='$fieldname'>$fieldname</label>";
                    $out .= "<input type='text' name='$fieldname' value='$fieldvalue'>"; // capture the new record name
            }
        // $out .=  $fieldvalue ; // output column to screen
        // if($count++ == 8){break;}
       }
       $out .= " </div> ";        
    } 

    $out .= "<input type='submit' name='UPDATE_MAIN_RECORD' value='update record'>"; // the add new button
    $out .= "<input type='submit' name='DELETE_MAIN_RECORD' value='delete record'>"; // the add new button
    $out .= "</form>"; // End the form 
    $out .= "</div>";  // end DIV_MAIN_FORM  ======================================
return $out;
}


// ========================================
// USER MESSAGE AREA
function create_message_area(){
	global $myMsg;
//        global $user_nav_selection;
        // DIV_MY_MESSAGE ============================
	$out .= "<div id='message'>";
	$out .= "Debug message=" . $myMsg ."<br>";
	$out .= "Group Name=" .  $_SESSION["Group"] ."<br>";
	$out .= "Your record management area"; 
	$out .= "</div>"; 
	// end DIV_MY_MESSAGE
        return $out;

}
function create_main_footer($start){
    // DIV_FOOTER	
	$out .= "<div id='TOT_FOOTER'>"; 
	$out .= "<div id='my_timer'>&nbsp;Elapsed time=";
	$out .= page_timer ($start) ; //    
	$out .= "</div>";
	$out .= "</div>";
        
    return $out;
}
function create_navigation(){
    // DIV_NAVIGATION ============================
	global $wpdb;  // wordpress database connection
	        
        $db_groups = $wpdb->prefix."tot_db_groups"; // load fields records
	$query_groups = "SELECT * FROM {$db_groups}"; //fields string to pass to mysql query
	$groups_results= mysql_query($query_groups) or die(mysql_error());// get groups from database
	
	$out .= "<div id='TOT_NAV'><br>"; 
	$out .= "<form name='navigation' method='POST'>"; // Form - new_record
	$out .= "<input type='hidden' name='navigation'>"; // unique identifier for this form
	$out .= wp_nonce_field('db_update_nonce_field','db_update_secure_nonce_field'); // SECURITY
	
	while($db_row = mysql_fetch_assoc($groups_results)){  // load the group_rows of fields data 
            foreach($db_row as $col_name => $col_value){
                if($col_name != 'id'){
                        $out .= "<input type='submit' name='navigator' value='$col_value'>"; // the add new button
                }
            }
            $out .= "<br>"; // new line
	} 
	
	$out .= "</form>"; // End the form 
	$out .= "</div>"; 
        return $out;
                
}
function check_user_id (){
    if(!is_user_logged_in()) {
       //no user logged in
    } else {
        $current_user_id = get_current_user_id();
    }
    return $current_user_id;
}

function check_user_last_access($user_id){
    global $wpdb;  // wordpress database connection
   
    $db_records = $wpdb->prefix."tot_db_records"; // load records
    $query_records = "SELECT `group_selected` FROM {$db_records} where user_id = '$user_id'"; // add where id = userid 
    $record_results= mysql_query($query_records) or die(mysql_error());// get group_selected from database
    
    while($db_row = mysql_fetch_assoc($record_results)){
        foreach($db_row as $col_name => $col_value){ 
            $ret = $col_value; 
        }} 
        
        if ($col_value== "") {
           // nothing setup for this user, adding a new entry now
            load_table_w_array('tot_db_records', array('group_selected'=>'user information', 'user_id'=>$user_id));
            $ret .= "user information"; 
        }
        return $ret;
}

function page_timer ($start) {
	$finish = microtime(TRUE); 	
	$totaltime = $finish - $start;
	return $totaltime;
	}

?>