<?php

// *************************************************************
// CREATE THE MAIN FORM HERE
if (!function_exists(view_user_main_form)) {

    function view_user_main_form() {

        $start = microtime(TRUE);  // starts a microtimer called start
        // OUTPUT PAGE HTML     
        $out .= "<html><body>"; // start of the html
        $out .= "<div id='data_set'>"; // DIV_PAGE ==================================
        
        // GET USER ID OR EXIT TO WELCOME
        
        $cuid = check_user_id();
        //$out .= $cuid;
        $group = check_user_last_access($cuid);

        $out .= create_message_area(); // MESSAGE DIV CODE
        // SET THE DEFAULT NAVIGATION
        $out .= create_navigation($group); // NAVIGATION DIV CODE
        // CREATE THE MAIN AREA OF THE FORM
        $out .= create_main_area($cuid, $group); // MAIN DIV CODE
        // CREATE FOOTER AREA
        $out .= create_main_footer($start); // FOOTER CODE
        // FINISH THE HTML
        
        
        $out .= "</div> "; // </ END OF MAIN FORM DIV
        $out .= "</body></html> "; // </ end of our html
        // SEND THAT BACK TO MAIN
        return $out;
    }

}

// LOAD THE FIELDS TITLES INFO FOR THE GROUP REQUESTED BY THE USER
function load_the_group_names($user_group_selected) {
    global $wpdb;  // wordpress database connection
    // FIRST LOAD THE FIELD TABLE BASED ON THE USER SELECTION OR DEFAULT
    $db_fields = $wpdb->prefix . "tot_db_fields";
    $query_field_titles = "SELECT field_title FROM {$db_fields} where field_group = '" . $user_group_selected . "'";
    $field_titles = mysql_query($query_field_titles) or die(mysql_error()); // get fields from database
    //$db_field_name .= "`id`, `user id`, ";
    while ($fieldrow = mysql_fetch_assoc($field_titles)) {  // load the group_rows of fields data 
        foreach ($fieldrow as $field_name => $field_value) {
            $db_field_name .= "<input type='text' style='color: #000;' readonly name='' value='$field_value'>";
        }
    }
    //$db_field_name .= "`date recorded`";
    return $db_field_name;
}

// LOAD THE FIELDS TABLE INFO FOR THE GROUP REQUESTED BY THE USER
function load_the_group_data($user_group_selected) {
    global $wpdb;  // wordpress database connection
    // FIRST LOAD THE FIELD TABLE BASED ON THE USER SELECTION OR DEFAULT
    $db_fields = $wpdb->prefix . "tot_db_fields";

    $query_fields = "SELECT field_name FROM {$db_fields} where field_group = '" . $user_group_selected . "'";
    $fields_results = mysql_query($query_fields) or die(mysql_error()); // get fields from database

    $num_cols = mysql_num_fields($fields_results);
    $num_rows = mysql_num_rows($fields_results);
    $values = array();

    // INIT ARRAY
    for ($c = 1; $c <= $num_cols; $c++) {
        for ($r = 1; $r <= $num_rows; $r++) {
            $values['col_' . $c][$r] = array();
            $headers['col_' . $c][1] = array();
        }
    }
    // INIT VARIABLES
    $c = 1;
    $r = 1;
    // LOAD THE FIELDNAMES INTO THE ARRAY
    while ($fieldrow = mysql_fetch_assoc($fields_results)) {  // load the group_rows of fields data 
        $c = 1; // reset back to column 1
        foreach ($fieldrow as $field_name => $field_value) {
            $values['col' . $c][$r] = $field_value;
            $c++;
        }
        $r++;
    }
    //ADD ID AND USER_ID FIELDS TO THE SEARCH STRING
    $db_fields_names .= "`id`, `user_id`, ";
    // USE THE ARRAY DATA TO CREATE A SEARCH STRING
    for ($r = 1; $r <= $num_rows; $r++) {
        for ($c = 1; $c <= $num_cols; $c++) {
            $db_fields_names .= "`" . $values['col' . $c][$r] . "`, ";
        }
    }
    // ADD date_created AND date_modified ONTO THE END OF THE STRING
    $db_fields_names .= "`date_recorded`";
    return $db_fields_names;
}

