<div class="iump-form-line-register iump-form-select">
    <label class="iump-labels-register"><?php _e('WP Role', 'ihc');?></label>
    <?php
          echo indeed_create_form_element(
                        array(
                            'type' => 'select',
                            'name' => 'role',
                            'value' => $role,
                            'multiple_values' => ihc_get_wp_roles_list(),
                            'class' => ''
                        )
          );
    ?>
</div>
