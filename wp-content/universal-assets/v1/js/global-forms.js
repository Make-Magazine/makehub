// add the wickedpicker functionality to all inputs with the class timepicker
jQuery(document).on("mouseenter touchstart",".timepicker",function(e){
	var time = "12:00";
	if(jQuery(this).val()) {
		time = jQuery(this).val().replace(/\s+/g, '');
		var timeAdd = 0;
		if(time.indexOf("PM") !== -1) {
			timeAdd = 12;
		}
		time = time.slice(0, -2).split(":");
		if(time[0] != 12) {
			time[0] = parseInt(time[0]) + timeAdd;
		}
		time = time.join(":");
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
