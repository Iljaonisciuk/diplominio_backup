<?php


//First solution : one file
add_action( 'admin_enqueue_scripts', 'sshop_load_admin_style' );
function sshop_load_admin_style() {
    wp_enqueue_style( 'sshop_admin_css', get_template_directory_uri() . '/assets//css/admin.css' );
}
