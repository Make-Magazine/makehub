
                                  <div class="iump-form-line-register iump-form-file">
                                  <label class="iump-labels-register"><?php echo $field['label'];?></label>
                                    <?php
                            				wp_enqueue_script( 'ihc-jquery_form_module' );
                            				wp_enqueue_script( 'ihc-jquery_upload_file' );
                            				$upload_settings = ihc_return_meta_arr('extra_settings');
                            				$max_size = $upload_settings['ihc_upload_max_size'] * 1000000;
                            				$rand = rand(1,10000);
                                    $attachment_name = '';
                                    $url = '';
                                    ?>
                            				<div id="<?php echo 'ihc_fileuploader_wrapp_' . $rand;?>" class="ihc-wrapp-file-upload ihc-wrapp-file-field" style=" vertical-align: text-top;">
                            				<div class="ihc-file-upload ihc-file-upload-button"><?php _e("Upload", 'ihc');?></div>
                                    </div>
                                    <script>
                                      jQuery(document).ready(function() {
                                        jQuery("#ihc_fileuploader_wrapp_<?php echo $rand;?> .ihc-file-upload").uploadFile({
                                          onSelect: function (files) {
                                              jQuery("#ihc_fileuploader_wrapp_<?php echo $rand;?> .ajax-file-upload-container").css("display", "block");
                                              var check_value = jQuery("#ihc_upload_hidden_<?php echo $rand;?>").val();
                                              if (check_value!="" ){
                                                alert("To add a new image please remove the previous one!");
                                                return false;
                                              }
                                              return true;
                                          },
                                          url: "<?php echo IHC_URL . 'public/ajax-upload.php';?>",
                                          fileName: "ihc_file",
                                          dragDrop: false,
                                          showFileCounter: false,
                                          showProgress: true,
                                          showFileSize: false,
                                          maxFileSize: <?php echo $max_size;?>,
                                          allowedTypes: "<?php echo $upload_settings['ihc_upload_extensions'];?>",
                                          onSuccess: function(a, response, b, c){
                                            if (response){
                                              var obj = jQuery.parseJSON(response);
                                              if (typeof obj.secret!="undefined"){
                                                  jQuery("#ihc_fileuploader_wrapp_<?php echo $rand;?>").attr("data-h", obj.secret);
                                              }
                                              jQuery("#ihc_fileuploader_wrapp_<?php echo $rand;?> .ihc-file-upload").prepend("<div onClick=\"ihcDeleteFileViaAjax("+obj.id+", -1, '#ihc_fileuploader_wrapp_<?php echo $rand;?>', '<?php echo  $fieldName;?>', '#ihc_upload_hidden_<?php echo $rand;?>');\" class=\'ihc-delete-attachment-bttn\'>Remove</div>");
                                              switch (obj.type){
                                                case "image":
                                                  jQuery("#ihc_fileuploader_wrapp_<?php echo $rand;?> .ihc-file-upload").prepend("<img src="+obj.url+" class=\'ihc-member-photo\' /><div class=\'ihc-clear\'></div>");
                                                break;
                                                case "other":
                                                  jQuery("#ihc_fileuploader_wrapp_<?php echo $rand;?> .ihc-file-upload").prepend("<div class=ihc-icon-file-type></div><div class=ihc-file-name-uploaded>"+obj.name+"</div>");
                                                break;
                                              }
                                              jQuery("#ihc_upload_hidden_<?php echo $rand;?>").val(obj.id);
                                              setTimeout(function(){
                                                jQuery("#ihc_fileuploader_wrapp_<?php echo $rand;?> .ajax-file-upload-container").css("display", "none");
                                              }, 3000);
                                            }
                                          }
                                        });
                                      });
                                    </script>
                                <?php
                                if ( $fieldValue ){
                                  if ( strpos ( $fieldValue, 'http' ) !== false ){
                                    $fileExtension = explode( '.', $fieldValue );
                                    end( $fileExtension );
                                    $attachment_type = current( $fileExtension );
                                    $url = $fieldValue;
                                  } else {
                                    $attachment_type = ihc_get_attachment_details($fieldValue, 'extension');
                                    $url = wp_get_attachment_url($fieldValue);
                                  }
                                  $imgClass = isset( $field['img_class'] ) ? $field['img_class'] : 'ihc-member-photo';
                                  switch ($attachment_type){
                                    case 'jpg':
                                    case 'jpeg':
                                    case 'png':
                                    case 'gif':
                                      //print the picture
                                      ?>
                                      <img src="<?php echo $url;?>" class="<?php echo $imgClass;?>" /><div class="ihc-clear"></div>
                                      <?php
                                      break;
                                    default:
                                      //default file type
                                      ?>
                                      <div class="ihc-icon-file-type"></div>

                                      <?php
                                      break;
                                  }
                                  ?>
                                  <?php
                                  $attachment_name = ihc_get_attachment_details($fieldValue);
                                }
                                ?>
  <?php if ( $fieldValue != '' ):?>
      <div class="ihc-file-name-uploaded"><a href="<?php echo $url;?>" target="_blank"><?php echo $attachment_name;?></a></div>
      <div onClick='ihcDeleteFileViaAjax( "<?php echo $fieldValue;?>", <?php echo $data['uid'];?>, "#ihc_fileuploader_wrapp_<?php echo $rand;?>", "<?php echo $fieldName;?>", "#ihc_upload_hidden_<?php echo $rand;?>");' class="ihc-delete-attachment-bttn"><?php _e( 'Remove', 'ihc' );?></div>
  <?php endif;?>
  <input type="hidden" value="<?php echo $fieldValue;?>" name="<?php echo $fieldName;?>" id="ihc_upload_hidden_<?php echo $rand;?>" />
</div>
