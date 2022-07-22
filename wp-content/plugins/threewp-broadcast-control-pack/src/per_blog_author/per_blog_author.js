jQuery(document).ready(function($) {
		window.broadcast_per_blog_author = {
			$pba : null,

			'move_author' : function()
			{
				var $author_selects = $( 'select.author_selector' );
				if ( $author_selects.length < 1 )
					return;
				console.log( $author_selects.length, 'found!' );
				var $meta_box = $( '#threewp_broadcast.postbox' );
				var $blogs = $( '.blogs .checkboxes', $meta_box );
				$.each( $author_selects, function( index, item )
				{
					var $item = $( item );
					var blog_id = $item.data( 'blog_id' );

					$item = $item.parent();
					$item = $item.parent();
					$item = $item.parent();

					var $target = $( 'div.blog.' + blog_id, $blogs );
					$item.insertAfter( $target );

					// And hide / show the container depending on the state of the blog checkbox.
					$( 'input.checkbox', $target ).on( 'change', function()
					{
						var $this = $( this );
						var checked = $this.prop( 'checked' );
						if ( checked )
							$item.show();
						else
							$item.hide();
					}).trigger( 'change' );
				});
			},

			'init' : function()
			{
				window.broadcast_per_blog_author.move_author();
			}

		};
		window.broadcast_per_blog_author.init();
});
