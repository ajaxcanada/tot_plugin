<?php
// *************************************************************
// create the record cell with fields table atributes
// *************************************************************
if(!function_exists(record_form)) {
function record_form(){

    $start = microtime(TRUE);  // starts a microtimer called start
    // SET THE DEAFULT NAVIGATION PAGE
    //hide_header_on_this_page();
    
    check_user_id();
    
    global $wpdb;  // wordpress database connection
    global $myMsg;	// messages used by TOT. these are at the top of the page. 
    //$db_records = $wpdb->prefix."tot_db_records"; // load db records
    $db_fields = $wpdb->prefix."tot_db_fields"; // load fields records
    $db_groups = $wpdb->prefix."tot_db_groups"; // load fields records

    //$query_records = "SELECT * FROM {$db_records}"; // records string to pass to mysql query
    $query_fields = "SELECT * FROM {$db_fields}"; //fields string to pass to mysql query
    $query_groups = "SELECT * FROM {$db_groups}"; //fields string to pass to mysql query

    //$records_results= mysql_query($query_records) or die(mysql_error()); // get records from database
    $fields_results= mysql_query($query_fields) or die(mysql_error());// get fields from database
    $groups_results= mysql_query($query_groups) or die(mysql_error());// get groups from database

    $out .= "<script type = 'text/javascript'>function UpdateAssociatedField(which,fld) {document.getElementById(fld).value = which.value;} </script>";
    //Create_javascript();

    $out .= "<html><body>"; // start of the html
    // javascript to update fields from selection
    //$out .= hide_header_on_this_page();
    $out .= "<div id='data_set'>"; // D I V 1 - the entire form so we can easily add style scroll
    $out .= "<div id='message'>"; // START OF MESSAGE_DIV
    $out .= $myMsg ."<br>To add a new record. enter the new record information, then press ADD NEW COLUMN"; 
    $out .= "</div>"; // END OF MESSAGE_DIV

    // my sms code
    // =================================================
    $out .= "<div id='send_SMS'>"; // SMS_MESSAGE_DIV
    $out .= "<form action='' name='send_sms' method='post'>"; // Form - new_record
    $out .= "Send someone a text message<br />";

    $out .= "<label for='phone'>Phone Number:</label>";
    $out .= "<input type='text' name='phone' placeholder='XXX XXX XXXX'>"; // capture the new record name
    $out .= "<label for='name'>Users Name:</label>";
    $out .= "<input type='text' name='sms_user_name' placeholder='enter the name'>"; // capture the new record name
    $out .= "<label for='message'>Message to send:</label>";
    $out .= "<input type='text' name='message' placeholder='enter your message'>"; // capture the new record name

    $out .= wp_nonce_field('db_update_nonce_field','db_update_secure_nonce_field'); // SECURITY
    $out .= "<input type='submit' name='send_sms_message' value='send message'>"; // the add new button
    $out .= "</form>"; // end of form
    $out .= "</div>"; // end of SMS_MESSAGE

    // my record form
    // =================================================	
    $out .= "<div id='new_record_entry'>"; // MAIN_RECORD_DIV
    $out .= "<form name='new_record_field' method='POST'>"; // Form - new_record
    $out .= "<input type='hidden' name='record_field_form'>"; // unique identifier for this form
    $out .= wp_nonce_field('db_update_nonce_field','db_update_secure_nonce_field'); // SECURITY

    // create INPUT for new record name
    $out .= "<label for='new_record_name_input'>Record Name:</label>";
    $out .= "<input type='text' name='new_record_name_input' placeholder='new record name'><br>"; // capture the new record name
    // create INPUT for a new group
    $out .= "<label for='new_record_group'>Record Group:</label>";
    $out .= "<input type='text' name='new_record_group' id='new_record_group' placeholder='new group name'></label>"; // capture the new record group
    // create a SELECT dropdown for existing groups that can be used
    $out .= "<label for='existing_record_group'> ";
    $out .= "<select name='record_group' id='existing_record_group' onChange=\"UpdateAssociatedField(this,'new_record_group')\">";
    $out .= "<option value='' disabled selected>select a group to use here</option>";
    while($group_row = mysql_fetch_assoc($groups_results)){  // load the group_rows of fields data 
            foreach($group_row as $cgroupname => $cgroupvalue){}
            $out .= "<option value='" . $cgroupvalue . "' >" . $cgroupvalue . "</option>"; // output column to screen
    }
    $out .= "</select></label><br>";	


    // CREATE BUTTONS
    $out .= "<input type='submit' name='ADD_RECORD_COLUMN' value='add new column'>"; // the add new button
    $out .= "<input type='submit' name='DELETE_RECORD_COLUMN' value='Delete column'>"; // the add new button
    $out .= "<hr>";	
    // OUTPUT THE RECORDS

    $num_cols = mysql_num_fields($fields_results);
    $num_rows = mysql_num_rows($fields_results);
    $values = array(); 	$headers = array();

    for ($c=1;$c<=$num_cols;$c++) { for ($r=1;$r<=$num_rows;$r++) { $values['col_'.$c][$r] = array(); }}
    for ($c=1;$c<=$num_cols;$c++) { $headers['col_'.$c][1] = array(); }
    // load the data into ann array	
    $c = 1;  $r = 1;
    while($fieldrow = mysql_fetch_assoc($fields_results)){  // load the group_rows of fields data 
        $c=1;
        foreach($fieldrow as $field_name => $field_value){
                if ($r == 1) {
                        $headers['col'.$c][$r] = $field_name;
                }
                $values['col'.$c][$r] = $field_value;
                $c++;
        }
        $r++;
    } 
    // PRINT OUT TABLE
    $out .= "<div class='DIV_TABLE'>";
    for ($c=1;$c<=$num_cols;$c++) {
        $out .= "<div id='DIV_ROW'><input style='color: #000; font-weight: bold' readonly value='" . $headers['col'.$c][1] . "'>";
        for ($r=1;$r<=$num_rows;$r++) { 
                $out .= "<input  value='".$values['col'.$c][$r]."'>"; 
        }
        $out .= "</div>";
    }
    $out .= "<br></div>";

    // DESTROY ARRAYS 
    unset($headers); unset($values);	

    $out .= "</form>"; // End the form 
    $out .= "</div>"; // END OF MAIN_RECORD_DIV
    $out .= "</div><br>"; // end / D I V 3  / D I V 2	

    //===============================================================
    $out .= "<div id='footer_info'>"; // D I V information
    $finish = microtime(TRUE); 	
    $t=time();
    $totaltime = $finish - $start;
    $out .= "<div id='my_timer'> Date " . (date("Y/m/d",$t)) ."      Elapsed time=";
    $out .= $totaltime .  "</div>";//    CREATE A NEW RECORD=> ";
    $out .= "</div>"; // end / D I V information

    $out .= "</div></body></html> ";	// </ end of our html

    return $out;
}
}
?>