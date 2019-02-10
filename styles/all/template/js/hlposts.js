(function($) {

'use strict';

$(function() {

	// Show the post highlight
	$('.post-buttons').on('click', '.highlight-toggle', function() {
		var postId = $( this ).val();
		$("#highlight_" + postId).toggle();
	});

	// Change Font Awesome icon on hover
	$("[id^='mark_read_'], [name^='mark_read_']").hover(
		function() {
			$(this).children('i').removeClass('fa-eye-slash').addClass('fa-eye');
		}, function() {
			$(this).children('i').removeClass('fa-eye').addClass('fa-eye-slash');
		}
	);

	$('.post-buttons').on('click', '.highlight-view', function() {
		$.get($(this).data('url'), function(response) {
			if (response.CONFIRM_DISPLAY)
			{
				phpbb.alert(response.MESSAGE_TITLE, response.MESSAGE_TEXT);
			}
			markPostRead(response);
		}, 'json');
	});

});

phpbb.addAjaxCallback('threedi_hlposts_mark', function(response) {
	markPostRead(response);
});

function markPostRead(response) {
	// Grab response data
	var postId				= response.POST_ID,
		langNewTitle		= response.MESSAGE_TEXT,
		readDisplayBool		= response.READ_DISPLAY_BOOL,
		back2topBool		= response.BACK2TOP_BOOL,
		postingButtonsBool	= response.POSTING_BUTTONS_BOOL;

	// Set icon id's
	var $iconPostingButton	= $('[name="mark_read_' + postId + '_posting_buttons"'),
		$iconBack2Top		= $('#mark_read_' + postId + '_back2top');

	// if the 'back2top' button is enabled
	if (back2topBool) {
		// if we have to show the 'marked read' icon
		if (readDisplayBool) {
			$iconBack2Top.replaceWith('<span class="top hlposts-margin-fix" title="' + langNewTitle + '"><i class="icon fa-eye fa-fw" style="color: #FF0000 !important;" aria-hidden="true"></i></span>');
		} else {
		// else we remove the DOM element
			$iconBack2Top.remove();
		}
	}

	// if the 'posting buttons' button is enabled
	if (postingButtonsBool) {
		// if we have to show the 'marked read' icon
		if (readDisplayBool) {
			$iconPostingButton.replaceWith('<button class="button-icon-only highlight-view" title="' + langNewTitle + '" name="marked_read_' + postId + '" disabled><i class="icon fa-eye fa-fw" style="color: #FF0000 !important;" aria-hidden="true"></i><span>' + langNewTitle + '</span></button>');
			$('[name="marked_read_' + postId + '"]').first().addClass('button');
		} else {
		// else we remove the DOM element (the parent, which is the <li>-element)
			$iconPostingButton.parent().remove();
		}
	}

	// Close the AJAX Response pop up after 3 seconds.
	if (response.CONFIRM_DISPLAY) {
		phpbb.closeDarkenWrapper(response.CONFIRM_DISPLAY_TIME);
	}
}

})(jQuery);
