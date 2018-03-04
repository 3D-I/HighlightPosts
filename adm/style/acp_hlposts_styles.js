(function($) {

'use strict';

$(function() {
	// Detect change in our style select-box
	$('#hlposts_styles').on('change', function() {
		// Hide all style fieldsets
		$(this).parents('fieldset.hlposts-acp-inner').siblings().hide();

		// Show the selected style's fieldset
		$('#' + $(this).children('option:selected').val()).show(400);
	});
});

}) (jQuery);
