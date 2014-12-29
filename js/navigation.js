/*
*
*	This file will manage all the events which concern
*	the navigation, when a user clicks on a link etc ...
*
*	Author: David Docteur
*
*/


/*
	Initialise the scroll function
*/

// Scroll detection
$(window).scroll(function() {
	if($(this).scrollTop() != 0) {
		$('#toTop').fadeIn();	
	} else {
		$('#toTop').fadeOut();
	}
});
		 
// Click onTop
$('#toTop').click(function() {
	$('body,html').animate({scrollTop:0},300);
});	

