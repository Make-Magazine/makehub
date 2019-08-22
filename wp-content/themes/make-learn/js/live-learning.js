jQuery(document).ready(function(){
	//Replace live-learning panel with most recent live learning data
	if(jQuery('.three-column-cards.live-learning').length != 0) {
		var upcoming = [];
		var mostRecent = [];
		jQuery.get( "https://make.co/wp-json/live-learning-api/v1/events/4", function( data ) {
		   upcoming = data.slice(1, 4);
			mostRecent = data[0];
         
			// Build the upcoming section and append it to any livelearning panels
			jQuery('.three-column-cards.live-learning').prepend("<a class='center-column'><div class='image-crop'><img src='' /></div><div class='column-header'>Coming up next</div><div class='column-time'></div><div class='column-title'></div><div class='column-name'></div></a>")
			jQuery(".live-learning .center-column").attr("href", mostRecent.sign_up_link);
			jQuery(".live-learning .center-column img").attr("src", mostRecent.main_image);
			jQuery(".live-learning .center-column .column-time").text(mostRecent.event_date + " " + mostRecent.event_time + " PST");
			jQuery(".live-learning .center-column .column-title").text(mostRecent.title);
			jQuery(".live-learning .center-column .column-name").text("by " + mostRecent.host);
			
			// Fill in information for the past events
			jQuery('.three-column-cards.live-learning .make-card').each(function(i, obj) {
				if( upcoming[i].video_link.length != 0) {
					jQuery(this).attr("href", upcoming[i].video_link);
				}else{
					jQuery(this).attr("href", "https://make.co/live");
				}
				jQuery(this).children(".image-crop").children("img").attr("src", upcoming[i].main_image);
				jQuery(this).children(".column-info").children(".column-title").text(upcoming[i].title);
				jQuery(this).children(".column-info").children(".column-name").text(upcoming[i].host);
			});
		});
	}
	
	// the workshop form isn't very dynamic right meow
	jQuery( "#workshop-button" ).on('click',function(event) {
		if(isValidEmailAddress(jQuery("#workshop-email").val())) {
			jQuery("#workshop-form .submit-message").text("Thanks for signing up!");
			jQuery("#workshop-form .submit-message").removeClass("hidden");
			jQuery.post("https://secure.whatcounts.com/bin/listctrl", jQuery('#workshop-form').serialize());
			jQuery("#workshop-form #workshop-email").css("border-color", "#6D6D6D");
			jQuery("#workshop-form .submit-message").css("color", "#fff");
			jQuery("#workshop-form #workshop-email").val("");
			event.preventDefault();
		} else {
			jQuery("#workshop-form #workshop-email").css("border-color", "#EA002A");
			jQuery("#workshop-form .submit-message").css("color", "#EA002A");
			jQuery("#workshop-form .submit-message").text("Please enter a valid email");
			jQuery("#workshop-form .submit-message").removeClass("hidden");
			event.preventDefault();
		}
	});
	
});

