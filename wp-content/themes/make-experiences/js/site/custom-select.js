// For the stylish select bar that has everything
if ( jQuery( "#vimeography-galleries" ).length ) {
	var selElement = jQuery(".select select")[0];
	var divCopy = document.createElement("div");
	divCopy.setAttribute("class", "select-selected");
	divCopy.innerHTML = selElement.options[selElement.selectedIndex].innerHTML;
	jQuery(".select")[0].append(divCopy);
	var divList = document.createElement("div");
	divList.setAttribute("class", "select-items select-hide");
	var optionItem;
	jQuery("#vimeography-galleries select option").each( function() {
		optionItem = document.createElement("div");
		optionItem.innerHTML = this.innerHTML;
		optionItem.id = this.value;
		optionItem.addEventListener("click", function(e){
			jQuery("#vimeography-galleries select").val( this.id );
			jQuery("#vimeography-galleries select").change();
		});
		divList.append(optionItem);
	});
	jQuery(".select .select-selected").append(divList);
	jQuery(".select .select-selected").click(function(e) {
		e.stopPropagation();
		closeAllSelect(this);
		jQuery(".select-items").toggleClass("select-hide");
		jQuery(this).toggleClass("select-arrow-active");
	});
	
	function closeAllSelect(elmnt) {
	  var x, y, i, arrNo = [];
	  x = document.getElementsByClassName("select-items");
	  y = document.getElementsByClassName("select-selected");
	  for (i = 0; i < y.length; i++) {
		 if (elmnt == y[i]) {
			arrNo.push(i)
		 } else {
			y[i].classList.remove("select-arrow-active");
		 }
	  }
	  for (i = 0; i < x.length; i++) {
		 if (arrNo.indexOf(i)) {
			x[i].classList.add("select-hide");
		 }
	  }
	}
	document.addEventListener("click", closeAllSelect);
}