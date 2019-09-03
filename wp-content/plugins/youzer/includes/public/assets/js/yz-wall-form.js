( function( $ ) {

	'use strict';

	$( document ).ready( function() {

		/**
		 * Submit Wall Posts.
		 */
		$( '.yz-wall-post' ).on( 'click', function() {

			// Init Vars.
			var last_date_recorded = 0,
				button = $( this ),
				button_title = $( this ).text(),
				form   = button.closest( 'form#yz-wall-form' ),
				inputs = {}, post_data, object;

			// Get all inputs and organize them into an object {name: value}
			$.each( form.serializeArray(), function( key, input ) {
				// Only include public extra data
				if ( '_' !== input.name.substr( 0, 1 ) && 'whats-new' !== input.name.substr( 0, 9 ) ) {
					if ( ! inputs[ input.name ] ) {
						inputs[ input.name ] = input.value;
					} else {
						// Checkboxes/dropdown list can have multiple selected value
						if ( ! $.isArray( inputs[ input.name ] ) ) {
							inputs[ input.name ] = new Array( inputs[ input.name ], input.value );
						} else {
							inputs[ input.name ].push( input.value );
						}
					}
				}
			} );

			form.find( '*' ).each( function() {
				if ( $.nodeName( this, 'textarea' ) || $.nodeName( this, 'input' ) ) {
					$( this ).prop( 'disabled', true );
				}
			} );

			// Disable Emojionearea Editor.
	        if ( form.find( '.yz-wall-textarea' ).get(0).emojioneArea ) {
	        	form.find( '.yz-wall-textarea' ).data( 'emojioneArea' ).disable();
	        }

			/* Disable Button & Display Loader. */
			button.addClass( 'loading' );
			button.prop('disabled', true );
			form.addClass( 'submitted' );
			button.css( 'min-width', button.css( 'width' ) );
			button.html( '<i class="fas fa-spinner fa-spin"></i>' );

			/* Default POST values */
			var object = '';
			var item_id = $( '#yz-whats-new-post-in' ).val();
			var content = $( '#whats-new' ).val();
			var firstrow = $( '#buddypress ul.activity-list li' ).first();
			var activity_row = firstrow;
			var timestamp = null;

			// Checks if at least one activity exists
			if ( firstrow.length ) {

				if ( activity_row.hasClass( 'load-newest' ) ) {
					activity_row = firstrow.next();
				}

				timestamp = activity_row.prop( 'class' ).match( /date-recorded-([0-9]+)/ );
			}

			if ( timestamp ) {
				last_date_recorded = timestamp[1];
			}

			/* Set object for non-profile posts */
			if ( item_id > 0 ) {
				object = $( '#yz-whats-new-post-object' ).val();
			}

			post_data = $.extend( {
				action: 'yz_post_update',
				'cookie': bp_get_cookies(),
				'_yz_wpnonce_post_update': $( '#_yz_wpnonce_post_update' ).val(),
				'content': content,
				'object': object,
				'item_id': item_id,
				'since': last_date_recorded,
				'_bp_as_nonce': $( '#_bp_as_nonce' ).val() || ''
			}, inputs );

			$.post( ajaxurl, post_data, function( response ) {

				form.find( '*' ).each( function() {
					if ( $.nodeName( this, 'textarea' ) || $.nodeName( this, 'input' ) ) {
						$( this ).prop( 'disabled', false );
					}
				});

	            if ( form.find( '.yz-wall-textarea' ).get( 0 ).emojioneArea ) {
	            	form.find( '.yz-wall-textarea' ).data( 'emojioneArea' ).enable();
	            }

            	// Get Response Data.
				if ( $.yz_isJSON( response ) ) {
            		var res = $.parseJSON( response );
					$.yz_DialogMsg( 'error', res.error );
				} else {

					// Show Check .
					button.html( '<i class="fas fa-check"></i>' ).hide().fadeIn( 'slow' );
	
					form.find( '.yz-delete-attachment' ).trigger( 'click' );
					
					if ( 0 === $('ul.activity-list').length ) {
						$( 'div.error' ).slideUp( 100 ).remove();
						$( '#message' ).slideUp( 100 ).remove();
						$( 'div.activity').append( '<ul id="activity-stream" class="activity-list item-list">' );
					}

					if ( firstrow.hasClass( 'load-newest' ) ) {
						firstrow.remove();
					}

					$( '#activity-stream' ).prepend( response );
	
					if ( ! last_date_recorded ) {
						$( '#activity-stream li:first' ).addClass( 'new-update just-posted' );
					}	

					// Scroll To Added Post.
					$('body,html').animate({
					    scrollTop: $( '#' + $( response ).attr( 'id') ).offset().top - 65 + 'px'
					}, 1000);

					if ( 0 !== $( '#latest-update' ).length ) {
						var l   = $( '#activity-stream li.new-update .activity-content .activity-inner p' ).html(),
							v     = $( '#activity-stream li.new-update .activity-content .activity-header p a.view' ).attr('href'),
							ltext = $( '#activity-stream li.new-update .activity-content .activity-inner p' ).text(),
							u     = '';

						if ( ltext !== '' ) {
							u = l + ' ';
						}

						u += '<a href="' + v + '" rel="nofollow">' + BP_DTheme.view + '</a>';

						$( '#latest-update' ).slideUp( 300,function() {
							$( '#latest-update' ).html( u );
							$( '#latest-update' ).slideDown( 300 );
						});
					}

					$( 'li.new-update' ).hide().slideDown( 300 );
					$( 'li.new-update' ).removeClass( 'new-update' );
					$( '#whats-new' ).val( '' );

					// Init Slider.
					if ( inputs.post_type == 'activity_slideshow' )  {
						$.youzer_sliders_init();
					}
					

					// Reset Form.
					form.get( 0 ).reset();
					
					// Reset Text Form.
		            if ( form.find( '.yz-wall-textarea' ).get( 0 ).emojioneArea ) {
		            	form.find( '.yz-wall-textarea' ).get( 0 ).emojioneArea.setText( '' );
		            }

		            // Reset Tagged Users Form.
		            form.find( '.yz-tagged-user .yz-tagusers-delete-user, .yz-list-delete-item, .yz-list-close-icon' ).trigger( 'click' );

		            // Select First Element.
		            form.find( 'div.yz-activity-privacy' ).find( '.list div' ).first().trigger( 'click' );
		            form.find( '#yz-whats-new-post-in' ).next( '.nice-select' ).find( '.list div' ).first().trigger( 'click' );
		            form.find( '.yz-wall-options input:radio[name="post_type"]' ).first().trigger( 'change' );
		            form.find( '.yz-lp-prepost .lp-button-cancel' ).trigger( 'click' );

					// reset vars to get newest activities
					newest_activities = '';
					activity_last_recorded  = 0;

				}

				setTimeout( function() {
					// Change Submit Button Text.
					button.html( button_title ).fadeIn( 'slow' );
				}, 1000 );
		
				// Enable Submit Button.
				form.find( '.yz-wall-post,.yz-update-post' ).prop( 'disabled', false ).removeClass( 'loading' );

			});

			return false;
		});
	
		/*
		 * Show/Hide Link Form.
		 **/
		$( document ).on( 'change', 'input:radio[name="post_type"]', function() {
			// Get Post type.
			var form = $( this ).closest( 'form' );
			var post_type = $( this ).val();
			var uploader = $( this ).data( 'uploader' );
			var inputs_lenght = $( 'input:radio[name="post_type"]' ).length;
			
			if( inputs_lenght > 1 ) {

				if (  form.find( '.yz-wall-custom-form[data-post-type!="' + post_type + '"]' ).length == 0 ) {
	            	form.find( '.yz-wall-custom-form[data-post-type="' + post_type + '"]' ).fadeIn();
				} else {
		            form.find( '.yz-wall-custom-form[data-post-type!="' + post_type + '"]' ).fadeOut( 1, function() {
		            	form.find( '.yz-wall-custom-form[data-post-type="' + post_type + '"]' ).fadeIn();
		            });
				}

			} else {
	            form.find( '.yz-wall-custom-form[data-post-type="' + post_type + '"]' ).fadeIn();
			}

	        // Show/Hide Upload Button
	        if ( uploader == 'on' ) {
	            form.find( '.yz-wall-actions .yz-wall-upload-btn' ).fadeIn();
	        } else {
	            form.find( '.yz-wall-actions .yz-wall-upload-btn' ).fadeOut();
	        }

	        // Remove Old Attachments
	       	form.find( '.yz-attachment-item' ).remove();

	    });

		// Display Form Fields After Page Load.
		$( 'input:radio[name="post_type"]' ).first().trigger( 'change' );

		// Init Vars.
		var yz_atts_count = 0, yz_nxt_atts_id = 0, yz_atts_files = null,
			yz_wall_form = $( '#yz-wall-form' );

		/**
		 * Open Files Uploader
		 */
		$( document ).on( 'click', '.yz-wall-upload-btn', function( e ) {

			var $form = $( this ).closest( 'form' );

			// Check Files Number.
			if ( ! $.yz_CheckFilesNumber( $form ) || $form.find( '.yz-file-progress' )[0] ) {
				return false;
			}

			// Trigger Click
		    $( this ).closest( 'form' ).find( '#yz-upload-attachments' ).click();

		    e.preventDefault();
		});

		/**
		 * Submit form to Upload Files.
		 */
		$( document ).on( 'change', '#yz-upload-attachments', function ( e ) {

		    e.preventDefault();

			// Get form Files.
    		var files = $( this ).get( 0 );

    		// Get Files.
    		yz_atts_files = files;

			// var file = files[0];
			// // var file = event.target.files[0];

			// var fileReader = new FileReader();

			// if ( file.type.match( 'video.*' ) ) {

			//     fileReader.onload = function() {

			// 		var blob = new Blob( [ fileReader.result ], { type: file.type });
			// 		var url = URL.createObjectURL( blob );
			// 		var video = document.createElement( 'video' );
			// 		var timeupdate = function() {
			// 			if ( yz_video_snapImage() ) {
			// 				video.removeEventListener( 'timeupdate', timeupdate );
			// 				video.pause();
			// 			}
			// 		};

			// 		video.addEventListener('loadeddata', function() {
			// 			if ( yz_video_snapImage() ) {
			// 			 video.removeEventListener( 'timeupdate', timeupdate );
			// 			}
			// 		});

			// 	    var yz_video_snapImage = function() {
			// 	        var canvas = document.createElement( 'canvas' );
			// 	        canvas.width = video.videoWidth;
			// 	        canvas.height = video.videoHeight;
			// 	        canvas.getContext( '2d' ).drawImage( video, 0, 0, canvas.width, canvas.height );
			// 	        var image = canvas.toDataURL();
			// 	        var success = image.length > 100000;
			// 	        if ( success ) {
			// 				var img = document.createElement( 'img' );
			// 				img.src = image;
			// 				document.getElementsByTagName( 'div' )[0].appendChild( img );
			// 				$( '.yz-form-attachments' ).append( img );
			// 				URL.revokeObjectURL(url);
			// 	        }
			// 	        return success;
			// 	    };

			// 		video.addEventListener( 'timeupdate', timeupdate );
			// 		video.preload = 'metadata';
			// 		video.src = url;
			// 		// Load video in Safari / IE11
			// 		video.muted = true;
			// 		video.playsInline = true;
			// 		video.play();
			//     };

			//     fileReader.readAsArrayBuffer( file );

			// }
    		// Upload Files.
			$.yz_UploadFiles(
				$( this ).closest( 'form' ),
					{
					'attachments': files,
					'max_size': Yz_Wall.max_size,
					'max_number': 20,
					'allowed_extensions': ['png', 'jpg', 'jpeg', 'gif', 'mp4', 'ogv', 'ogg', 'mp3', 'wav', 'webm' ]
				}
			);

		});

	    /**
	     * Validate Uploader Attachments.
	     */
	    $.yz_validate_attachment = function ( form, file, options ) {

			// Get Options.
        	var yz_options = $.extend( {
        		allowed_extensions : [ "jpg", "jpeg", "gif", "png" ],
        		max_number : 3,
        		max_size : 3
        	}, options ), dialog;

			// Check File Size.
			if ( file.size > yz_options.max_size * 1048576 ) {
				$.yz_DialogMsg( 'error', Yz_Wall.invalid_file_size );
				return false;
			}

			// Check Files Number.
			if ( ! $.yz_CheckFilesNumber( form ) ) {
				return false;
			}

			// Get Current Attachment Number.
			var attachments_nbr = form.find( '.yz-attachment-item' ).length + 1;

	    	// Check Files number.
			if ( attachments_nbr > yz_options.max_number ) {
				$.yz_DialogMsg( 'error', Yz_Wall.invalid_files_number );
				return false;
			}

			// Check File Extension.
			var ext = file.name.split( '.' ).pop().toLowerCase();

			// Check If File Is Video .
			if ( $.yz_isPostType( form, 'activity_video' ) ) {
				var allowed_video_extentions = Yz_Wall.video_extentions;
				if ( $.inArray( ext, allowed_video_extentions ) == -1 ) {
					$.yz_DialogMsg( 'error', Yz_Wall.invalid_video_ext );
					return false;
				}
			}

			// Check If File Is Image .
			if ( $.yz_isPostType( form, 'activity_photo' ) || $.yz_isPostType( form, 'activity_slideshow' ) ) {
				var allowed_image_extentions = Yz_Wall.image_extentions;
				if ( $.inArray( ext, allowed_image_extentions ) == -1 ) {
					$.yz_DialogMsg( 'error', Yz_Wall.invalid_image_ext );
					return false;
				}
			}

			// Check If File extention allowed .
			if ( $.yz_isPostType( form, 'activity_file' ) ) {
				var allowed_file_extentions = Yz_Wall.file_extentions;
				if ( $.inArray( ext, allowed_file_extentions ) == -1 ) {
					$.yz_DialogMsg( 'error', Yz_Wall.invalid_file_ext );
					return false;
				}
			}

			// Check If File Is Image .
			if ( $.yz_isPostType( form, 'activity_audio' ) ) {
				var allowed_audio_extentions = Yz_Wall.audio_extentions;
				if ( $.inArray( ext, allowed_audio_extentions ) == -1 ) {
					$.yz_DialogMsg( 'error', Yz_Wall.invalid_audio_ext );
					return false;
				}
			}

			return true;
	    }

		/**
		 * Upload Files.
		 */
		$.yz_UploadFiles = function ( form, options ) {

			// Get Options.
        	var qto = $.extend({
        		allowed_extensions : Yz_Wall.default_extentions,
        		max_number : 3,
        		max_size : 3
        	}, options ), dialog;

        	// Get Files.
        	var files = qto.attachments.files;

    		for ( var i = 0; i < files.length ; i++ ) {

				// Get File.
    			var file = files[i];

    			if ( ! $.yz_validate_attachment( form, file, qto ) ) {
    				return false;
    			}

        		// Get Attachment Item Html Code.
        		var qt_AttachmentItem = $.youzerAttachmentItem({
        			'file' : file,
        			'file_name': file.name
        		});

        		// Append Item To the Attachments List.
        		form.find( '.yz-form-attachments' ).append( qt_AttachmentItem );

        		// Upload File. 
        		if ( i == 0 ) {
        			$.yz_UploadFile( form, file );
        		}

			}

		}

		/**
		 * Get Attachment Item HTML Code.
		 */
		$.youzerAttachmentItem = function ( options ) {

			// Get Option.
			var qto = $.extend( {}, options ), file_code, image_code, file_name;

			// Get File Name.
			file_name = $.yz_GetNameExcerpt( qto.file_name );
	
			// Get Files HTML Code.
			file_code =  '<div class="yz-attachment-item yz-file-preview">' +
							'<div class="yz-attachment-details">' +
								'<i class="fas fa-hourglass-half yz-file-icon"></i>' +
								'<span class="yz-file-name">' + file_name + '</span>' +
							'</div>' +
							'<div class="yz-file-progress">' +
								'<span class="yz-file-upload"></span>' +
							'</div>' +
							'<input type="hidden" class="yz-attachment-data" name="attachments_files[]" />' +
						'</div>';
	
			// Get Image Preview HTML Code.
			image_code =  '<div class="yz-attachment-item yz-image-preview">' +
							'<div class="yz-attachment-details">' +
								'<i class="fas fa-hourglass-half yz-file-icon"></i>' +
							'</div>' +
							'<div class="yz-file-progress">' +
								'<span class="yz-file-upload"></span>' +
							'</div>' +
							'<input type="hidden" class="yz-attachment-data" name="attachments_files[]" />' +
						'</div>';

			// Return Item Code.
			if ( $.yz_CheckIsFileImage( qto.file ) ) {
				return image_code;
			} else {
				return file_code;
			}

		}

		/**
		 * Upload Attachments.
		 */
		$.yz_UploadFile = function ( form, file ) {

			// Get Attachment Item.
			var item = form.find( '.yz-file-progress:first' ).parent( '.yz-attachment-item' );

			// Create New Form Data.
		    var formData = new FormData();

		    // Fill Form with Data.
		    formData.append( 'image', file );
		    formData.append( 'action', 'yz_upload_wall_attachments' );
		    formData.append( 'security', form.find( 'input[name="_yz_wpnonce_post_update"]' ).val() );

		    // Upload File.
		    $.ajax({
		        type  : 'POST',
		        url   : Youzer.ajax_url,
		        data  : formData,
		        cache : false,
		        contentType: false,
		        processData: false,
		        xhr: function() {
	                var YouzerXhr = $.ajaxSettings.xhr();
	                if ( YouzerXhr.upload ) {

	                	// Disable submit button.
						form.find( '.yz-wall-post,.yz-update-post' ).attr( 'disabled', true );

	                    YouzerXhr.upload.addEventListener( 'progress', function( e ) {
						    if ( e.lengthComputable ) {

						   		// Set up Variables.
						        var max = e.total,
						        	current = e.loaded,
						        	Percentage = ( current * 100 ) / max;

						        // Get Progress Bar
						       	var progress_bar = item.find( '.yz-file-upload' );
						       	
						       	// Upload Started Class.
						       	var yz_loading_icon = 'fas fa-spinner fa-spin yz-file-icon';

						       	// Add loader icon
		        				item.find( '.yz-file-icon' ).attr( 'class', yz_loading_icon );

						       	// Update Upload status.
						        progress_bar.css( 'width', Percentage  + '%' );

						        if ( Percentage >= 100 ) {
						        	// Change Progress Bar Class .
						        	progress_bar.addClass( 'yz-file-uploaded' );
						        }

				    		}  

	                    });
	                }
	                return YouzerXhr;
		        },

		        success: function( result ) {

	            	// Get Response Data.
	            	var res = $.parseJSON( result );

		            if ( res.error ) {

		            	// Show Error Message
		            	$.yz_DialogMsg( 'error', res.error );

		            	// Remove Item.
		            	item.remove();

						// Check Upload Progress to Enable Submit Field.
						$.yz_CheckUploadProgress( form );

	            		return false;
		            }
			        
			        // Prepare Trash Icon
		        	var trash_icon = '<i class="fas fa-trash-alt yz-delete-attachment"></i>',
		        		paperclip_icon = 'fas fa-paperclip yz-file-icon';
		        	
		        	// Remove Progress Bar.
		        	item.find( '.yz-file-progress' ).fadeOut( 400, function() {
		        		
		        		// Remove Progress Div.
		        		$( this ).remove();
		        		
		        		// Let's Upload Next File.
		        		$.yz_upload_next_file( form );

						// Check Upload Progress to Enable Submit Field.
						$.yz_CheckUploadProgress( form );

		        	});
		        	
		        	// Delete Loader Icon.
					if ( $.yz_CheckIsFileImage( file ) ) {
			        	item.find( '.yz-file-icon' ).remove();
			        }

			   		// Change Loader Icon with paperclip icon.
		        	item.find( '.yz-file-icon' ).attr( 'class', paperclip_icon );

		        	// Add Trash Icon to the attachment item.
		        	item.find( '.yz-attachment-details' ).append( trash_icon );

		        	// Update Item Attachments Data.
					item.find( '.yz-attachment-data' ).val( result );

					var fileReader = new FileReader();

					if ( file.type.match( 'video.*' ) ) {

					    fileReader.onload = function() {

							var blob = new Blob( [ fileReader.result ], { type: file.type });
							var url = URL.createObjectURL( blob );
							var video = document.createElement( 'video' );
							var timeupdate = function() {
								if ( yz_video_snapImage() ) {
									video.removeEventListener( 'timeupdate', timeupdate );
									video.pause();
								}
							};

							video.addEventListener('loadeddata', function() {
								if ( yz_video_snapImage() ) {
								 video.removeEventListener( 'timeupdate', timeupdate );
								}
							});

						    var yz_video_snapImage = function() {
						        var canvas = document.createElement( 'canvas' );
						        canvas.width = video.videoWidth;
						        canvas.height = video.videoHeight;
						        canvas.getContext( '2d' ).drawImage( video, 0, 0, canvas.width, canvas.height );
						        var image = canvas.toDataURL( 'image/jpeg' );
						        var success = image.length > 100000;
						        
						        if ( success ) {
									var video_data = JSON.parse( item.find( '.yz-attachment-data' ).val() );
									video_data[0].video_thumbnail = image;
									item.find( '.yz-attachment-data' ).val( JSON.stringify( video_data ) );
									// var img = document.createElement( 'img' );
									// img.src = image;
									// document.getElementsByTagName( 'div' )[0].appendChild( img );
									URL.revokeObjectURL(url);
						        }

						        return success;
						    };

							video.addEventListener( 'timeupdate', timeupdate );
							video.preload = 'metadata';
							video.src = url;
							// Load video in Safari / IE11
							video.muted = true;
							video.playsInline = true;
							video.play();
					    };

					    fileReader.readAsArrayBuffer( file );

					}
					// Get File Data.
					var file_data = $.parseJSON( result );

					// Get Temporary File Name
					var filename = $.map( file_data, function( file ) { return file.original; });
					
					var img_preview =  Yz_Wall.base_url + 'temp/' + filename[0];

					item.css( 'background-image', 'url(' + img_preview + ')' );

		        },
		        
		        error : function( XMLHttpRequest, textStatus, errorThrown ) {

	            	// Remove Item.
	            	item.remove();

					$.yz_DialogMsg( 'error', textStatus );
	            	
	            	// Check Upload Progress to Enable Submit Field.
					$.yz_CheckUploadProgress( form );

	            	$.yz_upload_next_file( form );

		        }

		    });

		}

		/**
		 * Upload Next File
		 */
		 $.yz_upload_next_file = function( form ) {

    		// Let's Upload Next File.
    		yz_atts_count++;

        	if ( typeof yz_atts_files.files[ yz_atts_count ] !== 'undefined' ) {
        		$.yz_UploadFile( form, yz_atts_files.files[ yz_atts_count ] );
        	}

		}

		/**
		 * Get File Name Excerpt.
		 */
		$.yz_GetNameExcerpt = function ( name ) {

		    // Set up Variables.
			var strLen = 25,
		    	separator = '...';

		    // If file name not too long keep it.
		    if ( name.length <= strLen ) {
		    	return name;
		    }

		    // Set up Variables.
		    var sepLen = separator.length,
		        charsToShow = strLen - sepLen,
		        frontChars = Math.ceil(charsToShow/2),
		        backChars = Math.floor(charsToShow/2);

		    // Shorten File Name.
		    return name.substr( 0, frontChars ) + separator + name.substr(name.length - backChars);
		};

		/**
		 * Delete Attachment .
		 */
        $( document ).on( 'click', '.yz-delete-attachment' , function( e ) {

        	// Get Form.
        	var form = $( this ).closest( 'form' );

        	// Get Attachment item.
        	var attachment = $( this ).closest( '.yz-attachment-item' );

        	// Get File Data.
			var file_data = $.parseJSON( attachment.find( '.yz-attachment-data' ).val() );
        	
			// Get Temporary File Name
			var filename = $.map( file_data, function( file ) { return file.original; });

			// Remove Attachment from Form.
			attachment.remove();

			// Remove Attachment from Directory.
			$.yz_DeleteAttachment( form, filename[0] );

        });

		/**
		 * Delete Attachment File.
		 */
		$.yz_DeleteAttachment = function( form, file ) {

			// Create New Form Data.
		    var formData = new FormData();

		    // Fill Form with Data.
		    formData.append( 'attachment', file );
		    formData.append( 'action', 'yz_delete_wall_attachment' );
			formData.append( 'security', form.find( 'input[name="_yz_wpnonce_post_update"]' ).val() );

			$.ajax({
                type: "POST",
                data: formData,      
                url: ajaxurl,
		        contentType: false,
		        processData: false
			});

		}

		/**
		 * Display Tag Users Search Box & Friends List.
		 */
		$( document ).on( 'click', '.yz-tag-users-tool', function() {

			var form = $( this ).closest( 'form' );

        	// Hide All Form Opened Lists.
        	form.find( '.yz-feeling-form' ).fadeOut();

			if ( $( this ).hasClass( 'loaded' ) ) {
				form.find( '.yz-tagusers-form' ).fadeToggle();
				form.find( '.yz-tagusers-search-input' ).focus();
				return;
			}

			var button = $( this ),
				icon = button.find( 'i' ),
				old_icon = icon.attr( 'class' );

			// Display Loader.
			icon.attr( 'class', 'fas fa-spinner fa-spin' );

			// Get User Friends.
	        $.ajax({
	            type: 'POST',
	            url: ajaxurl,
	            dataType: 'json',
	            data: { 'action': 'yz_tag_users_get_user_friends' },
	            success: function( response ) {
		        	
	            	button.addClass( 'loaded' );
		        	var	list = $( '<div></div>' ).append( response.data );
				
		        	
		        	// Hide Selected Items.
		        	if ( form.parent().attr( 'id' ) == 'youzer-edit-activity-wrapper' ) {
		        		
		        		var tagged_users_ids = [];

		        		form.find( 'input[name="tagged_users[]"]' ).each(function() {
		        			list.find( '.yz-list-item[data-user-id="100"] ').fadeOut();
						    tagged_users_ids.push($(this).val());
						});

		        		list.find( '.yz-list-item' ).each(function( i, data ) {

						   if ( jQuery.inArray( $( this ).attr( 'data-user-id' ), tagged_users_ids ) !== -1 ) {
						   	$ ( this ).find( '.yz-wall-tag-user' ).hide();
						   }

						});

		        	}
	            	// console.log ( list ); 
	            	form.find( '.yz-tagusers-form' ).fadeIn( 200, function() {
	            		icon.attr( 'class', old_icon );
	            		form.find( '.yz-tagusers-search-input' ).focus();
	            		form.find( '.yz-wall-tagusers-list' ).html( list );
	            	});
	            }
	        });

		});

		/**
		 * Tag Users Search.
		 */
		$( document ).on( 'keyup', '.yz-list-search-input', function() {
			var form = $( this ).closest( '.yz-wall-list' );
			var value = this.value.toLowerCase().trim();
			form.find( '.yz-wall-list-items .yz-list-item' ).show().filter(function() {
				return $( this ).find( '.yz-item-title, .yz-item-description' ).text().toLowerCase().trim().indexOf( value ) == -1;
			}).hide();
		});

		/**
		 * Display Feeling/Activity Search Box & Categories..
		 */
		$( document ).on( 'click', '.yz-user-mood-tool', function() {

			var form = $( this ).closest( 'form' );

	        form.find( '.yz-tagusers-form' ).fadeOut();
			
			if ( $( this ).hasClass( 'loaded' ) ) {
				form.find( '.yz-feeling-form' ).fadeToggle();
				form.find( '.yz-feeling-search-input' ).focus();
				return;
			}

			// Set Place Holder Attribute.
			form.find( '.yz-feeling-form' ).attr( 'data-placeholder', form.find( '.yz-feeling-search-input' ).attr( 'placeholder' ) );

			var button = $( this ),
				icon = button.find( 'i' ),
				old_icon = icon.attr( 'class' );

			// Display Loader.
			icon.attr( 'class', 'fas fa-spinner fa-spin' );

			// Get User Friends.
	        $.ajax({
	            type: 'POST',
	            url: ajaxurl,
	            dataType: 'json',
	            data: { 'action': 'yz_feeling_activity_get_categories' },
	            success: function( response ) {
	            	// Hide All Form Opened Lists.
	            	button.addClass( 'loaded' );
	            	form.find( '.yz-feeling-form' ).fadeIn( 200, function() {
	            		icon.attr( 'class', old_icon );
	            		form.find( '.yz-feeling-search-input' ).focus();
	            		form.find( '.yz-wall-feeling-list' ).html( response.data );
	            	});
	            }
	        });

		});

		/**
		 * Select Feeling/Activity Category.
		 **/
		$( document ).on( 'click', '.yz-feeling-item', function( e ) {

			e.preventDefault();

			// Init Vars.
			var el = $( this ), parent = el.closest( '.yz-wall-feeling' );

			if ( $( this ).parent().hasClass( 'yz-list-category-items' ) ) {

				// Select Emoji.
				$.yz_select_mood_emojis( parent, el.attr( 'data-emoji' ), el.attr( 'data-category-title' ), el.find( '.yz-item-img' ).css( 'background-image' ) );

				return;
			}

			// Add Category Title.
			parent.find( '.yz-list-items-title' ).text( el.attr( 'data-category-title' ) );
			parent.find( 'input[name="mood_type"]' ).val( el.attr( 'data-category-title' ) );

			// Hide Categories.
			parent.find( '.yz-list-categories' ).fadeOut();
			parent.find( '.yz-feeling-close-icon' ).addClass( 'yz-feeling-go-back' );
			parent.find( '.yz-feeling-close-icon i' ).attr( 'class', 'far fa-arrow-alt-circle-left' );

			// Change Search Form Place Holder.
			parent.find( 'input[name="mood_search"]' ).val( '' ).attr( 'placeholder', el.find( '.yz-item-description' ).text() );
			parent.find( '.yz-list-search-icon i' ).attr( 'class', el.find( '.yz-item-icon i' ).attr( 'class' ) );

			// Show Category.
			var current_category = parent.find( '.yz-list-category-items[data-category="' + $( this ).attr( 'data-category' ) + '"]' );
			if ( current_category.length == 1 ) {
				current_category.fadeIn();
			} else {
				parent.find( '.yz-list-submit-button' ).fadeIn();
			}

		});

		/**
		 * Hide Feeling/Activity Box.
		 **/
		$( document ).on( 'click', '.yz-feeling-close-icon', function() {

			// Init Vars.
			var el = $( this ), parent = el.closest( '.yz-wall-feeling' );

			// Reset Form.
			parent.find( '.yz-feeling-submit-button' ).fadeOut();
			parent.find( '.yz-feeling-search-input' ).val( '' );
			parent.find( '.yz-feeling-search-input' ).attr( 'placeholder', parent.find( '.yz-feeling-form' ).attr( 'data-placeholder' ) );
			parent.find( '.yz-feeling-search-icon i' ).attr( 'class', 'fas fa-search' );

			if ( el.hasClass( 'yz-feeling-go-back' ) ) {
				el.find( 'i' ).attr( 'class', 'fas fa-times' );
				parent.find( '.yz-list-categories, .yz-list-item' ).fadeIn();
				parent.find( '.yz-list-category-items' ).fadeOut();
				el.removeClass( 'yz-feeling-go-back' );
				return;
			}

			// Hide Box.
			el.closest( '.yz-feeling-form' ).fadeOut();

		});

		/**
		 * Edit Feeling / Activity Item.
		 */
		$( document ).on( 'click', '.yz-wall-feeling .yz-list-edit-item', function() {
			
			// Init Vars.
			var el = $( this ), parent = el.closest( '.yz-wall-feeling' ), selected_items = parent.find( '.yz-list-selected-items' );

			selected_items.fadeOut( 200, function() {

				parent.find( '.yz-feeling-search-input' ).val( selected_items.find( '.yz-item-title' ).text() );

				// Delete Item.
				el.closest( '.yz-selected-item' ).fadeOut( 200, function() { $( this ).remove() });

				// Display Search Box.
				parent.find( '.yz-feeling-form' ).fadeIn();

				// Remove Class
				parent.removeClass( 'yz-activity-feeling-selected' );

			});

		});

		/**
		 * Delete Feeling / Activity Item.
		 */
		$( document ).on( 'click', '.yz-wall-feeling .yz-list-delete-item', function() {
			
			// Init Vars.
			var el = $( this ), parent = el.closest( '.yz-wall-feeling' ), selected_items = parent.find( '.yz-list-selected-items' );


			selected_items.fadeOut( 200, function() {
				
				// Reset Selected Item Text.
				selected_items.find( '.yz-item-title' ).text( '' );

				// Delete Item.
				el.closest( '.yz-selected-item' ).fadeOut( 200, function() { $( this ).remove() });

				parent.find( '.yz-feeling-close-icon' ).trigger( 'click' );

				// Remove Class
				parent.removeClass( 'yz-activity-feeling-selected' );

			});

		});

		/**
		 * Enter User Activity Value.
		 **/
		$( document ).on( 'click', '.yz-feeling-submit-button', function() {

			// Init Vars.
			var parent = $( this ).closest( '.yz-wall-feeling' ), value = parent.find( '.yz-feeling-search-input' ).val();

			if ( value.trim() == '' ) {
				return;
			}

			// Select Emoji.
			$.yz_select_mood_emojis( parent, value, value );

		});

		// Set Enter On Submit Search Button.
		$( document ).on( 'keypress', '.yz-feeling-search-input', function( e ) {
			if( e.keyCode == 13 ) {
				e.preventDefault();
				var submit = $( this ).next( '.yz-feeling-submit-button' );
				if ( submit.css( 'display' ) != 'none' ) {
					submit.click();
				}
			}
		});

		/**
		 * Display Selected Item
		 */
		$.yz_select_mood_emojis = function( parent, value, title, image ) {

			if ( parent.hasClass( 'yz-activity-feeling-selected' ) ) {
				// alert( 'hhaha');
				// return;
				parent.find( '.yz-selected-item' ).remove();
			}

			// Add Class.
			parent.addClass( 'yz-activity-feeling-selected' );

			// Display Selected Element.
			parent.find( '.yz-feeling-form' ).fadeOut( 0, function() {
				parent.find( '.yz-feeling-selected-items' ).fadeIn();
			});

			var image_div = ( typeof image === "undefined" || image === null ) ? '' : "<div class='yz-item-img' style='background-image: " + image + ";'></div>";
			var div_class = ( typeof image === "undefined" || image === null ) ? ' yz-selected-item-no-image' : '';

			// Parent.
			parent.find( '.yz-list-selected-items' ).append(
				'<div class="yz-selected-item yz-feeling-selected-item' + div_class + '">' + image_div + '<div class="yz-item-title">' + title + '</div>' +
				'<i class="fas fa-pencil-alt yz-selected-item-tool yz-list-edit-item"></i>' +
				'<i class="fas fa-trash-alt yz-selected-item-tool yz-list-delete-item"></i>' + 
				'<input type="hidden" name="mood_value" value="' + value + '">' +
				'</div>' ).fadeIn();

			// Clear Search Input.
			parent.find( '.yz-feeling-search-input' ).val( '' );
		}

		/**
		 * Tag Users.
		 **/
		$( document ).on( 'click', '.yz-wall-tag-user', function() {

			// Init Vars.
			var button = $( this );

			if ( button.hasClass( 'yz-selected' ) ) {
				return;
			}

			// Add Class.
			button.addClass( 'yz-selected' );

			// Init Vars
			var parent = button.closest( '.yz-list-item' );

			$( this ).fadeOut( 200, function(){

				var item = '<div class="yz-selected-item yz-tagged-user">' +
						"<div class='yz-item-img' style='background-image: " + parent.find( '.yz-item-img' ).css( 'background-image' ) + ";'></div>" +
						'<div class="yz-item-title">' + parent.find( '.yz-item-title' ).text() + '</div><i class="fas fa-trash-alt yz-selected-item-tool yz-list-delete-item yz-tagusers-delete-user" data-user-id="' + parent.attr( 'data-user-id' ) + '"></i>' +
						'<input type="hidden" name="tagged_users[]" value="' + parent.attr( 'data-user-id' ) + '">' +
					'</div>';

				parent.closest( '.yz-wall-tagusers' ).find( '.yz-tagged-users' ).append( item );
				
				var users_count = button.closest( '.yz-wall-tagusers' ).find( '.yz-tagged-user' ).length;
				
				var form = button.closest( '.yz-wall-tagusers' );

				if ( form.find( '.yz-tagged-user' ).length == 1 ) {
					form.find( '.yz-tagged-users' ).fadeIn();
				}

			});
		});

		/**
		 * Delete Tagged Users.
		 **/
		$( document ).on( 'click', '.yz-list-close-icon.yz-tagusers-close-icon', function() {
			$( this ).closest( '.yz-tagusers-form' ).fadeOut();
		});

		/**
		 * Delete Tagged Users.
		 **/
		$( document ).on( 'click', '.yz-tagusers-delete-user', function() {

			// Init Vars.			
			var parent = $( this ).parent();
			var form = $( this ).closest( '.yz-wall-tagusers' );
			var tagged_users = $( this ).closest( '.yz-tagged-users' );
			
			// Display select button again.
			form.find( '.yz-list-item[data-user-id=' + $( this ).attr( 'data-user-id' ) + '] .yz-wall-tag-user' ).removeClass( 'yz-selected' ).fadeIn();

			$( this ).parent().fadeOut( 200, function() {
				$( this ).remove();
				var users_count = tagged_users.find( '.yz-tagged-user' ).length;
				if ( users_count == 0 ) {
					tagged_users.fadeOut();
				}
			});

		});

		/**
		 * Hide Privacy if the post will be added to groups.
		 */
		$( document ).on( 'change', '#yz-whats-new-post-in', function( e ) {

			if ( $( this ).val() != 0 ) {
				$( this ).closest( '.yz-wall-actions' ).find( 'div.yz-activity-privacy' ).fadeOut();				
			} else {
				$( this ).closest( '.yz-wall-actions' ).find( 'div.yz-activity-privacy' ).fadeIn();				
			}

		});

		/**
		 * Hide Privacy if the post will be added to groups.
		 */
		$( document ).on( 'click', '.yz-send-comment', function( e ) {

			e.preventDefault();

			if ( $( this ).hasClass( 'loading' ) ) {
				return;
			}

			var target = $( this ), form, content, form_parent, comment_id, form_id, tmp_id, ajaxdata, ak_nonce,
			new_count,show_all_a, old_icon;

			target.addClass( 'loading' );
			old_icon = target.find( 'i' ).attr( 'class' );
			target.find( 'i' ).attr( 'class', 'fas fa-spinner fa-spin' );

			// console( 'olaaa' );


			/* Activity comment posting */
			// if ( target.attr('name') === 'ac_form_submit' ) {
				form = target.parents( 'form' );
				form_parent = form.parent();
				form_id = form.attr('id').split('-');

				if ( !form_parent.hasClass('activity-comments') ) {
					tmp_id = form_parent.attr('id').split('-');
					comment_id = tmp_id[1];
				} else {
					comment_id = form_id[2];
				}

				content = $( '#' + form.attr('id') + ' textarea' );

				/* Hide any error messages */
				$( '#' + form.attr('id') + ' div.error').hide();
				target.addClass('loading').prop('disabled', true);
				content.addClass('loading').prop('disabled', true);

				ajaxdata = {
					action: 'new_activity_comment',
					'cookie': bp_get_cookies(),
					'_wpnonce_new_activity_comment': $('#_wpnonce_new_activity_comment').val(),
					'comment_id': comment_id,
					'form_id': form_id[2],
					'content': content.val()
				};

				// Akismet
				ak_nonce = $('#_bp_as_nonce_' + comment_id).val();
				if ( ak_nonce ) {
					ajaxdata['_bp_as_nonce_' + comment_id] = ak_nonce;
				}

				$.post( ajaxurl, ajaxdata, function(response) {
					target.removeClass('loading');
					content.removeClass('loading');
					target.find( 'i' ).attr( 'class', old_icon );
					/* Check for errors and append if found. */
					if ( response[0] + response[1] === '-1' ) {
						form.append( $( response.substr( 2, response.length ) ).hide().fadeIn( 200 ) );
					} else {
						var activity_comments = form.parent();
						form.fadeOut( 200, function() {
							if ( 0 === activity_comments.children('ul').length ) {
								if ( activity_comments.hasClass('activity-comments') ) {
									activity_comments.prepend('<ul></ul>');
								} else {
									activity_comments.append('<ul></ul>');
								}
							}

							/* Preceding whitespace breaks output with jQuery 1.9.0 */
							var the_comment = $.trim( response );

							activity_comments.children('ul').append( $( the_comment ).hide().fadeIn( 200 ) );
							form.children('textarea').val('');
							activity_comments.parent().addClass('has-comments');
						} );
						$( '#' + form.attr('id') + ' textarea').val('');

						/* Increase the "Reply (X)" button count */
						new_count = Number( $('#activity-' + form_id[2] + ' a.acomment-reply span').html() ) + 1;
						$('#activity-' + form_id[2] + ' a.acomment-reply span').html( new_count );

						// Increment the 'Show all x comments' string, if present
						show_all_a = activity_comments.parents('.activity-comments').find('.show-all a');
						if ( show_all_a ) {
							show_all_a.html( BP_DTheme.show_x_comments.replace( '%d', new_count ) );
						}
					}

					$(target).prop('disabled', false);
					$(content).prop('disabled', false);
				});

				return false;
			// }
		});

		// Submit Comment Form if user hits enter.
		$( document ).on( 'keypress', '.ac-form textarea', function( e ) {
			if ( e.which == 13 && !e.shiftKey ) {
                e.preventDefault();
                $( this ).closest( 'form' ).find( '.yz-send-comment' ).click();
            }
		});

		/*
		 * Check If Uploaded File Is Image.
		 **/
		$.yz_CheckIsFileImage = function( file ) {
			var fileType = file['type'];
			var ValidImageTypes = [ "image/gif", "image/jpeg", "image/png" ];
			if ( $.inArray( fileType, ValidImageTypes ) < 0 ) {
			    return false;
			}
			return true;
		}

		/*
		 * Check Upload Progress !!??
		 **/
		$.yz_CheckUploadProgress = function( form ) {
			if ( ! form.find( '.yz-file-progress' )[0] ) {
				form.find( '#yz-upload-attachments' ).val( '' );
				form.find( '.yz-wall-actions button[type="submit"]' ).attr( 'disabled' , false );
				// Reset Vars.
				yz_atts_count = 0;
				yz_atts_files = null;
			}
		}

		/**
		 * Check Post Type.
		 **/
	    $.yz_isPostType = function( form, post_type ) {
	    	if ( post_type == form.find( 'input:radio[name="post_type"]:checked' ).val() ) {
	    		return true;
	    	}
	    	return false;
	    }

		/*
		 * Check Files Number.
		 **/
		$.yz_CheckFilesNumber = function( form ) {
			if ( 'activity_photo' != form.find( 'input:radio[name="post_type"]:checked' ).val() && 'activity_slideshow' != form.find( 'input:radio[name="post_type"]:checked' ).val() && form.find( '.yz-attachment-item' )[0] ) {
				$.yz_DialogMsg( 'error', Yz_Wall.max_one_file );
				return false;
			}
			return true;
		}

	});

})( jQuery );