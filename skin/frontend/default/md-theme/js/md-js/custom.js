$(document).ready(function(){
	
	/* Search Overlay */
	
	$("#topNavi .search,#searchOverlay .closeBtn").on("click", function(e){
		$("#searchOverlay").slideToggle();
		e.preventDefault();
	});
	
	
	/* Dropdown Menu */

	$("#topNavi .navi>li>a").on("mouseover", function(e){
		$(this).parent().find(".sub").stop().fadeIn();
	});

	$("#topNavi .navi>li").on("mouseleave", function(e){
		$(this).find(".sub").stop().fadeOut();
	});
		
	$("#topNavi .navi .inner").on("mouseleave", function(e){
		$(this).parent().stop().fadeOut();
	});
	
	$("#topNavi .navi li .sub .types>li ul li").on("mouseover", function(e){
		$(this).find("ul").stop().fadeIn();
	});
		
	$("#topNavi .navi li .sub .types>li ul li").on("mouseleave", function(e){
		$(this).find("ul").stop().fadeOut();
	});
	
	$("#topNavi .navi li .sub .shopBy a").on("click", function(e){
		$(this).toggleClass("expanded");
		$(this).parent().next().stop().slideToggle();
		e.preventDefault();
	});

	/* Mobile Navi */
	
	$("#mobileMenu, #mobileNavi .closeBtn").on("click", function(e){
		$("body").toggleClass("showMobileMenu");
		$("body.tabletMode #mobileNavi").fadeToggle();
		e.preventDefault();
	});
	
	$("#mobileNavi .links li.hasSub>a").on("click", function(e){

		$(this).parent().toggleClass("opened");
		
		if($(this).parent().parent().find("li").hasClass("opened")) {
			$(this).parent().parent().addClass("expanded");
		} else {
			$(this).parent().parent().removeClass("expanded");
		}
		
		$(this).next().stop().slideToggle();
					
		e.preventDefault();
	});	
	
	/* Check Viewport */
	
	function checkViewportWidth(){
		var viewportWidth = $("body").width();
		
		if(viewportWidth < 1234) {
			$("body").removeClass("desktopMode");
			$("body").addClass("tabletMode");
		} else {
			$("body").removeClass("tabletMode");
			$("body").addClass("desktopMode");
		}
		
		if($("#mobileNavi").is(":visible")){
			$("body").addClass("showMobileMenu");
		} else {
			$("body").removeClass("showMobileMenu");
		}
		
		/* Side Navi */
		
		if(viewportWidth < 975) {
			$("#sidebar .sideNavi .title").off();
			
			$("#sidebar .sideNavi .title").on("click", function(e){
				$(this).next().slideToggle();
				$(this).toggleClass("opened");
				e.preventDefault();
			})	
			
			$("#sidebar .sideNavi .title").each(function(){
				if ($(this).hasClass("opened")){
					$(this).next().show();
				} else {
					$(this).next().hide();
				}
			});

		} else {
			$("#sidebar .sideNavi>ul").show();
			$("#sidebar .sideNavi .title").off();			
		}
		
	}
	
	checkViewportWidth();
	
	/* Page Scrolling Functions */
	
	$(window).scroll(function(){
		if ($(this).scrollTop() > 0){
			$("body").addClass("scrolling");
		} else {
			$("body").removeClass("scrolling");
		}

		var scrollTop  = $(window).scrollTop();
		var makeStickyAfter = 27;

		if (scrollTop <= makeStickyAfter) {
			if (!$("body").hasClass("withTopbar")) {
				$("#topNavi").css("top", -scrollTop);	
			}			
			$("body").removeClass("stickyNavi");
		} else {
			if (!$("body").hasClass("withTopbar")) {
				$("#topNavi").css("top", -makeStickyAfter);
			}
			$("body").addClass("stickyNavi");
		}
		
		/* Parralax Effect */

		$(".parralax").each(function(){
			  var divam = 10;
			  var backgroundAttachment = $(this).css("background-attachment");
	
	
			if(backgroundAttachment=="fixed" && $("body").width()>=1234) {
				$(this).css({
					"background-position":"center -"+scrollTop/divam+"px"     
				});	
			}
		});
		

	});
	
	$(window).scroll();
	
	
	/* Window Resizing */
	
	$(window).resize( function() {
		checkViewportWidth();
	});
	
	/* Reveal Animation */
	
	new WOW({boxClass: "animated"}).init();
	
	/* Side Navi */
	
	$("#sidebar .sideNavi ul li a").on("click", function(e){
		$(this).next().slideToggle(function(){
			$(this).parent().toggleClass("active");	
		});
		e.preventDefault();
	});
		
	/* Services Slider */
	
	$("#services .listing").owlCarousel({
		loop: false,
		autoplay: false,
		items: 3,
		dots: false,
		responsiveRefreshRate: 10,
		responsive: {
			0: {
				items: 2,
				margin: 6,
				pullDrag: true,
				touchDrag: true,
				nav: true
			},
			
			768: {
				items: 3,
				margin: 15,
				pullDrag: false,
				touchDrag: false,
				nav: false
			}
		}
	});
	
	/* Blog Slider */
	
	$("#blogSlider .slides").owlCarousel({
		loop: true,
		autoplay: true,
		autoplayHoverPause: true,
		items: 3,
		margin: 18,
		responsiveRefreshRate: 10,
		responsive: {
			0: {
				items: 1,
				nav: true,
				dots: false,
			},
			
			768: {
				items: 2,
				margin: 16,
				nav: false,
				dots: true
			},
			
			992: {
				items: 3,
				margin: 18,
			}
		}
	});
	
	/* Press Slider */
	
	var pressSlider = $("#pressMedia .press .slider>ul").owlCarousel({
		loop: false,
		autoplay: false,
		items: 1,
		margin: 17,
		dots: true,
		nav: true,
		autoHeight: true,
		responsiveRefreshRate: 10
	});
	
	/* Media Slider */
	
	var mediaSlider = $("#pressMedia .media .slider>ul").owlCarousel({
		loop: false,
		autoplay: false,
		items: 1,
		margin: 17,
		dots: true,
		nav: true,
		autoHeight: true,
		responsiveRefreshRate: 10
	});
	
	/* Press Media Tabs */
	
	$("#pressMedia .tabs li a").on("click", function(e){
		$(this).parents(".tabs").find("li.active").removeClass("active");
		$(this).parent().addClass("active");
		
		var tabIndex = $(this).parent().index();
		var contents = $(this).parents(".section").find(".contents");
		
		contents.removeClass("active");
		contents.eq(tabIndex).addClass("active");
		e.preventDefault();
	});
	
	$("#pressMedia .tabs li.active").each(function(){
		var tabIndex = $(this).index();
		var contents = $(this).parents(".section").find(".contents");	
		contents.eq(tabIndex).addClass("active");		
	});

				
	/* Responsive Queries */
	
	ssm.addStates([
	{
		query: '(max-width: 767px)',
		onEnter: function(){
			showPerPage(pressSlider, $("#pressMedia .press ul.listing>li"), 8);
			showPerPage(mediaSlider, $("#pressMedia .media ul.listing>li"), 2);
			
		}
	},
	{
		query: '(min-width: 768px) and (max-width: 991px)',
		onEnter: function(){
			showPerPage(pressSlider, $("#pressMedia .press ul.listing>li"), 12);
			showPerPage(mediaSlider, $("#pressMedia .media ul.listing>li"), 3);
		}
	},
	{
		query: '(min-width: 992px)',
		onEnter: function(){
			showPerPage(pressSlider, $("#pressMedia .press ul.listing>li"), 15);
			showPerPage(mediaSlider, $("#pressMedia .media ul.listing>li"), 6);
		}
	}
	]);

	/* Items Per Page Function */
	
	function showPerPage(slider, listItems, perPage){
		var items = $(listItems).clone();	
		var itemContainer = $("<div></div>");
		var totalItems = items.length;
		var counter = 0;
		var perPage = perPage;

		$(itemContainer).append("<li><ul></ul></li>");
		
		for (a=1; a<=totalItems; a++){
			if (counter==perPage){
				$(itemContainer).append("<li><ul></ul></li>");
				counter =0;
			}
			
			$(itemContainer).find("ul:last").append($(items)[a-1])
			counter ++;
		}

		slider.trigger("replace.owl.carousel", [itemContainer.html()]).trigger("refresh.owl.carousel");		
	}

	/* Match Height */
	
	$.fn.matchHeight._maintainScroll = true;

	$('.equalHeight').matchHeight({
		 byRow: true
	});
	
	
});

$(window).load(function(){
	
	/* Before After Slider */
	
	$("#tryIt .beforeAfter .slider .images").twentytwenty({ default_offset_pct: 0.8});	
	
		
	/* Zoom Effect */
	
	$(".zoomEffect").on('mousemove', function(e){
		$(this).find(".pic").css({'transform-origin': ((e.pageX - $(this).offset().left) / $(this).width()) * 100 + '% ' + ((e.pageY - $(this).offset().top) / $(this).height()) * 100 +'%'});
    })	
	
});
