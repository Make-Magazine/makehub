window.onload = (event) => {
	if(document.body.classList.contains("single-gift-guides")) {
		var imageView = document.getElementById("gg-gallery-viewer-img");
		var imageView = document.getElementById("gg-gallery-viewer-img");
		jQuery(".gg-gallery-thumbnail").on("click", function(){
			jQuery("#gg-gallery-viewer-img").attr('src', jQuery(this).attr('data-src'));
			jQuery("#gg-gallery-viewer-img").removeClass("gg-gallery-off");
			jQuery("#gg-video-viewer").addClass("gg-gallery-off");
		});
		jQuery(".gg-video-thumbnail").on("click", function(){
			jQuery("#gg-gallery-viewer-img").addClass("gg-gallery-off");
			jQuery("#gg-video-viewer").removeClass("gg-gallery-off");
		});
	}
	if(document.body.classList.contains("page-gift-guide")) {
		var moreFilters = false;
		jQuery(".sf-field-posts_per_page").on("click", function(event){
			if(moreFilters == false) {
				document.getElementsByClassName("sf-field-taxonomy-gift_guide_categories")[0].style.maxHeight = "100%";
				document.getElementsByClassName("sf-field-taxonomy-audiences")[0].style.display = "block";
				jQuery(".sf-field-posts_per_page h4").text("Less Filters");
				moreFilters = true;
			} else {
				document.getElementsByClassName("sf-field-taxonomy-gift_guide_categories")[0].style.maxHeight = "210px";
				document.getElementsByClassName("sf-field-taxonomy-audiences")[0].style.display = "none";
				jQuery(".sf-field-posts_per_page h4").text("More Filters");
				moreFilters = false;
			}
		});
	}
}


function galleryZoomIn(event) {
	var zoomBox = document.getElementById("gg-gallery-overlay");
	zoomBox.style.display = "inline-block";
	var img = document.getElementById("gg-gallery-viewer-img");
	var posX = event.offsetX ? (event.offsetX) : event.pageX - img.offsetLeft;
	var posY = event.offsetY ? (event.offsetY) : event.pageY - img.offsetTop;
	zoomBox.style.backgroundPosition = (-posX + (img.clientWidth/8)) + "px " + (-posY + (img.clientHeight/8)) + "px";
	zoomBox.style.backgroundImage = "url("+img.src+")";
}

function galleryZoomOut() {
	var element = document.getElementById("gg-gallery-overlay");
	element.style.display = "none";
}
