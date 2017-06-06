jQuery(window).ready(function() { 

    function hideButtons(){jQuery('.add-to-bag, .cta a, .custom-container div a, .main-nav li a').attr('href', '#popupTest').attr('data-lity','');}
    function showButtons(){jQuery('.add-to-bag, .cta a').attr('href', '<?php echo $cartUrl; ?>');}
    function hideTick(){jQuery('#bundleWrapper .custom-container .content .autoshipWrapper .inner .custom-checkbox label').hide();}

    jQuery.getJSON('//ipinfo.io', function (location) {
        // console.log(location.country);
       if(location.country!='US') {
            hideButtons();
            hideTick();
        }
    });
});