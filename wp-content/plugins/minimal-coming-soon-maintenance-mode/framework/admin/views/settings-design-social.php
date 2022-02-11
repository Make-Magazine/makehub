<div class="csmm-tile" id="design-social">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Social Icons Module</div>
        <p>A standard element for any site, no matter the size or purpose.</p>

        <div class="csmm-section-content">

            <div class="csmm-double-group  clearfix">
                <div class="csmm-form-group">
                    <label for="icon_size" class="csmm-strong">Icon Size</label>
                    <select id="icon_size" name="icon_size" class="">
                        <?php
                        $icon_sizes = array(
                            array('val' => 'small', 'label' => 'Small'),
                            array('val' => 'medium', 'label' => 'Medium'),
                            array('val' => 'large', 'label' => 'Large'),
                            array('val' => 'extra-large', 'label' => 'Extra Large'),
                        );
                        csmm_create_select_options($icon_sizes, $signals_csmm_options['icon_size']);
                        ?>
                    </select>
                </div>

                <div class="csmm-form-group">
                    <label for="social_icons_color" class="csmm-strong">Icon Color</label>
                    <input type="text" name="social_icons_color" id="social_icons_color" value="<?php echo csmm_hex2rgba($signals_csmm_options['social_icons_color']); ?>" class="csmm-color csmm-form-control color {required:false}">
                </div>
            </div>


            <div class="csmm-group">
                <div class="csmm-form-group">
                    <label for="signals_csmm_message_noemail" class="csmm-strong">Icons</label>
                    <table class="table social_sytems_table">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th style="min-width: 180px;">URL</th>
                                <th style="min-width: 100px;">Icon</th>
                                <th><button type="button" id="add_new_row" class="csmm-btn">Add new icon</button></th>
                            </tr>
                        </thead>
                        <tbody class="sort_rows">
                            <?php

                            if (!isset($signals_csmm_options['social_list_url'])) {
                                $signals_csmm_options['social_list_url'] = array();
                            }


                            if (count($signals_csmm_options['social_list_url']) >= 1) {

                                for ($i = 0; $i < count($signals_csmm_options['social_list_url']); $i++) {

                            ?>

                                    <tr>
                                        <td>
                                            <div class="move_block"><i data-icomoon=""></i></div>
                                        </td>
                                        <td>
                                            <input placeholder="https://" type="text" class="csmm-form-control" name="social_list_url[]" value="<?php echo sanitize_text_field($signals_csmm_options['social_list_url'][$i]); ?>" />
                                        </td>
                                        <?php /*
                    <td>
                    <input type="text" class="csmm-form-control" name="social_list_text[]" value="<?php echo sanitize_text_field($signals_csmm_options['social_list_text'][$i]); ?>" /> </td>
                    */ ?>
                                        <td>
                                            <input type="text" class="csmm-form-control icon_picker_select" name="social_list_icon[]" value="<?php echo sanitize_text_field($signals_csmm_options['social_list_icon'][$i]); ?>" />
                                        </td>
                                        <td><button type="button" class="remove_row_button csmm-btn csmm-btn-red"><strong>Delete</strong></button></td>
                                    </tr>

                                <?php

                                }
                            } else {
                                ?>
                                <tr>
                                    <td>
                                        <div class="move_block"><i data-icomoon=""></i></div>
                                    </td>
                                    <td><input type="text" class="csmm-form-control" name="social_list_url[]" /> </td>
                                    <td><input type="text" class="csmm-form-control icon_picker_select" name="social_list_icon[]" />
                                    </td>
                                    <td><button type="button" class="remove_row_button csmm-btn csmm-btn-red"><strong>Delete</strong></button></td>
                                </tr>
                            <?php
                            }
                            ?>

                        </tbody>
                    </table><br>
                    <p class="csmm-form-help-block">Make sure you write the full link, including the <i>http</i> or <i>https</i> prefix.<br>
                        Icons can be searched using keywords or filtered by category.</p>
                </div>

            </div>
        </div>
    </div>
</div><!-- #social -->