jQuery(document).ready(function ($) {
    jQuery(document).on("change", "li.preview_image div.ginput_container input[type=file]", function () {
        readURL(this);
    });

    function readURL(input) {
        var inputID = jQuery(input).attr('id');
        if (input.files && input.files[0]) {			
            var reader = new FileReader();
            reader.onload = function (e) {         
                jQuery("#preview_"+inputID).remove();
                jQuery(input).after('<div id="preview_'+inputID+'"><div class="preview_img-wrapper"></div></div>');
                jQuery('#preview_'+inputID+' .preview_img-wrapper').css( 'background-image', 'url(' + e.target.result + ')' );
            }
            reader.readAsDataURL(input.files[0]);
        }
    }	
	
});

jQuery(document).on("mouseenter touchstart",".timepicker",function(e){
	if(jQuery(this).val()) {
		var time = jQuery(this).val().replace(/\s+/g, '');
		var timeAdd = 0;
		if(time.indexOf("PM") !== -1) {
			timeAdd = 12;
		}
		time = time.slice(0, -2).split(":");
		if(time[0] != 12) {
			time[0] = parseInt(time[0]) + timeAdd;
		}
		time = time.join(":");
	} else {
		var time = "12:00";
	}
	
	var options = {
		minutesInterval: 15,
		now: time, 
		upArrow: 'wickedpicker__controls__control-up', 
		downArrow: 'wickedpicker__controls__control-down',
		close: 'wickedpicker__close',
		hoverState: 'hover-state',
	}
	jQuery(this).wickedpicker(options);	
});


function numbersAndDashes() {
    var e = event || window.event;  // get event object
    var key = e.keyCode || e.which; // get key cross-browser
	var charCode = (e.which) ? e.which : e.keyCode;
    if (charCode != 46 && charCode != 45 && charCode > 31  && (charCode < 48 || charCode > 57)) {
        //Prevent default action, which is inserting character
        if (e.preventDefault) e.preventDefault(); //normal browsers
        e.returnValue = false; //IE
    }
}

