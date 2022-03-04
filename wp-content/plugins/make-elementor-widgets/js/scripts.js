jQuery(document).ready(function(){
	jQuery(".make-elementor-expando-box h4").click(function(){
		jQuery(this).toggleClass( "closed" );
		jQuery(this).next().toggleClass( "closed" );
	});
});
