( function( $ ) {

	'use strict';

	$( document ).ready( function() {
		
		var current_edit_button;

		/**
		 * Get Edit Form.
		 **/
		$( document ).on( 'click', '.yz-edit-activity, .yz-edit-post', function( e ) {

			e.preventDefault();
			
			// Check if there's no other edit button is clicked on the same time.
			if ( $( '.yz-edit-activity.loading,.yz-edit-post.loading' )[0] ) {
				return;
			}

			// Init Vars.
			var $link = $( this ), $form, $form_wrapper,
			activity_id = $link.parents( 'li' ).attr('id').split('-')[1],
			activity_type = $link.attr( 'data-activity-type' );

			if ( $link.hasClass( 'loading' ) ) {
				return;
			}

			// Add Link Loading Icon.
			$link.addClass( 'loading' );

			// Set Current Buttonn.
			current_edit_button = $link;

			// Get Form Wrapper.
			$form_wrapper = $( '#youzer-edit-activity-wrapper');

			// Get Form Data.
			var data = {
				'action': 'yz_get_edit_activity_form',
				'yz_edit_activity_nonce': Youzer.security_nonce,
				'activity_id': activity_id,
				'activity_type': activity_type,
			};

// 			$.ajax({
//   url:ajaxurl,
//   type:"POST",
//   data:data,
//   contentType:"application/json; charset=utf-8",
//   dataType:"json",
//   success: function(response){
  							
// 				// Get Response.
// 				response = $.parseJSON( response );
// 				console.log( response );

//   }
// });
			// Process Verification.
			$.post( Youzer.ajax_url, data, function( response ) {
						
				// Get Response.
				response = $.parseJSON( response );

				if ( response.remove_button ) {
	    			// Remove Edit Button.
	    			current_edit_button.remove();
					$.yz_DialogMsg( 'error', response.error );
	    			return;
				}

				// Mark Button As laoded.
				$link.attr( 'data-loaded', 'true' );

	    		// Remove Loading Class.
	    		$link.removeClass( 'loading' );

	    		var $form = $( response.form );
	    		
	    		// Set Privacy.
				if ( response.privacy ) {
					$form.find( 'select.yz-activity-privacy option[value="' + response.privacy + '"]' ).attr( 'selected', true );
				}

				// Set Tagged Friends.
				if ( response.tagged_friends ) {
					console.log ( response.tagged_friends );
					$form.find( '.yz-tagged-users' ).append( response.tagged_friends );
					if ( $form.find( '.yz-tagged-user' ).length != 0 ) {
						$form.find( '.yz-tagged-users' ).fadeIn();
					}
				}

				// Set Mood.
				if ( response.mood ) {
					$form.find( '.yz-feeling-title' ).text( response.mood.title );
					$form.find( 'input[name="mood_type"]' ).val( response.mood.type );
					$form.find( '.yz-feeling-selected-items' ).append( response.mood.content ).fadeIn();
					$form.find( '.yz-wall-feeling' ).addClass( 'yz-activity-feeling-selected' );
				}

				// Display Selected Element.
				// parent.find( '.yz-feeling-form' ).fadeOut( 0, function() {
				// 	parent.find( '.yz-feeling-selected-items' ).fadeIn();
				// });

	    		// Init Nice Select.
				if ( jQuery.isFunction( $.fn.niceSelect ) ) {
	    			$form.find( 'select:not([multiple="multiple"])' ).niceSelect();
	    		}
				
				// Append Content.
				$( 'body' ).append( $form );

	    		// Remove Action Attribute.
	    		$form.find( 'form' ).attr( 'action' , '' );

	    		// Set Form Activity Type.
				if ( ! $form.find( 'input[name="post_type"]' )[0] ) {
					$form.find( '.yz-wall-options' ).append( '<input type="hidden" value="' + activity_type + '" name="post_type" />');
				}

	    		// Display Current Post Type Fields.
				$form.find( 'input:radio[name="post_type"]' ).first().trigger( 'change' );

				// Remove Unsed Elements
				$form.find( '#whats-new-post-in-box' ).remove();

				// Hide Form Options Tab
				$form.find( '.yz-wall-options' ).hide();

				if ( $form.find( '.yz-wall-attchments' )[0] ) {
					// Reorder Form Actions & Attachments.
				 	$form.find( '.yz-wall-actions' ).detach().insertAfter( $form.find( '.yz-wall-attchments' ) );
				}

				if ( activity_type == 'activity_giphy' && response.meta.giphy_image ) {
					$form.find( '.yz-wall-giphy-form .yz-wall-cf-item' ).hide( 1, function() {
						$form.find( '.yz-selected-giphy-item' ).show( 1 ).css( 'display', 'inline-block' )
						.prepend( '<img  src="' + response.meta.giphy_image + '" alt="">' );
						$form.find( '.yz-selected-giphy-item input[name="giphy_image"]' ).val( response.meta.giphy_image );
					});
				}

				// Set Main Textarea Content.
				$form.find( 'textarea[name="status"]' ).val( response.content );
				
			    // Activate Emojis in Posts.
			    // if ( response.posts_emojis == 'on' || response.comments_emojis == 'on' ) {
				    if (  
				    	 ( activity_type == 'activity_comment' && response.comments_emojis == 'on' )
				    	||
				    	( activity_type != 'activity_comment' && response.posts_emojis == 'on' ) 
				    	) {

    				if ( ! jQuery().emojioneArea ) {
				        $( '<script/>', { rel: 'text/javascript', src: Youzer.assets + 'js/emojionearea.min.js' } ).appendTo( 'head' );
				        $( '<link/>', { rel: 'stylesheet', href: Youzer.assets + 'css/emojionearea.min.css' } ).appendTo( 'head' );
				        // }
					} else {
						$form.find( '.yz-wall-textarea' ).emojioneArea( {
			                pickerPosition: 'bottom',
			                autocomplete: true,
			                saveEmojisAs : 'image',
			                events: {
			                ready: function () {
			                  // form.find( '.emojionearea-button-open' ).click();
			                  this.editor.textcomplete([{
			                      id: 'yz_mentions',
			                      match: /\B@([\-\d\w]*)$/,
			                      search: function ( term, callback ) {
			                          var mentions = bp.mentions.users;
			                          callback( $.map(mentions, function ( mention ) {
			                          return mention.ID.indexOf( term ) === 0 || mention.name.indexOf( term ) === 0 ? mention : null;
			                      }));
			                      },
			                      template: function ( mention ) {
			                          return '<img src="' + mention.image + '" /><span class="username">@' + mention.ID + '</span><small>' +mention.name+ '</small>';
			                      },
			                      replace: function ( mention ) {
			                          return '@' + mention.ID + '&nbsp;';
			                      },
			                      cache: true,
			                      index: 1
			                   }]);
			                }     
			              }
            			} );
						// $.yz_init_wall_textarea_emojionearea( $form.find( '.yz-wall-textarea' ) );
						// $form.find( '.emojionearea-editor' ).html( response.content );
					}
			    } else {
			    	$form.find( '.yz-load-emojis' ).remove();
			    }

			  //   // Activate Emojis in Posts.
			  //   if ( typeof Yz_Emoji !== 'undefined' ) {

				 //    if (  
				 //    	 ( activity_type == 'activity_comment' && Yz_Emoji.comments_visibility == 'on' )
				 //    	||
				 //    	( activity_type != 'activity_comment' && Yz_Emoji.posts_visibility == 'on' ) 
				 //    	) {
					// 	$.yz_init_wall_textarea_emojionearea( $form.find( '.yz-wall-textarea' ) );
					// 	$form.find( '.emojionearea-editor' ).html( response.content );
					// }
			  //   }

				// Update Form Submit Button.
				$form.find( '.yz-wall-post' ).attr( {
					'class': 'yz-update-post',
					'data-activity-type': activity_type,
					'data-activity-id': activity_id,
				} ).text( Youzer.save_changes );

				// Set Field Values.
				$.each( response.meta, function( field_name, field_value ) {
					// Set Input Textarea Fields.					
					$form.find( '.yz-wall-cf-input[name="' + field_name + '"]' ).val( field_value );
				});

				// Add Attachment.
				if ( response.attachments == 'hide' ) {
					$form.find( '.yz-wall-upload-btn' ).remove();
				} else {
					$form.find( '.yz-form-attachments' ).html( response.attachments );
				}

				if ( response.url_preview ) { 
					var url_preview = $form.find( '.yz-lp-prepost' );
					url_preview.attr( 'data-loaded', true );
					$.yz_set_live_preview_form( url_preview, response.url_preview );
				}


	    		$form.css( { 'position': 'absolute', 'top': $( document ).scrollTop() + 100 } );
	    		
	    		if ( ! $( '.yz-modal-overlay')[0] ) {	
	    			$( 'body' ).append( '<div class="yz-modal-overlay"></div>' );
	    		} else {
	    			$( '.yz-modal-overlay' ).fadeIn();
	    		}


			}).fail( function( xhr, textStatus, errorThrown ) {

				// Remove Loading Class.
	    		$link.removeClass( 'loading' );

            	// Show Error Message
            	$.yz_DialogMsg( 'error', Youzer.unknown_error );

				return false;

    		});


		});

		/**
		 * Save Edit Form.
		 **/
		$( document ).on( 'click', '.yz-update-post', function( e ) {
			
			e.preventDefault();

			var activity_type = $( this ).attr( 'data-activity-type' );
				var last_date_recorded = 0,
					button = $( this ),
					button_title = $( this ).text(),
					$form   = $( this ).closest( '#yz-wall-form' ),
					target = current_edit_button.parent().hasClass( 'activity-meta' ) || current_edit_button.parent().hasClass( 'yz-activity-tools' )  ? 'activity' : 'comment',
					inputs = {}, post_data, object;

			if ( button.hasClass( 'loading' ) ) {
				return;
			}

			// Add Loading Class.
			button.addClass( 'loading' ); 
			button.html( '<i class="fas fa-spinner fa-spin"></i>' );


				// $form = $( this ).parent( '#yz-wall-form' );

				jQuery.each( $form.serializeArray(), function( key, input ) {
					// Only include public extra data
					if ( '_' !== input.name.substr( 0, 1 ) && 'whats-new' !== input.name.substr( 0, 9 ) ) {
						//attachments_files[]
						if ( ! inputs[ input.name ] ) {
							inputs[ input.name ] = input.value;
						} else {
							// Checkboxes/dropdown list can have multiple selected value
							if ( ! jQuery.isArray( inputs[ input.name ] ) ) {
								inputs[ input.name ] = new Array( inputs[ input.name ], input.value );
							} else {
								inputs[ input.name ].push( input.value );
							}
						}
					}
				} );
			
				var data = $.extend( {
					'action': 'yz_save_activity_edit_form',
					'yz_edit_activity_nonce': $form.find('input[name="yz_edit_activity_nonce"]').val(),
					'activity_id': $( this ).data( 'activity-id' ),
					'content': $form.find( '.yz-wall-textarea' ).val(),
					'target': target
					}, inputs );


			$.ajax({
				type: 'POST',
				url : ajaxurl,
				data: data,
				success: function( response ) {

					if ( $.yz_isJSON( response ) ) {

		            	var res = $.parseJSON( response );

			            if ( res.error ) {

							// Add Loading Class.
							button.removeClass( 'loading' ); 
							button.text( button_title );
							
							if ( res.remove_modal ) {
								// Remove Modal.
				    			$.yzea_hide_modal( button );
				    			// Remove Edit Button.
				    			current_edit_button.remove();
							}

							$.yz_DialogMsg( 'error', res.error );

							return;
						}
				
					}

				// console.log ( res );
	    			// Update Comment.
					if ( target == 'comment' && res.content ) {
						// Display Live Edit.
						$( 'li#acomment-' + data.activity_id ).find( '.acomment-content' ).first().html( '<p>' + res.content + '</p>' ).show();
					} 

	    			// Update Post.
					if ( target == 'activity' ) {

						$( 'li#activity-' + data.activity_id ).replaceWith( response );
						
						// Init Slideshow.
						if ( $( 'li#activity-' + data.activity_id ).hasClass( 'activity_slideshow' ) ) {
							$.youzer_sliders_init();
						}

					}

					// Remove Modal.
	    			$.yzea_hide_modal( button );

				}
			});

		});

		/**
		 * Delete Attachment.
		 */
		$( document ).on( 'click', '#yz-edit-activity-form .yz-delete-attachment', function( e ) {
			// Get Item ID.
			var item_id = $( this ).closest( '.yz-attachment-item' ).attr( 'data-item-id' );
			if ( item_id ) {
				$( document ).find( '#yz-edit-activity-form .yz-wall-attachments' ).append( '<input type="hidden" name="delete_attachments[]" value="' + item_id + '" >' );
			}
		});

		/**
		 * Remove Modal
		 */
		$.yzea_hide_modal = function( button ) {
			$( '.yz-modal-overlay' ).fadeOut( 300, function( ) {
	    		button.closest( '.yz-modal' ).fadeOut( 300, function() {
	    			$( this ).remove();
	    		});
			});
		}

	});


})( jQuery );