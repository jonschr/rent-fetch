<?php

add_action( 'admin_menu', 'rentfetch_add_documentation_sidebar_link');
function rentfetch_add_documentation_sidebar_link() {
    
    global $submenu;
    $menu_slug = "edit.php?post_type=floorplans"; // used as "key" in menus
   
    $submenu[$menu_slug][] = array(
        'Documentation', 
        'manage_options', 
        'https://github.com/jonschr/apartment-sync',
    );
}

add_action( 'admin_footer', 'rentfetch_admin_menu_open_new_tab' );    
function rentfetch_admin_menu_open_new_tab() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#menu-posts-floorplans li a').each(function () {
            if ($(this).text() == 'Documentation') {
                $(this).css('color', 'yellow');
                $(this).attr('target','_blank');
            }
        });
    });
    </script>
    <?php
}