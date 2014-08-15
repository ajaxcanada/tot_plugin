<?php
// *************************************************************
// HIDE THE HEADER AND TITLE OFF THE PAGE
function hide_header_on_this_page(){
    $css_out .= "<style>";
    $css_out .= "#header {display: none; }";
    $css_out .= ".entry-title {display: none;}";
    $css_out .= "</style>";
    //echo "this is a test";
    echo $css_out;
}













?>