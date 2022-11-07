(function ($) {
	$(document).ready(function () {

		/*
		* On ADD GP FEED SCREEN / click on Upgrade Member checkbox show confirmation checkbox
		*/
		$(".gaddon-setting-checkbox #gp_checkupgrademember").click(function(){
			if ($("#gp_checkupgrademember").prop('checked')) {
				$("table.gforms_form_settings #gaddon-setting-row-gp_checkupgrademember_confirm").show(500);
			}else{
				// hide "confirm upgrade member" checkbox
				$("#gaddon-setting-row-gp_checkupgrademember_confirm").hide(500);
			}
		})

		/*
		* On ADD GP FEED SCREEN / click on Select Price Field checkbox to show price field select dropdown
		*/
		$(".gaddon-setting-checkbox #gp_selectdifferentpricefield").click(function(){
			if ($("#gp_selectdifferentpricefield").prop('checked')) {
				$("table.gforms_form_settings #gaddon-setting-row-gp_selectpricefield").show(500);
			}else{
				// hide "Price Field" checkbox
				$("#gaddon-setting-row-gp_selectpricefield").hide(500);
			}
		})

		/*
         * On ADD GP FEED SCREEN / click on "Sub-Accounts Quantity Field" checkbox show mapping fields
         */
        $(".gaddon-setting-checkbox #gp_enablesubaccount").click(function() {
            if ($("#gp_enablesubaccount").prop('checked')) {
                $("table.gforms_form_settings #gaddon-setting-row-gp_assignmax_subaccounts").show(500);
            } else {
                // hide "confirm upgrade member" checkbox
                $("#gaddon-setting-row-gp_assignmax_subaccounts").hide(500);
                $("#gaddon-setting-row-corporate_quantity_field").hide(500);
                $("#gp_assignmax_subaccounts").prop('checked', false);
            }
		})
		
		/*
         * On ADD GP FEED SCREEN / click on "Sub-Accounts Quantity Field" checkbox show mapping fields
         */
        $(".gaddon-setting-checkbox #gp_assignmax_subaccounts").click(function() {
            if ($("#gp_assignmax_subaccounts").prop('checked')) {
                $("table.gforms_form_settings #gaddon-setting-row-corporate_quantity_field").show(500);
            } else {
                // hide "confirm upgrade member" checkbox
                $("#gaddon-setting-row-corporate_quantity_field").hide(500);
            }
		})
		
		$(".gaddon-setting-checkbox #gp_enablecoupons").click(function() {
            if ($("#gp_enablecoupons").prop('checked')) {
                $("table.gforms_form_settings #gaddon-setting-row-gp_all_coupons").show(500);
            } else {
                // hide coupons dropdown
                $("#gaddon-setting-row-gp_all_coupons").hide(500);
            }
		})

		/**
		 * Enabling Offline method should select the Pending status in Membership status options
		 */
		$(".gaddon-setting-checkbox #gp_enable_offline_method").click(function() {
			if ($("#gp_enable_offline_method").prop('checked')) {
				$("#membershipstatus").val('$pending_str');
            } else {
				$("#membershipstatus").val('$complete_str');
            }
		})

	});
})(jQuery);
