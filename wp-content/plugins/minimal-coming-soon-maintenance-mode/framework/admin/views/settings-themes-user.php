<?php
global $csmm_lc;

// Create Coming Soon themes folder if it does not exist
$uploads = wp_upload_dir();
$csmm_themes_url = $uploads['baseurl'] . '/coming-soon-themes/';
$csmm_themes_folder = $uploads['basedir'] . '/coming-soon-themes/';

$meta = csmm_get_meta();
$themes = array();
clearstatcache();

if(file_exists($csmm_themes_folder)){
    $user_themes = array_diff(scandir($csmm_themes_folder), array('..', '.'));
    foreach($user_themes as $theme_folder){
        $theme_file = glob($csmm_themes_folder . trailingslashit($theme_folder) . '*.txt');
        $tmp = @json_decode(@file_get_contents($theme_file[0]), true);

        if (!is_array($tmp['meta']) || !is_array($tmp['data']) || sizeof($tmp['data']) < 30) {
            continue;
        }
        $tmp['theme_id'] = $theme_folder;
        $tmp['last_edit'] = strtotime($tmp['meta']['last_edit']);
        $themes[] = $tmp;
    }

    usort($themes, function ($a, $b) {
        return $b['last_edit'] - $a['last_edit'];
    });
}

?>

<div class="csmm-tile" id="themes-user">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">User Themes</div>
        <p>These are the themes that you created and saved. Please note that activating a theme overwrites all customizations done to the current design. Non-design settings such as access control are not affected.
            <?php
            if (!$csmm_lc->is_active('agency_themes')) {
                echo '<p><br>Need more themes? Extra themes pack includes <a href="' . csmm_generate_web_link('get-extra-themes-top', 'themes') . '" target="_blank">50+ themes</a>. Contact <a href="#support" class="csmm-change-tab">support</a> to purchase extra themes for only $9.</p>';
            }
            ?></p>

        <div id="csmm-themes-wrapper" class="csmm-section-content">
            <?php
            if(count($themes) == 0){
                echo '<p style="text-align:center; padding-top:100px">You do not have any user themes.<br>Use the "Save Theme" button on the bottom of the screen to create a new user theme.</p>';
            }


            foreach ($themes as $theme) {
                echo '<div class="theme-thumb" data-theme-date="' . $theme['meta']['last_edit'] . '" data-theme="' . $theme['meta']['last_edit'] . '" data-theme="' . $theme['meta']['name_clean'] . '" data-theme-name="' . $theme['meta']['name'] . '">';
                if(strlen($theme['data']['bg_cover']) > 4){
                    echo '<img src="' . $theme['data']['bg_cover'] . '" alt="' . $theme['meta']['name'] . '" title="' . $theme['meta']['name'] . '">';
                } else {
                    echo '<span class="dashicons dashicons-format-image"></span>';
                }
                echo '<span class="name">' . $theme['meta']['name'] . '</span>';
                echo apply_filters('csmm_theme_thumbnail_details', '', $theme);
                echo '<span name="actions">';
                echo '<a href="' . add_query_arg(array('action' => 'csmm_activate_theme', 'theme' => $theme['theme_id'], 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php')) . '" class="csmm-btn confirm-action" data-confirm="Are you sure you want to activate the selected theme? Customizations you made on the current design will be lost.">Activate</a>&nbsp;';
                echo '<a href="' . home_url('/') . '?csmm_preview_theme=' . $theme['theme_id'] . '" class="csmm-btn" target="_blank">Preview</a>&nbsp;';
                echo '<a href="' . add_query_arg(array('action' => 'csmm_delete_theme', 'theme' => $theme['theme_id'], 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php')) . '" class="csmm-btn csmm-btn-red confirm-action" data-confirm="Are you sure you want to delete the selected theme?">Delete</a></span>';
                echo '</span></div>';
            } // foreach theme


            
            ?>
        </div>
    </div>
</div><!-- #themes -->