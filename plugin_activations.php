<?php
/*
 */
// function to create the DB / Options / Defaults					
function install_plugin_create_fields_table() {
    global $wpdb;
    
    $db_fields_table = $wpdb->prefix . 'tot_db_fields';

    // create the tot fields database table
    if($wpdb->get_var("show tables like '$db_fields_table'") != $db_fields_table) 
    {
        $sql = "CREATE TABLE " . $db_fields_table . " (
        `id` int(3) NOT NULL AUTO_INCREMENT,
        `field_name` varchar(32) DEFAULT 'enter field name' NOT NULL, 
        `field_title` varchar(32) DEFAULT 'enter title' NOT NULL,
        `field_default` varchar(32) DEFAULT '' NULL,
        `field_help` varchar(32) DEFAULT '' NULL,
        `field_width` varchar(32) DEFAULT '60px' NOT NULL,
        `field_group` varchar(32) DEFAULT 'none' NOT NULL,
        `field_sub_group` varchar(32) DEFAULT 'none' NOT NULL,
        `field_order` int(3) DEFAULT '0' NOT NULL,
        `field_values` varchar(32),
        `field_element` varchar(32),
        `field_validation` varchar(32),
        `field_cb_required` varchar(32),
        `field_cb_sortable` varchar(32),
        `field_cb_readonly` varchar(32),
        UNIQUE KEY id (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

function install_plugin_create_records_table(){
   	global $wpdb;
  	
	$db_records_table = $wpdb->prefix . 'tot_db_records';
 
	// create the tot fields database table
	if($wpdb->get_var("show tables like '$db_records_table'") != $db_records_table) 
	{
            $sql = "CREATE TABLE " . $db_records_table . " (
            `id`                int(3)      NOT NULL AUTO_INCREMENT,
            `date_recorded`     timestamp   NOT NULL,
            `date_modified`     datetime    NOT NULL,
            `user_id`           varchar(32) NOT NULL,
            `group_selected`    varchar(32) NOT NULL,
            `house_address`     varchar(32) NULL,
            `house_name`        varchar(32) NULL,
            `reminder_date`     date        NOT NULL,
            `reminder_time`     time        NOT NULL,
            `reminder_repeat`   varchar(32) NULL,
            `reminder_message`  varchar(32) NULL,
            `reminder_phone`    varchar(32) NULL,
            UNIQUE KEY id (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
	}
}

function install_plugin_create_groups_table(){
   	global $wpdb;
  	
	$tot_db_groups = $wpdb->prefix . 'tot_db_groups';
	// create the tot fields database table
	//hwi_tot_db_groups
	if($wpdb->get_var("show tables like '$tot_db_groups'") != $tot_db_groups) 
	{
		$sql = "CREATE TABLE " . $tot_db_groups . " (
		`id` int(3) NOT NULL AUTO_INCREMENT,
		`group_name` varchar(64) NOT NULL,
		UNIQUE KEY id (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
 
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

// LOAD THE DEFAILT DATA INTO THE TABLES
function insert_data() {
    // FIELDS
    load_table_w_array('tot_db_fields', 
        array('field_name' => 'group_selected', 'field_title' => 'group selected', 'field_group' => 'user information'));
    load_table_w_array('tot_db_fields', 
        array('field_name' => 'house_name', 'field_title' => 'House Name', 'field_help' => "House Name",  'field_group' => 'house information'  ) );
    load_table_w_array('tot_db_fields', 
        array('field_name' => 'house_address',  'field_title' => 'House Address', 'field_help' => "House Address", 'field_group' => 'house information' ) );
    load_table_w_array('tot_db_fields', 
        array('field_name' => 'reminder_date', 'field_title' => 'reminder date', 'field_group' => 'reminder'));
    load_table_w_array('tot_db_fields', 
        array('field_name' => 'reminder_time', 'field_title' => 'reminder time', 'field_group' => 'reminder'));
    load_table_w_array('tot_db_fields', 
        array('field_name' => 'reminder_repeat', 'field_title' => 'reminder repeat', 'field_group' => 'reminder'));
    load_table_w_array('tot_db_fields', 
        array('field_name' => 'reminder_message', 'field_title' => 'reminder message', 'field_group' => 'reminder'));
    load_table_w_array('tot_db_fields', 
        array('field_name' => 'reminder_phone', 'field_title' => 'reminder phone', 'field_group' => 'reminder'));
    // RECORDS
    load_table_w_array('tot_db_records', array('group_selected'=>'user information'));
    // GROUPS
     load_table_w_array('tot_db_groups', array('group_name'=>'user information'));
    load_table_w_array('tot_db_groups', array('group_name'=>'house information'));
    load_table_w_array('tot_db_groups', array('group_name'=>'house detail'));
    load_table_w_array('tot_db_groups', array('group_name'=>'reminder'));
}		

function load_table_w_array($db, $array ){
    global $wpdb;
    $wpdb->insert( ($wpdb->prefix . $db), $array ); 
    }   
   
function db_uninstall(){
	global $wpdb;
	
	// db field table
	$db_table_name = $wpdb->prefix. 'tot_db_fields';
	$wpdb->query("DROP TABLE IF EXISTS $db_table_name");
	
	// db records table
	$db_table_name = $wpdb->prefix. 'tot_db_records';
	$wpdb->query("DROP TABLE IF EXISTS $db_table_name");
	
	// db records table
	$db_table_name = $wpdb->prefix. 'tot_db_groups';
	$wpdb->query("DROP TABLE IF EXISTS $db_table_name");

}
?>