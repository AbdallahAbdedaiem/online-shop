$(function() {
	'use strict';

	/*
	====================================
	====================================
	====================================
	===========   FRONTEND   ===========
	====================================
	====================================
	====================================
	*/

	//swith login | sign up
	$('.auth-page h1 span').click(function(){
		$(this).addClass('active').siblings('span').removeClass('active');
		$('.auth-page form').hide();
		$('form' +  '.' + $(this).data('class')).slideDown(80).siblings('form').slideUp(80);
	});

	//error fade out
    $(".my-toast").fadeOut(3000, function(){
        $(this).remove();
    });

    //live preview Ad
    /*First method
    $('.add-item .live-name').keyup(function(){
    	$('.live-preview .card-title')
    		.text($(this).val());
    });
    $('.add-item .live-desc').keyup(function(){
    	$('.live-preview .card-text')
    		.text($(this).val());
    });
    $('.add-item .live-price').keyup(function(){
    	$('.live-preview span')
    		.text("$" + $(this).val());
    });*/
    //method 2
    $('.live').keyup(function(){
    	$($(this).data('class')).text($(this).val());
    })











	/*
	====================================
	====================================
	====================================
	===========   Backend   ============
	====================================
	====================================
	====================================
	*/
	//Trigger the selectBoxIt plugin
	$('select').selectBoxIt({
		autoWidth: false
	});


	//hide place holder on form focus
	$('[placeholder]').focus(function(){
		/*store placeholder in attr called data-text*/
		$(this).attr('data-text',$(this).attr('placeholder'));
		$(this).attr('placeholder','');
	}).blur(function(){
		$(this).attr('placeholder', $(this).attr('data-text'));
	});

	//Add asterisk on required fields
	$('input').each(function(){
		if($(this).attr('required') === 'required'){
			$(this).after("<span class = 'asterisk'>*</span>")
		}
	});

	//confirmation message on button
	$('.confirm').click(function(){
		return confirm('Are you sure?')
	});


});