$(function() {
	'use strict';

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

	//convert password field to text field on hover
	var passField = $('.password');
	$('.show-pass').hover(function(){
		passField.attr('type','text');
	},
	function(){
		passField.attr('type','password');
	});

	//confirmation message on button
	$('.confirm').click(function(){
		return confirm('Are you sure?')
	});

	//category view option
	$('.categories .cat h5').click(function(){
		$(this).next('.full-view').css.backgroundColor = '#f00';
		$(this).siblings('.full-view').fadeToggle(200);
	});
	$('.options span').click(function(){
		$(this).addClass('active')
		.siblings('span').removeClass('active');
		if( $(this).data('view') == 'full'){
			$('.cat .full-view').fadeIn(200);
		} else {
			$('.cat .full-view').fadeOut(200);
		}
	});

	//dashboard toggle latest
	$(".latest i.pull-right").click(
		function(){
			$(this)
			.toggleClass("fa-plus fa-minus").
			parent().next('.card-body').slideToggle(100);
	})


});