// *************************************************************
// CREATE THE MAIN FORM HERE
function create_main_area($user_id, $user_group_selected) {
    global $wpdb;  // wordpress database connection
    // GET THE FIELD NAMES FOR THE GROUP THE USER SELECTED
    $titles = load_the_group_names($user_group_selected);
    $db_fields_names = load_the_group_data($user_group_selected);

    $db_records = $wpdb->prefix . "tot_db_records"; // load db records
    $query_records = "SELECT " . $db_fields_names . " FROM {$db_records} WHERE user_id={$user_id}"; // records string to pass to mysql query
    $records_results = mysql_query($query_records) or die(mysql_error()); // get records from database
    // DIV_MAIN_FORM ============================
    $out .= "<div id='TOT_MAIN_USER_FORM'>";
    $out .= "<form name='main_form_data' method='POST'>";

    $out .= "<input type='hidden' name='main_form'>"; // unique identifier for this form
    $out .= wp_nonce_field('db_update_nonce_field', 'db_update_secure_nonce_field'); // SECURITY
    $out .= $user_group_selected.'<br>';
    // OUTPUT THE DATA ==========================
    $r_count = 0;
    $out .= $titles;
    while ($row = mysql_fetch_assoc($records_results)) {  // load the group_rows of fields data 
        $out .= "<div id='roz'>"; //_" . $r_count++."'>" ;
        foreach ($row as $fieldname => $fieldvalue) {
            switch ($fieldname) {
                case 'id':
                case 'user_id':
                case 'date_recorded':
                    $out .= "<input type='hidden' name='$fieldname' value='$fieldvalue'>";
                    break;
                default:
                    //$out .= "<label for='$fieldname'>$fieldname</label>";
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
function create_message_area() {
    global $myMsg;
    // global $user_group_selected;
    // DIV_MY_MESSAGE ============================
    $out .= "<div id='message'>";
    if (isset($myMsg)) {
        $out .= "Debug message=" . $myMsg . "<br>";
    }

    //$out .= "Your record management area";
    $out .= "</div>";
    // end DIV_MY_MESSAGE
    return $out;
}

function create_main_footer($start) {
    // DIV_FOOTER	
    $out .= "<div id='TOT_FOOTER'>";
    $out .= "<div id='my_timer'>&nbsp;Elapsed time=";
    $out .= page_timer($start); //    
    $out .= "</div>";
    $out .= "</div>";

    return $out;
}

function create_navigation($group) {
// DIV_NAVIGATION ============================
    global $wpdb;  // wordpress database connection

    $db_groups = $wpdb->prefix . "tot_db_groups"; // load fields records
    $query_groups = "SELECT * FROM {$db_groups}"; //fields string to pass to mysql query
    $groups_results = mysql_query($query_groups) or die(mysql_error()); // get groups from database

    $out .= "<div id='TOT_NAV'>";
    $out .= "<form name='navigation' method='POST'>"; // Form - new_record
    $out .= "<input type='hidden' name='navigation'>"; // unique identifier for this form
    $out .= "<input type='hidden' name='navigation'>"; // unique identifier for this form
    $out .= wp_nonce_field('db_update_nonce_field', 'db_update_secure_nonce_field'); // SECURITY
    //$out .= "<span id='new_style'>". nav_div_label . "</span>";// . $group; //LABEL FOR THIS DIV
    $out .= nav_div_label ;// . $group; //LABEL FOR THIS DIV
    
    while ($db_row = mysql_fetch_assoc($groups_results)) {  // load the group_rows of fields data 
        foreach ($db_row as $col_name => $col_value) {
            if ($col_name != 'id') {
                $out .= "<input type='submit' name='navigator' value='$col_value'>"; // the add new button
            }
        }
        $out .= "<br>"; // new line
    }

    $out .= "</form>"; // End the form 
    $out .= "</div>";
    return $out;
}

function check_user_id() {
    hide_header_on_this_page();

    if (!is_user_logged_in()) {
        echo unregistered_user_welcome_message;
        exit;
        //no user logged in
    } else {
        $cuid = get_current_user_id();
    }
    return $cuid;
}

function check_user_last_access($user_id) {
    global $wpdb;  // wordpress database connection

    $db_records = $wpdb->prefix . "tot_db_records"; // load records
    $query_records = "SELECT `group_selected` FROM {$db_records} where user_id = '$user_id'"; // add where id = userid 
    $record_results = mysql_query($query_records) or die(mysql_error()); // get group_selected from database

    while ($db_row = mysql_fetch_assoc($record_results)) {
        foreach ($db_row as $col_name => $col_value) {
            $ret = $col_value;
        }
    }

    if ($col_value == "") {
        // nothing setup for this user, adding a new entry now
        load_table_w_array('tot_db_records', array('group_selected' => 'user information', 'user_id' => $user_id));
        $ret .= "user information";
    }
    return $ret;
}

function page_timer($start) {
    $finish = microtime(TRUE);
    $totaltime = $finish - $start;
    return $totaltime;
}

?>