$(document).ready(function(){

	//If windows size change
//	$(window).resize(function() {

		/* Scrolled class toggle */
		if($(document).width() > 1024) {
			$(document).scroll(function(){
				var s = $(document).scrollTop();
				if(s > 0){
					$('.navbar.navbar-inverse, .navbar.navbar-inverse .navbar-inner, .custom-nav, body').addClass('scrolled');
				}
				else{
					$('.navbar.navbar-inverse, .navbar.navbar-inverse .navbar-inner, .custom-nav, body').removeClass('scrolled');
				}
			});
		}


		/* Show Loader on click anchor / submit */
		if($(document).width() <= 1024) {
			$('a').on('click', function(e){
				if($(this).is(':not([data-toggle])')){
					if($(this).attr('href') != 'javascript:void(0);'){
						if($(this).attr('href') != '#'){
							if($(this).attr('href').charAt(0) != '#'){
								$('.loader-general, .block_loader').fadeIn('fast');
							}
						}
					}
				}
			});

	        $(".dropdown-submenu").on("click", function (e) {
	            $(this).toggleClass('open');
	            $(this).parent().parent().is(".open") && e.stopPropagation();
	        });
		}

		/* BUTTONS LIST RESPONSIVE DROPDOWN */
	    if($(document).width() <= 1024) {
	       var dp = "<div class='dropdown-actions pull-right'>";
	               dp+= "<div class='dropdown'>";
	                   dp+="<button aria-expanded='true' aria-haspopup='true' data-toggle='dropdown' id='dropdownMenu1' type='button' class='btn btn-primary dropdown-toggle'>";
	                   dp+="<span class='glyphicon glyphicon-option-vertical icon-options'></span>";
	                   dp+="</button>";

	                   dp+="<ul aria-labelledby='dropdownMenu1' class='dropdown-menu pull-right inner-dropdown'>";

	                   var links = $('.page-header .title-actions a').length;
	                   for(i=0;i<links;i++){
	                     dp+="<li></li>";
	                   }
	                   dp+="</ul>";

	               dp+="</div>";
	           dp+="</div>";

	           $(dp).insertBefore('.page-header .title-actions');

	           var i = 0;
	           $('.page-header .title-actions a').each(function(){
	               $('.inner-dropdown li').eq(i).append(this);
	               i++;
	           });

	           var last_i = i;

	           var more = $('.more-buttons a').length;
	           if(more > 0){
	               var lis = "";
	               for(i=0;i<more;i++){
	                   lis+="<li></li>";
	               }
	               $('.inner-dropdown').append(lis);
	               var j = last_i;
	               $('.more-buttons a').each(function(){
	                   $('.inner-dropdown li').eq(j).append(this);
	                   j++;
	               });

	               $('.more-buttons').closest('.row').remove();
	           }

	           $('.inner-dropdown li a span').remove();
	           $('.inner-dropdown li a').attr('class','');
	           $('.inner-dropdown li a').addClass('btn-dp');

	           /* Responsives Tables */
	           $("table:not(.datetimepicker table)").rtResponsiveTables();
	    }


	    /* Move Search bar on width 480px or less */
	    if($(document).width() <= 480) {
	    	var obj = $('form#search[role="search"], form[role="search"]');
	    	var html = "<div class='responsive-search'></div>";
	    	obj.parent().parent().parent().prepend(html);
	    	obj.appendTo('.responsive-search');
	    }

//	});


	/* show/hide sidebar-menu */
	$('.btn-menu-responsive, .block-back-menu-responsive').on('click',function(){
	  $('.custom-nav').toggleClass( "mostrar" );
	  $('.block-back-menu-responsive').toggleClass("show-back");

	  if($('.block-back-menu-responsive').hasClass('show-back')){
		  $('body').css('overflow','auto');
	  }
	  else {
		  $('body').css('overflow','hidden');
	  }

          if($('.custom-nav').hasClass('mostrar')){

          }
          else {
              $('.dropdown-menu, .dropdown-submenu').removeClass('open');
          }

	});


	/* On open Modal hide body scrollbar */
	$('.modal').on('show.bs.modal', function(){
		$('body').css('overflow', 'hidden');
	});
	$('.modal').on('hide.bs.modal', function(){
		$('body').css('overflow', 'auto');
	});

    $('body .container').css('opacity','1');

    /* Responsive Tables Actions */

    if($('.rt-vertical-table').is(':visible')){
    	$('.rt-vertical-table tbody tr').on('click', function(){
    		if($('.popover').hasClass('in')){

    		} else {
	    		$('.rt-vertical-table tbody tr td:last-child').not($(this).find('td:last-child')).hide();
	    		$(this).find('td:last-child').toggle();
    		}
    	});
    }

    $('.fa-parent').tooltip({
    	placement : 'bottom'
    });
});

$(function() {

  // We can attach the `fileselect` event to all file inputs on the page
  $(document).on('change', ':file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
  });

  // We can watch for our custom `fileselect` event like this
  $(document).ready( function() {
      $(':file').on('fileselect', function(event, numFiles, label) {

          var input = $(this).parents('.input-group').find(':text'),
              log = numFiles > 1 ? numFiles + ' files selected' : label;

          if( input.length ) {
              input.val(log);
          } else {
              if( log ) alert(log);
          }

      });
  });

});
