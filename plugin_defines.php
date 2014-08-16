<?php

/* putting all the outputs in defines to kiss */

define(unregistered_user_welcome_message, 'Welcome to ToolsOnTools.com<br>'
        . 'This is a members only page so you need to be logged in to access it!<br>'
        . '<a href="' . wp_registration_url() . ' title=' . __('Register') . '">' . __('REGISTER') . '</a>'
        . 'or <a href=' . site_url('wp-login.php') . '>LOG IN</a>'
        . ' to gain access to all your cool stuff'
        . '<br><br>Have a great day!');

//define(nav_div_label, 'Groups');
define(nav_div_label, 'Group<br>')


?>