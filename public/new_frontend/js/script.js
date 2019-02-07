
$(function () {


	// tabs
	$('.tab_content').hide(); 
	$('.tab_content:first').show(); //show first tab
	$('.tab_header a:first-child').addClass('active'); //make first nav link active

	$('.tab_header a').click(function() {
		var $this = $(this).attr('href');
		$('.tab_content').hide(); 
		$($this).fadeIn(1000); 

		$('.tab_header a').removeClass('active'); 
		$(this).addClass('active');

		// return false;
	});

    // Time slots media house select
    $('.one_media').click(function() {

        $('.one_media').removeClass('active');
        $(this).addClass('active');

    });

    //Scroll
	$('.click_nav').click(function() {
	    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') 
	        || location.hostname == this.hostname) {

	        var target = $(this.hash);
	        target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
	           if (target.length) {
	             $('html,body').animate({
	                 scrollTop: target.offset().top - 95
	            }, 500);
	            return false;
	        }
	    }
	});


    jQuery( document ).ready(function( $ ) {
        $('.modal_click').on('click', function () {
            var ref = $(this).attr('href');
            $(ref).modal();
            return false;
        });
    });

    //avatar image upload
    function readURL(input, targ) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                targ.attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // show image selection before upload
    $(document).on("change", ".upload_profile", function() {

        var target = $(this).parent('.profile_hold').find('.target');
        readURL(this, target); //show preview image
    });

});
