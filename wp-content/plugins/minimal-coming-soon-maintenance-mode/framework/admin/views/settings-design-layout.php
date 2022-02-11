<div class="csmm-tile" id="design-layout">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Coming Soon Type</div>
        <p>Select the coming soon mode</p>
        <br /><br />

        <div class="csmm-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="csmm_mode" class="csmm-strong">Layout Builder</label>
                        <?php
            if ($signals_csmm_options['disable_settings'] == '1') {
                $signals_csmm_options['mode'] = 'html';
            }
            ?>
            <select name="signals_csmm_mode" id="csmm_mode">
                <option value="layout" <?php selected('layout', $signals_csmm_options['mode']); ?>>Coming Soon Layout Builder (recommended)</option>
                <option value="page" <?php selected('page', $signals_csmm_options['mode']); ?>>Pick an Existing Page (any 3rd party builder)</option>
                <option value="html" <?php selected('html', $signals_csmm_options['mode']); ?>>Custom HTML Only</option>
            </select>
                        
                        <p class="csmm-form-help-block">For the majority of users, and especially those who want to use any Coming Soon themes, our layout builder is the best choice. However if you want to use any other builders such as Gutenberg, Elementor, or Beaver Builder to name a few - pick an existing page and use it as the coming soon one.<br>
                      If you want to take it a step further, pick the 3rd option and upload the complete page's HTML code.</p>
                    </div>
                </div>

        <div id="csmm_mode_layout" class="csmm-mode-options" <?php echo $signals_csmm_options['mode'] != 'layout' ? 'style="display:none;"' : ''; ?>>
            <div class="csmm-tile-title">Layout Builder</div>
            <p>To change the order of modules, simply drag them and arrange. To remove them, drag to the right, inactive modules section. To make them visible, bring them back to the left, active section.</p>

            <div class="csmm-section-content">
                <div class="csmm-group csmm-arrange-group">

                    <?php
                    $modules = array();
                    $modules['logo'] = array('name' => 'Logo', 'link' => 'design-logo');
                    $modules['header'] = array('name' => 'Header', 'link' => 'design-header');
                    $modules['content'] = array('name' => 'Content', 'link' => 'design-content');
                    $modules['content2col'] = array('name' => 'Content 2 Columns', 'link' => 'design-content2col');
                    $modules['divider'] = array('name' => 'Divider', 'link' => 'design-divider');
                    $modules['form'] = array('name' => 'Subscribe Form', 'link' => 'design-form');
                    $modules['contactform'] = array('name' => 'Contact Form', 'link' => 'design-contact');
                    $modules['video'] = array('name' => 'Video', 'link' => 'design-video');
                    $modules['countdown'] = array('name' => 'Countdown', 'link' => 'design-countdown');
                    $modules['progressbar'] = array('name' => 'Progress Bar', 'link' => 'design-progress');
                    $modules['social'] = array('name' => 'Social Icons', 'link' => 'design-social');
                    $modules['map'] = array('name' => 'Map', 'link' => 'design-map');
                    $modules['html'] = array('name' => 'Custom HTML', 'link' => 'design-html');
                    if(csmm_is_mailpoet_active()){
                        $modules['mailpoet'] = array('name' => 'MailPoet', 'link' => admin_url('admin.php?page=mailpoet-forms#/'));
                    }
                    $modules = apply_filters('csmm_modules_list', $modules);


                    $active_modules = false;
                    if (!empty($signals_csmm_options['arrange'])) {
                        $active_modules = explode(',', $signals_csmm_options['arrange']);
                    }
                    if (!is_array($active_modules)) {
                        $active_modules = array('logo', 'header', 'content', 'form', 'video', 'social');
                    }
                    $available_modules = array_diff(array_keys($modules), $active_modules);

                    echo '<div class="arrange-wrapper" id="active-modules"><span class="arrange-label">Page Layout</span>';
                    echo '<div class="browser-header"><div class="browser-button"></div><div class="browser-button"></div><div class="browser-button"></div><div class="browser-input"><span class="dashicons dashicons-update"></span></div></div>';
                    echo '<ul id="arrange-items" class="csmm-layout-builder">';
                    // active elements
                    foreach ($active_modules as $module) {
                        if($module == 'mailpoet' && !csmm_is_mailpoet_active()){
                            continue;
                        }
                        echo '<li data-id="' . $module . '"><img src="' . CSMM_URL . '/framework/admin/img/sections/' . $module . '.png" title="Drag to rearrange the module on coming soon page, or move it to inactive modules">';
                        echo '<div class="actions-center"><span class="module-name">' . $modules[$module]['name'] . '</span>';
                        echo '<a title="Drag to rearrange the module on coming soon page, or move it to inactive modules" href="javascript: void(0);" class="js-action move-module"><span class="dashicons dashicons-move"></span></a>';
                        if(strpos($modules[$module]['link'], 'design') !== 0) {
                            echo '<a title="Edit module" href="' . $modules[$module]['link'] . '" title="Edit module"><span class="dashicons dashicons-edit"></span></a>';
                        } else {
                            echo '<a title="Edit module" href="#' . $modules[$module]['link'] . '" class="js-action csmm-change-tab" title="Edit module"><span class="dashicons dashicons-edit"></span></a>';
                        }
                        echo '<a href="#" class="js-action remove-module" title="Remove module from coming soon page"><span class="dashicons dashicons-trash"></span></a>';
                        echo '<a href="#" class="js-action add-module" title="Add module to coming soon page"><span class="dashicons dashicons-plus"></span></a>';
                        echo '</div></li>';
                    }
                    echo '</ul></div>';

                    echo '<div class="arrange-wrapper" id="hidden-modules"><span class="arrange-label">Available / Hidden Modules</span>';
                    echo '<ul id="arrange-items2" class="csmm-layout-builder">';
                    // available elements
                    foreach ($available_modules as $module) {
                        if($module == 'mailpoet' && !csmm_is_mailpoet_active()){
                            continue;
                        }
                        echo '<li data-id="' . $module . '"><img src="' . CSMM_URL . '/framework/admin/img/sections/' . $module . '.png" title="Drag to rearrange the module on coming soon page, or move it to inactive modules">';
                        echo '<div class="actions-center"><span class="module-name">' . $modules[$module]['name'] . '</span>';
                        echo '<a title="Drag to rearrange the module on coming soon page, or move it to inactive modules" href="#" class="js-action move-module"><span class="dashicons dashicons-move"></span></a>';
                        if(strpos($modules[$module]['link'], 'design') !== 0) {
                            echo '<a title="Edit module" href="' . $modules[$module]['link'] . '" title="Edit module"><span class="dashicons dashicons-edit"></span></a>';
                        } else {
                            echo '<a title="Edit module" href="#' . $modules[$module]['link'] . '" class="js-action csmm-change-tab" title="Edit module"><span class="dashicons dashicons-edit"></span></a>';
                        }
                        echo '<a href="#" class="js-action remove-module" title="Remove module from coming soon page"><span class="dashicons dashicons-trash"></span></a>';
                        echo '<a href="#" class="js-action add-module" title="Add module to coming soon page"><span class="dashicons dashicons-plus"></span></a>';
                        echo '</div></li>';
                    }
                    echo '</ul></div>';
                    ?>

                    <input type="hidden" name="signals_csmm_arrange" id="signals_csmm_arrange" value="<?php echo esc_attr_e($signals_csmm_options['arrange']); ?>">
                </div>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="signals_csmm_overlay" class="csmm-strong">Content Overlay</label>
                        <input type="checkbox" class="csmm-form-ios" id="signals_csmm_overlay" name="signals_csmm_overlay" value="1" <?php checked('1', $signals_csmm_options['content_overlay']); ?>>
                        <p class="csmm-form-help-block">If enabled, applies a transparent background to the entire content section of the page.</p>
                    </div>

                    <div class="csmm-form-group overlay_parameters">
                        <label for="signals_csmm_overlay_mobile" class="csmm-strong">Show only on small screens</label>
                        <input type="checkbox" class="csmm-form-ios" id="signals_csmm_overlay_mobile" name="signals_csmm_overlay_mobile" value="1" <?php checked('1', $signals_csmm_options['content_overlay_mobile']); ?>>
                        <p class="csmm-form-help-block">If enabled, only displays the overlay on small screens, up to 992px wide.</p>
                    </div>
                </div>
                
                <div class="csmm-double-group csmm-clearfix overlay_parameters">
                    <div class="csmm-form-group">
                        <label for="overlay_color" class="csmm-strong">Content Overlay Color</label>
                        <input type="text" name="overlay_color" id="overlay_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['overlay_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                        <p class="csmm-form-help-block">Background color for the content overlay.</p>
                    </div>

                    <div class="csmm-form-group">
                        <label for="signals_csmm_width" class="csmm-strong">Content Overlay Opacity</label>
                        <input type="hidden" name="transparency_level" value="<?php esc_attr_e($signals_csmm_options['transparency_level']); ?>" data-min="0" data-max="100" data-step="1" data-label="%val%%" class="csmm-slide-input">
                        <p class="csmm-form-help-block">Less opacity means a more transparent background. Default: 60%.</p>
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="signals_csmm_width" class="csmm-strong">Content Width</label>
                        <input type="hidden" name="signals_csmm_width" id="signals_csmm_width" value="<?php esc_attr_e($signals_csmm_options['content_width']); ?>" data-min="200" data-max="1040" data-step="10" data-label="%val%px" class="csmm-slide-input">
                        <p class="csmm-form-help-block">Maximum content width. Default: 600px.</p>
                    </div>

                    <div class="csmm-form-group">
                        <label for="signals_csmm_position" class="csmm-strong">Content Position</label>
                        <select name="signals_csmm_position" id="signals_csmm_position">
                            <option value="left" <?php selected('left', $signals_csmm_options['content_position']); ?>>Top Left</option>
                            <option value="center" <?php selected('center', $signals_csmm_options['content_position']); ?>>Top Center</option>
                            <option value="right" <?php selected('right', $signals_csmm_options['content_position']); ?>>Top Right</option>
                            <option value="middle" <?php selected('middle', $signals_csmm_options['content_position']); ?>>Middle Center</option>
                            <option value="bottom-center" <?php selected('bottom-center', $signals_csmm_options['content_position']); ?>>Bottom Center</option>
                        </select>
                        <p class="csmm-form-help-block">Content box position on the page.</p>
                    </div>
                </div>
                <?php
                $animations = array(
                    array('val' => '', 'label' => 'Disabled'),
                    array('val' => '-1', 'disabled' => true, 'label' => 'Attention Seekers'),
                    array('val' => 'bounce', 'label' => 'bounce'),
                    array('val' => 'flash', 'label' => 'flash'),
                    array('val' => 'pulse', 'label' => 'pulse'),
                    array('val' => 'rubberBand', 'label' => 'rubberBand'),
                    array('val' => 'shake', 'label' => 'shake'),
                    array('val' => 'swing', 'label' => 'swing'),
                    array('val' => 'tada', 'label' => 'tada'),
                    array('val' => 'wobble', 'label' => 'wobble'),
                    array('val' => 'jello', 'label' => 'jello'),
                    array('val' => '-1', 'disabled' => true, 'label' => 'Bouncing Entrances'),
                    array('val' => 'bounceIn', 'label' => 'bounceIn'),
                    array('val' => 'bounceInDown', 'label' => 'bounceInDown'),
                    array('val' => 'bounceInLeft', 'label' => 'bounceInLeft'),
                    array('val' => 'bounceInRight', 'label' => 'bounceInRight'),
                    array('val' => 'bounceInUp', 'label' => 'bounceInUp'),
                    array('val' => '-1', 'disabled' => true, 'label' => 'Fading Entrances'),
                    array('val' => 'fadeIn', 'label' => 'fadeIn'),
                    array('val' => 'fadeInDown', 'label' => 'fadeInDown'),
                    array('val' => 'fadeInDownBig', 'label' => 'fadeInDownBig'),
                    array('val' => 'fadeInLeft', 'label' => 'fadeInLeft'),
                    array('val' => 'fadeInLeftBig', 'label' => 'fadeInLeftBig'),
                    array('val' => 'fadeInRight', 'label' => 'fadeInRight'),
                    array('val' => 'fadeInRightBig', 'label' => 'fadeInRightBig'),
                    array('val' => 'fadeInUp', 'label' => 'fadeInUp'),
                    array('val' => 'fadeInUpBig', 'label' => 'fadeInUpBig'),
                    array('val' => '-1', 'disabled' => true, 'label' => 'Flippers'),
                    array('val' => 'flip', 'label' => 'flip'),
                    array('val' => 'flipInX', 'label' => 'flipInX'),
                    array('val' => 'flipInY', 'label' => 'flipInY'),
                    array('val' => '-1', 'disabled' => true, 'label' => 'Rotating Entrances'),
                    array('val' => 'rotateIn', 'label' => 'rotateIn'),
                    array('val' => 'rotateInDownLeft', 'label' => 'rotateInDownLeft'),
                    array('val' => 'rotateInDownRight', 'label' => 'rotateInDownRight'),
                    array('val' => 'rotateInUpLeft', 'label' => 'rotateInUpLeft'),
                    array('val' => 'rotateInUpRight', 'label' => 'rotateInUpRight'),
                    array('val' => '-1', 'disabled' => true, 'label' => 'Sliding Entrances'),
                    array('val' => 'slideInUp', 'label' => 'slideInUp'),
                    array('val' => 'slideInDown', 'label' => 'slideInDown'),
                    array('val' => 'slideInLeft', 'label' => 'slideInLeft'),
                    array('val' => 'slideInRight', 'label' => 'slideInRight'),
                    array('val' => '-1', 'disabled' => true, 'label' => 'Zoom Entrances'),
                    array('val' => 'zoomIn', 'label' => 'zoomIn'),
                    array('val' => 'zoomInDown', 'label' => 'zoomInDown'),
                    array('val' => 'zoomInLeft', 'label' => 'zoomInLeft'),
                    array('val' => 'zoomInRight', 'label' => 'zoomInRight'),
                    array('val' => 'zoomInUp', 'label' => 'zoomInUp'),
                    array('val' => '-1', 'disabled' => true, 'label' => 'Specials'),
                    array('val' => 'lightSpeedIn', 'label' => 'lightSpeedIn'),
                    array('val' => 'hinge', 'label' => 'hinge'),
                    array('val' => 'jackInTheBox', 'label' => 'jackInTheBox'),
                    array('val' => 'rollIn', 'label' => 'rollIn')
                );
                ?>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="animation" class="csmm-strong">Content Intro Animation</label>
                        <select name="animation" id="animation">
                            <?php echo csmm_create_select_options($animations, $signals_csmm_options['animation']); ?>
                        </select>
                        <p class="csmm-form-help-block">When the page loads, the content will be animated on to the page with the selected animation. Use the <a href="https://comingsoonwp.com/content-animations/" target="_blank">animation previews</a> for easier picking.</p>
                    </div>
                </div>

                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="module_margin" class="csmm-strong">Modules Spacing</label>
                        <input type="hidden" name="module_margin" id="module_margin" value="<?php esc_attr_e($signals_csmm_options['module_margin']); ?>" data-min="0" data-max="50" data-step="1" data-label="%val%px" class="csmm-slide-input">
                        <p class="csmm-form-help-block">Vertical spacing between design modules. The selected value is added to both top and bottom margins of the module. Default: 10px.</p>
                    </div>
                </div>


                <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_secondary_size" class="csmm-strong">Content Text Size</label>
                    <input type="hidden" name="signals_csmm_secondary_size" value="<?php esc_attr_e($signals_csmm_options['secondary_font_size']); ?>" data-min="6" data-max="200" data-step="1" data-label="%val%px" class="csmm-slide-input">
                    <p class="csmm-form-help-block">Don't be afraid to make to font a bit bigger. Default: 16px;</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_secondary_color" class="csmm-strong">Content Text Color</label>
                    <input type="text" name="signals_csmm_secondary_color" id="signals_csmm_secondary_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['secondary_font_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    <p class="csmm-form-help-block">Make sure the contrast is good. Whiteish text on a dark background is always a good pick.</p>
                </div>
            </div>

            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_secondary_font" class="csmm-strong">Content Font</label>
                    <select name="signals_csmm_secondary_font" id="signals_csmm_secondary_font" class="csmm-google-fonts">
                        <option value="Arial" <?php selected('Arial', $signals_csmm_options['secondary_font']); ?>>Arial</option>
                        <option value="Helvetica" <?php selected('Helvetica', $signals_csmm_options['secondary_font']); ?>>Helvetica</option>
                        <option value="Georgia" <?php selected('Georgia', $signals_csmm_options['secondary_font']); ?>>Georgia</option>
                        <option value="Times New Roman" <?php selected('Times New Roman', $signals_csmm_options['secondary_font']); ?>>Times New Roman</option>
                        <option value="Tahoma" <?php selected('Tahoma', $signals_csmm_options['secondary_font']); ?>>Tahoma</option>
                        <option value="Verdana" <?php selected('Verdana', $signals_csmm_options['secondary_font']); ?>>Verdana</option>
                        <option value="Geneva" <?php selected('Geneva', $signals_csmm_options['secondary_font']); ?>>Geneva</option>
                        <option disabled>-- via google --</option>
                        <?php

                        // Listing fonts from the array
                        foreach ($signals_google_fonts as $signals_font) {
                            echo '<option value="' . $signals_font . '"' . selected($signals_font, $signals_csmm_options['secondary_font']) . '>' . $signals_font . '</option>' . "\n";
                        }

                        ?>
                    </select>
                    <h3><?php _e('This is how the content font is going to look!', 'signals'); ?></h3>
                    <p class="csmm-form-help-block">Choose from over 700 Google Fonts. Make sure other modules use the same, or matching fonts.</p>
                </div>
            </div>


            <div class="csmm-double-group csmm-clearfix">
                <div class="csmm-form-group">
                    <label for="signals_csmm_link_color" class="csmm-strong">Link Color</label>
                    <input type="text" name="signals_csmm_link_color" id="signals_csmm_link_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['link_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    <p class="csmm-form-help-block">Make sure it stands out but make it fit your color scheme.</p>
                </div>

                <div class="csmm-form-group">
                    <label for="signals_csmm_link_hover_color" class="csmm-strong">Link Hover Color</label>
                    <input type="text" name="signals_csmm_link_hover_color" id="signals_csmm_link_hover_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['link_hover_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                    <p class="csmm-form-help-block">Generally it's a lighter or darker variation of the normal link color.</p>
                </div>
            </div>
        

            </div>

        </div><!-- layout mode -->

        <div id="csmm_mode_page" class="csmm-mode-options" <?php echo $signals_csmm_options['mode'] != 'page' ? 'style="display:none;"' : ''; ?>>
            <div class="csmm-section-content">
                <div class="csmm-double-group csmm-clearfix">
                    <div class="csmm-form-group">
                        <label for="" class="csmm-strong">Select page:</label>
                        <?php if(empty(wp_dropdown_pages(array('id' => 'signals_csmm_page', 'name' => 'signals_csmm_page', 'selected' => $signals_csmm_options['csmm_page'])))){
                            echo 'You do not have any <a href="'.admin_url('edit.php?post_type=page').'">pages</a>';
                        } ?>
                    </div>
                </div>
            </div>
        </div><!-- page mode -->

        <div id="csmm_mode_html" class="csmm-mode-options" <?php echo $signals_csmm_options['mode'] != 'html' ? 'style="display:none;"' : ''; ?>>
            <p class="csmm-form-help-block">If you enable this option, the plugin <b>will ignore </b>all other modules, background or content settings and display only the HTML you provide.<br>Basically, you'll get a blank template to work with. Basic CSS reset rules will be automatically added.</p>

            <div class="csmm-section-content">

                <div class="csmm-form-group">
                    <label for="signals_custom_html_layout" class="csmm-strong">Custom HTML</label>
                    <div id="signals_custom_html_layout_editor"></div>
                    <textarea name="signals_custom_html_layout" id="signals_custom_html_layout" rows="8" placeholder="Custom HTML for the plugin"><?php echo stripslashes($signals_csmm_options['custom_html_layout']); ?></textarea>

                    <p class="csmm-form-help-block">The module is wrapped in CSS classes like all other modules when displayed: <i>.html-container</i> and <i>.mm-module</i>.</p>
                </div>

            </div>
        </div><!-- html mode -->

    </div>
</div><!-- #layout -->