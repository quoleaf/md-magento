jQuery(function($){jQuery('.checkout-cart-index #s_method_owebiashipping3_international').parent().after('<small>IMPORTANT NOTE: "La Canada Venture reserve right to adjust international shipping rate for certain countries with high shipping rates"</small>');jQuery('.opc-index-index label[for="s_method_owebiashipping3_international"]').after('<small style="display:block">IMPORTANT NOTE: "La Canada Venture reserve right to adjust international shipping rate for certain countries with high shipping rates"</small>');jQuery('input[name="email"]').on('change',function(e){var email=jQuery('input[name="email"]').val();});jQuery('input[name="first_name"]').on('change',function(e){var name=jQuery('input[name="first_name"]').val();});function validateEmail(email){var re=/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;return re.test(email);}
jQuery('.newsletter-submit').on('click',function(e){if(jQuery('.newsletter-name').val()==""||jQuery('.newsletter-email').val()==""){alert('Please Input Email and Name!');return false;}
if(!validateEmail(jQuery('.newsletter-email').val())){alert('Invalid Email!');return false;}
jQuery('#loader').removeAttr('style');});$("body.opc-index-index .checkout-selector .members").on("click",function(){$('body.opc-index-index .checkout-selector .members input').prop('checked',true);$('body.opc-index-index .opc-wrapper-opc .opc-col-center #login_guest').addClass('hide');$('body.opc-index-index .opc-wrapper-opc .opc-col-center #login_email').removeClass('hide');$('body.opc-index-index .opc-wrapper-opc .checkout-guests #opc-address-form-billing #register_details').hide();});$("body.opc-index-index .checkout-selector .guests").on("click",function(){$('body.opc-index-index .checkout-selector .guests input').prop('checked',true);$('body.opc-index-index .opc-wrapper-opc .opc-col-center #login_guest').removeClass('hide');$('body.opc-index-index .opc-wrapper-opc .opc-col-center #login_email').addClass('hide');});if($('.opc-index-index #register_details input[name="billing[firstname]"]').val()==" "){$('.opc-index-index #register_details input[name="billing[firstname]"]').val('');}
if($('.opc-index-index #register_details input[name="billing[lastname]"]').val()==" "){$('.opc-index-index #register_details input[name="billing[lastname]"]').val('');}
var email=localStorage.getItem("email");$('.OPCregister-email').val(email);$('.opc-index-index .account-login input[name="login[password]"]').on('change',function(){var pass=$(this).val();$('.opc-index-index .OPCregister-pass').val(pass);});$('body.opc-index-index .opc-wrapper-opc .opc-col-center fieldset .form-group input[name="guestlogin[username]"]').on('change',function(){var pass=$(this).val();$('body.opc-index-index .opc-wrapper-opc .opc-col-center fieldset#register_details ul li input[name="billing[email]"]').val(pass);});var subdomain = window.location.pathname; subdomain.indexOf(1); subdomain.toLowerCase(); subdomain = subdomain.split("/")[1]; console.log(subdomain); if(subdomain=="au"){jQuery('.MDCurrencyWrapper .selectedCurrency').removeClass('USD');jQuery('.MDCurrencyWrapper .selectedCurrency').addClass('AUD');}
else if(subdomain=="ca"){jQuery('.MDCurrencyWrapper .selectedCurrency').removeClass('USD');jQuery('.MDCurrencyWrapper .selectedCurrency').addClass('CAD');jQuery('.opc-index-index').addClass('.not-ch');}
else if(subdomain=="ch"){jQuery('.MDCurrencyWrapper .selectedCurrency').removeClass('USD');jQuery('.MDCurrencyWrapper .selectedCurrency').addClass('CNY');jQuery('.opc-index-index').addClass('.ch-dinpay');}
else if(subdomain=="mx"){jQuery('.MDCurrencyWrapper .selectedCurrency').removeClass('USD');jQuery('.MDCurrencyWrapper .selectedCurrency').addClass('MXN');jQuery('.opc-index-index').addClass('.not-ch');}
else if(subdomain=="br"){jQuery('.MDCurrencyWrapper .selectedCurrency').removeClass('USD');jQuery('.MDCurrencyWrapper .selectedCurrency').addClass('BRL');jQuery('.opc-index-index').addClass('.not-ch');}
else if(subdomain=="us"){jQuery('.MDCurrencyWrapper .selectedCurrency').removeClass('USD');jQuery('.MDCurrencyWrapper .selectedCurrency').addClass('USD');jQuery('.opc-index-index').addClass('.not-ch');}
else{$('.MDCurrencyWrapper .selectedCurrency').addClass('USD');}
$("#topNavi .search,#searchOverlay .closeBtn").on("click",function(e){$("#searchOverlay").slideToggle();e.preventDefault();});$("#topNavi .navi>li>a").on("mouseover",function(e){$(this).parent().find(".sub").stop().fadeIn();});$("#topNavi .navi>li").on("mouseleave",function(e){$(this).find(".sub").stop().fadeOut();});$("#topNavi .navi .inner").on("mouseleave",function(e){$(this).parent().stop().fadeOut();});$("#topNavi .navi li .sub .types>li ul li").on("mouseover",function(e){$(this).find("ul").stop().fadeIn();});$("#topNavi .navi li .sub .types>li ul li").on("mouseleave",function(e){$(this).find("ul").stop().fadeOut();});$("#topNavi .navi li .sub .shopBy a").on("click",function(e){$(this).toggleClass("expanded");$(this).parent().next().stop().slideToggle();e.preventDefault();});$("#mobileMenu, #mobileNavi .closeBtn").on("click",function(e){$("body").toggleClass("showMobileMenu");$("body.tabletMode #mobileNavi").fadeToggle();e.preventDefault();});$("#mobileNavi .links li.hasSub>a").on("click",function(e){$(this).parent().toggleClass("opened");if($(this).parent().parent().find("li").hasClass("opened")){$(this).parent().parent().addClass("expanded");}else{$(this).parent().parent().removeClass("expanded");}
$(this).next().stop().slideToggle();e.preventDefault();});function checkViewportWidth(){var viewportWidth=$("body").width();if(viewportWidth<1234){$("body").removeClass("desktopMode");$("body").addClass("tabletMode");}else{$("body").removeClass("tabletMode");$("body").addClass("desktopMode");}
if($("#mobileNavi").is(":visible")){$("body").addClass("showMobileMenu");}else{$("body").removeClass("showMobileMenu");}
if(viewportWidth<975){$("#sidebar .sideNavi .title").off();$("#sidebar .sideNavi .title").on("click",function(e){$(this).next().slideToggle();$(this).toggleClass("opened");e.preventDefault();})
$("#sidebar .sideNavi .title").each(function(){if($(this).hasClass("opened")){$(this).next().show();}else{$(this).next().hide();}});}else{$("#sidebar .sideNavi>ul").show();$("#sidebar .sideNavi .title").off();}}
checkViewportWidth();$(window).scroll(function(){if($(this).scrollTop()>0){$("body").addClass("scrolling");}else{$("body").removeClass("scrolling");}
var scrollTop=$(window).scrollTop();var makeStickyAfter=27;if(scrollTop<=makeStickyAfter){if(!$("body").hasClass("withTopbar")){$("#topNavi").css("top",-scrollTop);}
$("body").removeClass("stickyNavi");}else{if(!$("body").hasClass("withTopbar")){$("#topNavi").css("top",-makeStickyAfter);}
$("body").addClass("stickyNavi");}
$(".parralax").each(function(){var divam=10;var backgroundAttachment=$(this).css("background-attachment");if(backgroundAttachment=="fixed"&&$("body").width()>=1234){$(this).css({"background-position":"center -"+scrollTop/divam+"px"});}});});$(window).scroll();$(window).resize(function(){checkViewportWidth();});new WOW({boxClass:"animated"}).init();$("#sidebar .sideNavi ul li a.main").on("mouseover",function(e){$(this).next().show(function(){$(this).parent().toggleClass("active");});});$("#sidebar .sideNavi ul li").on("mouseleave",function(e){$(this).find("ul").hide("300")});$("#services .listing").owlCarousel({loop:false,autoplay:false,items:3,dots:false,responsiveRefreshRate:10,responsive:{0:{items:2,margin:6,pullDrag:true,touchDrag:true,nav:true},768:{items:3,margin:15,pullDrag:false,touchDrag:false,nav:false}}});$("#blogSlider .slides").owlCarousel({loop:true,autoplay:true,autoplayHoverPause:true,items:3,margin:18,responsiveRefreshRate:10,responsive:{0:{items:1,nav:true,dots:false,},768:{items:2,margin:16,nav:false,dots:true},992:{items:3,margin:18,}}});var pressSlider=$("#pressMedia .press .slider>ul").owlCarousel({loop:false,autoplay:false,items:1,margin:17,dots:true,nav:true,autoHeight:true,responsiveRefreshRate:10});var mediaSlider=$("#pressMedia .media .slider>ul").owlCarousel({loop:false,autoplay:false,items:1,margin:17,dots:true,nav:true,autoHeight:true,responsiveRefreshRate:10});$("#getSocials.slider .images").owlCarousel({loop:false,autoplay:false,items:5,margin:22,dots:false,responsiveRefreshRate:10,responsive:{0:{items:2,nav:true,},768:{items:3,margin:20,nav:true,},992:{items:5,margin:22,nav:false,pullDrag:false,touchDrag:false,}}});$("#pressMedia .tabs li a").on("click",function(e){$(this).parents(".tabs").find("li.active").removeClass("active");$(this).parent().addClass("active");var tabIndex=$(this).parent().index();var contents=$(this).parents(".section").find(".contents");contents.removeClass("active");contents.eq(tabIndex).addClass("active");e.preventDefault();});$("#pressMedia .tabs li.active").each(function(){var tabIndex=$(this).index();var contents=$(this).parents(".section").find(".contents");contents.eq(tabIndex).addClass("active");});ssm.addStates([{query:'(max-width: 767px)',onEnter:function(){showPerPage(pressSlider,$("#pressMedia .press ul.listing>li"),8);showPerPage(mediaSlider,$("#pressMedia .media ul.listing>li"),2);}},{query:'(min-width: 768px) and (max-width: 991px)',onEnter:function(){showPerPage(pressSlider,$("#pressMedia .press ul.listing>li"),12);showPerPage(mediaSlider,$("#pressMedia .media ul.listing>li"),3);}},{query:'(min-width: 992px)',onEnter:function(){showPerPage(pressSlider,$("#pressMedia .press ul.listing>li"),15);showPerPage(mediaSlider,$("#pressMedia .media ul.listing>li"),6);}}]);function showPerPage(slider,listItems,perPage){var items=$(listItems).clone();var itemContainer=$("<div></div>");var totalItems=items.length;var counter=0;var perPage=perPage;$(itemContainer).append("<li><ul></ul></li>");for(a=1;a<=totalItems;a++){if(counter==perPage){$(itemContainer).append("<li><ul></ul></li>");counter=0;}
$(itemContainer).find("ul:last").append($(items)[a-1])
counter++;}
slider.trigger("replace.owl.carousel",[itemContainer.html()]).trigger("refresh.owl.carousel");}
$.fn.matchHeight._maintainScroll=true;$('.equalHeight').matchHeight({byRow:true});$(window).load(function(){$("#tryIt .beforeAfter .slider .images").twentytwenty({default_offset_pct:0.8});jQuery("#product .left .thumbnails ul li").on("click",function(e){var fileName=$(this).find(".pic img").prop("src");jQuery(this).parents(".left").find(".bigImage .pic img").prop("src",fileName);e.preventDefault();});$("#product .right .tabs .options li").on("click",function(e){var tabIndex=$(this).index();$("#product .right .tabs .options li").removeClass("active");$(this).addClass("active");$("#product .right .tabs .tabInfo").hide();$(this).parents(".tabs").find(".tabInfo").eq(tabIndex).fadeIn();e.preventDefault();});$("#product .right .tabs .options li.active").click();$(".zoomEffect").on('mousemove',function(e){$(this).find(".pic").css({'transform-origin':((e.pageX-$(this).offset().left)/ $(this).width())*100+'% '+((e.pageY-$(this).offset().top)/ $(this).height())*100+'%'});})});});