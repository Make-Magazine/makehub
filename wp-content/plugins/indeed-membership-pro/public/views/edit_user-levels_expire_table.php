<table class="wp-list-table widefat fixed tags ihc-manage-user-expire">
    <thead>
        <tr>
            <th><?php _e('Level Name', 'ihc');?></th>
            <th><?php _e('Acess Type', 'ihc');?></th>
            <th><?php _e('Start Time', 'ihc');?></th>
            <th><?php _e('Expire Time', 'ihc');?></th>
        </tr>
    </thead>
    <tbody>
        <?php
            $i = 1;
            foreach ($user_levels as $v){
              $v = (int)$v;
              $temp_data = ihc_get_level_by_id($v);
              if ($temp_data){
                $time = ihc_get_start_expire_date_for_user_level($uid, $v);
                $placeholder['start_time'] = '';
                $placeholder['expire_time'] = '';
                if (!$time['start_time']){
                  $placeholder['start_time'] = '----/--/----';
                }
                if (!$time['expire_time']){
                  $placeholder['expire_time'] = '----/--/----';
                }

                if (!isset($temp_data['access_type'])){
                  $temp_data['access_type'] = 'LifeTime';
                }
                echo '<tr class="'. ($i%2==0 ? 'alternate':'') .'" id="tr_level_user_' . $v . '_' . $uid . '">'
                          . '<td style="color: #21759b; font-weight:bold; width:120px;font-family: "Oswald", arial, sans-serif !important;font-size: 14px;font-weight: 300;">' . $temp_data['name'] . '</td>'
                          . '<td style="color: #888; font-weight:bold; width:120px;font-family: "Oswald", arial, sans-serif !important;font-size: 14px;font-weight: 300;">' . $temp_data['access_type'] . '</td>'
                          . '<td>' . indeed_create_form_element( array('type'=>'text',
                                            'name'=>'start_time_levels['.$v.']',
                                            'class'=>'start_input_text',
                                            'value' => $time['start_time'],
                                            'placeholder' => $placeholder['start_time']
                                            )
                            )
                          . '</td>'
                          . '<td>' . indeed_create_form_element( array('type'=>'text',
                                            'name'=>'expire_levels['.$v.']',
                                            'class'=>'expire_input_text',
                                            'value' => $time['expire_time'],
                                            'placeholder' => $placeholder['expire_time']
                                            )
                            )
                          . '</td>'
                        . '</tr>';
              }
            $i++;
            }
        ?>
    </tbody>
</table>
