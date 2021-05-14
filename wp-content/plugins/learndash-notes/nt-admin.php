<?php
/*
 * Add tab to the learndash settings
 *
 */
add_filter("learndash_admin_tabs", "lds_notes_tabs");
function lds_notes_tabs($admin_tabs) {

	$admin_tabs["notes"] = array(
		"link"  		=>      'options-general.php?page=learndash-notes-license',
		"name" 			=>      __( "Notes", "sfwd-lms" ),
		"id"    		=>      "admin_page_learndash-notes",
		"menu_link"     =>      "edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses",
	);

   	return $admin_tabs;

}

// add_filter("learndash_admin_tabs_on_page", "learndash_notes_admin_tabs_on_page", 3, 3);
function learndash_notes_admin_tabs_on_page($admin_tabs_on_page, $admin_tabs, $current_page_id) {

	$admin_tabs_on_page["admin_page_learndash-notes"] = array_merge($admin_tabs_on_page["sfwd-courses_page_sfwd-lms_sfwd_lms_post_type_sfwd-courses"], (array) $admin_tabs_on_page["admin_page_learndash-notes"]);

	foreach ($admin_tabs as $key => $value) {
		if($value["id"] == $current_page_id && $value["menu_link"] == "edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses")
		{
			$admin_tabs_on_page[$current_page_id][] = "notes";
			return $admin_tabs_on_page;
		}
	}

	return $admin_tabs_on_page;
}
