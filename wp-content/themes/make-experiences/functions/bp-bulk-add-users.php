<?php

add_action('load-users.php',function() {

if(isset($_GET['action']) && isset($_GET['bp_gid']) && isset($_GET['users'])) {
    $group_id = $_GET['bp_gid'];
    $users = $_GET['users'];
    foreach ($users as $user_id) {
        groups_join_group( $group_id, $user_id );
    }
}
    //Add some Javascript to handle the form submission
    add_action('admin_footer',function(){ ?>
    <script>
        jQuery("select[name='action']").append(jQuery('<option value="groupadd">Add to BP Group</option>'));
        jQuery("#doaction").click(function(e){
            if(jQuery("select[name='action'] :selected").val()=="groupadd") { e.preventDefault();
                gid=prompt("Please enter a BuddyPres Group ID","1");
                jQuery(".wrap form").append('<input type="hidden" name="bp_gid" value="'+gid+'" />').submit();
            }
        });
    </script>
    <?php
    });
});