(function($) {

'use strict';

$(function() {
	$('#firstCollapseMenu').collapsible({
		accordion: false,
		accordionUpSpeed: 400,
		accordionDownSpeed: 400,
		collapseSpeed: 400,
		contentOpen: [0, 1, 2], 
		arrowRclass: 'arrow-r',
		arrowDclass: 'arrow-d',
		animate: true
	});
});

}) (jQuery);
