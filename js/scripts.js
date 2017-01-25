// JavaScript Document// DOM Ready
jQuery(function() {
	
	// SVG fallback
	// toddmotto.com/mastering-svg-use-for-a-retina-web-fallbacks-with-png-script#update
	if (!Modernizr.svg) {
		var imgs = document.getElementsByTagName('img');
		var dotSVG = /.*\.svg$/;
		for (var i = 0; i != imgs.length; ++i) {
			if(imgs[i].src.match(dotSVG)) {
				imgs[i].src = imgs[i].src.slice(0, -3) + "png";
			}
		}
	}
	
});

jQuery(document).ready( function($){
	
	/*** Functions to add select functionality to a UL list 
		 Used on account.html page for the avatar Drop Down
	***/
	$('.fake-select>li>a').on('click', function(e){
		e.preventDefault();
		var height = $(this).outerHeight();
		$(this).next('ul').css('top', height).slideToggle('slow');
	});
	/** When avatar is clicked, set it as selected and add to 
		hidden input 
	*/
	$('.fake-select ul').on('click', 'li', function(e){
		e.preventDefault();
		// Remove class from sibling
		$(this).siblings('li').removeClass('selected');
		// Add Selected class to li
		$(this).addClass('selected');
		var html = $(this).find('a').html();
		var val = $(this).find('a').data('value');
		$('#date').val(val);
		$(this).parents('.fake-select').find('.control .date').html(html);
		
		$(this).parent('ul').slideUp('slow');
		
	});
	
	// Form Field Validation
	$('#fname, #lname, #email').on('keypress', function(){
		checkField($(this));
	});
	
	$('#fname, #lname, #email').on('blur', function(){
		checkField($(this));
	});
    
    //Check for Google Adwords ?gclid 
    var href = location.href;
    var url = href.split('gclid=');
    if(url){
        $('#gclid').val(url[1]);
    }
	
});

/** Function to validate a field

	@paramenter el -jQuery object
	
**/
function checkField(el){
		var error = 0;
		var emailcheck = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/; // Variable for email format
		var value = el.val();
		if(value.length < 1){
			error = 1;
		}
		
		// If email field, check for valid email format
		if(el.is('#email')){
			if(!emailcheck.test(value)){
				error = 1;
			}
		}
		
		if(error == 1){
			el.parents('.form-item').removeClass('success').addClass('error');
		} else {
			el.parents('.form-item').removeClass('error').addClass('success');
		}
}

function checkForm(){
	
	var fields = $('.form-item');
	
	//Check to see if there is error states
	if($('.form-item').hasClass('error')){
		return false;
	}
	//Check for success!
	var success=0;
	fields.each( function(){
		if($(this).hasClass('required')){
			checkField($(this).find('input'));
			if($(this).hasClass('success')){
				success++;
			}
		}
	});
	console.log(success);
	if(success === 3){
		return true;	
	} else {
		return false;
	}
}