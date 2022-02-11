<?php
global $csmm_lc;
$meta = csmm_get_meta();
$theme_folder = CSMM_PATH . 'framework/admin/themes/';
$themes = array();
clearstatcache();

foreach (glob($theme_folder . '*.txt') as $filename) {
    $tmp = @json_decode(@file_get_contents($filename), true);
    
    if (!is_array($tmp['meta']) || !is_array($tmp['data']) || sizeof($tmp['data']) < 30) {
        continue;
    }
    if (defined('CSMMT')) {
    } elseif (empty($tmp['meta']['name']) 
              || $tmp['meta']['status'] == 'draft' 
              || ($tmp['meta']['status'] == 'agency' && !$csmm_lc->is_active('agency_themes')) 
              || ($tmp['meta']['status'] == 'extra' && !$csmm_lc->is_active('extra_themes'))
             ) {
        continue;
    }

    $tmp['last_edit'] = strtotime($tmp['meta']['last_edit']);
    $themes[] = $tmp;
}

usort($themes, function ($a, $b) {
    return $b['last_edit'] - $a['last_edit'];
});

?>

<div class="csmm-tile" id="themes">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Themes</div>
        <p>Once a theme is activated, it can be fully adjusted and modified. There are no "locked-in" features. Please note that activating a theme overwrites all customizations done to the current design. Non-design settings such as access control are not affected.
            <?php
            if (!$csmm_lc->is_active('agency_themes')) {
                echo '<p><br>Need more themes? Extra themes pack includes <a href="' . csmm_generate_web_link('get-extra-themes-top', 'themes') . '" target="_blank">50+ themes</a>. Contact <a href="#support" class="csmm-change-tab">support</a> to purchase extra themes for only $9.</p>';
            }
            ?></p>

        <p>Filter themes: <input type="search" name="csmm-search-templates" id="csmm-search-templates" placeholder="Enter keyword" value="" class="skip-save"></p>

        <div id="csmm-themes-wrapper" class="csmm-section-content">
            <?php
            foreach ($themes as $theme) {
                echo '<div class="theme-thumb" data-theme-date="' . $theme['meta']['last_edit'] . '" data-theme="' . $theme['meta']['last_edit'] . '" data-theme="' . $theme['meta']['name_clean'] . '" data-theme-name="' . $theme['meta']['name'] . '">';
                echo '<img src="' . CSMM_ASSETS . 'themes/' . $theme['meta']['name_clean'] . '/thumb.jpg" alt="' . $theme['meta']['name'] . '" title="' . $theme['meta']['name'] . '">';
                echo '<span class="name">' . $theme['meta']['name'] . '</span>';
                echo apply_filters('csmm_theme_thumbnail_details', '', $theme);
                echo '<span name="actions"><a href="' . add_query_arg(array('action' => 'csmm_activate_theme', 'theme' => $theme['meta']['name_clean'], 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php')) . '" class="csmm-btn confirm-action" data-confirm="Are you sure you want to activate the selected theme? Customizations you made on the current design will be lost.">Activate</a>&nbsp; &nbsp;<a target="_blank" class="csmm-btn csmm-btn-secondary" href="https://comingsoonwp.com/theme-preview/?theme=' . $theme['meta']['name_clean'] . '">Preview</a></span>';
                echo '</div>';
            } // foreach theme

            if (!$csmm_lc->is_active('agency_themes')) {
                echo '<p>Need more themes? Extra themes pack includes <a href="' . csmm_generate_web_link('get-extra-themes-bottom', 'themes') . '" target="_blank">50+ themes</a>. Contact <a href="#support" class="csmm-change-tab">support</a> to purchase extra themes for only $9.</p>';
            }
            ?>
        </div>
    </div>
</div><!-- #themes -->