jQuery(document).ready(function($) {

    $('.js-lds-show-topics').click(function(e) {
        e.preventDefault();
        $(this).parents().find('.u-lds-hide').slideToggle('slow');
        $(this).find('i.fa').toggleClass('fa-chevron-circle-down').toggleClass('fa-chevron-circle-up');
    });

});
