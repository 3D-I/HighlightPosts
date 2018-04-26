<?php
/**
 *
 * Highlight Posts. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, 3Di & Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	// Legend 1
	'ACP_HLPOSTS_SETTINGS'					=>	'Settings',

	// Legend 2
	'ACP_HLPOSTS_GENERAL'					=>	'General',
	'ACP_HLPOSTS_TEMPLATE'					=>	'Template',
	'ACP_HLPOSTS_STYLE'						=>	'Style',

	// Legend 3
	'ACP_HLPOSTS_SETTINGS_BASIC'			=>	'Basics',
	'ACP_HLPOSTS_VIEW_PAGE'					=>	'Post overview',
	'ACP_HLPOSTS_TEMPLATE_LOCATIONS'		=>	'Icons',
	'ACP_HLPOSTS_TEMPLATE_LOCATIONS_EXPLAIN'=>	'Here below you may chose to have the icon(s) on both locations simultaneously, which helps in case of long posts. Please <em><strong>note</strong></em>, at least one location has to be chosen.',
	'ACP_HLPOSTS_TEMPLATE_COLORS'			=>	'Highlight colors',

	// Basics
	'ACP_HLPOSTS_FLOOD_INTERVAL_NOTE'		=>	'Here below you can chose how many minutes an user have to wait before to mark as read another post.',
	'ACP_HLPOSTS_FLOOD'						=>	'Flood',
	'ACP_HLPOSTS_FLOOD_EXPLAIN'				=>	'Interval in Minutes (max 60) - 0 disables the feature',
	'ACP_HLPOSTS_MARK_CONFIRM_SHOW'			=>	'Display the “marked read” confirmation message',
	'ACP_HLPOSTS_MARK_CONFIRM_SHOW_EXPLAIN'	=>	'<strong>No</strong> overrides the setting here below.',
	'ACP_HLPOSTS_MARK_CONFIRM_TIME'			=>	'Display time for “marked read” confirmation message',
	'ACP_HLPOSTS_MARK_CONFIRM_TIME_EXPLAIN'	=>	'The time is in milliseconds. So <samp>3000</samp> are 3 seconds.',
	'ACP_HLPOSTS_MARK_CONFIRM_TIME_NOTE'	=>	'Here below you can chose how long the confirmation message will be displayed after a user marked a post read, max 10 seconds.',
	'ACP_HLPOSTS_READ_ICON_DISPLAY'			=>	'Display “marked read” icon',
	'ACP_HLPOSTS_READ_ICON_DISPLAY_EXPLAIN'	=>	'Show the icon once the post is marked as read.',
	'ACP_HLPOSTS_READ_ICON_TPL'				=>	'Location of “mark read” icon',
	'ACP_HLPOSTS_TEMPLATE'					=>	'Template',
	'ACP_HLPOSTS_PM_SIG'					=>	'Your custom signature for PMs notification',
	'ACP_HLPOSTS_PM_SIG_EXPLAIN'			=>	'Leave empty if not used. Emojis will be stripped.',
	'ACP_HLPOSTS_BOT'						=>	'PMs Bot',
	'ACP_HLPOSTS_BOT_TITLE'					=>	'Select an user/Bot',
	'ACP_HLPOSTS_USE_BOT'					=>	'Use PMs Bot',
	'ACP_HLPOSTS_USE_BOT_EXPLAIN'			=>	'If <strong>No</strong> been chosen then the Sender will be the Highlighter of the post and the selection will be ignored.<br> Otherwise please choose an user with the dropdown box, which will be the Sender of the PMs, then select <strong>Yes</strong>.<br><br><strong>Note</strong>: the system will not overload the PMs inbox/sentbox folders of the Sender/Bot. Would be a good idea to create a fake-user for that, though.',
	'ACP_HLPOSTS_USERS_PAGE'				=>	'Users per page',
	'ACP_HLPOSTS_USERS_PAGE_EXPLAIN'		=>	'Here below you can chose how many users who have marked the post as read will be shown on a per page basis, within the pagination. <strong>Note</strong>, <strong>0</strong> disables the feature and the number of users will be the same as per the ACP native “<em>posts per page</em>” facility.',

	// Post overview
	'ACP_HLPOSTS_POST_OVERVIEW'				=>	'Display the post',
	'ACP_HLPOSTS_POST_OVERVIEW_EXPLAIN'		=>	'If Yes, the post will be shown in the “who has marked this post as read” page',
	// Icons
	'ACP_HLPOSTS_READ_ICON_POSTING'			=>	'Within the posting buttons',
	'ACP_HLPOSTS_READ_ICON_MINIPROFILE'		=>	'In the miniprofile next to posts.',
	'ACP_HLPOSTS_NOTIFY_DEFAULT_NOTE'		=>	'Make sure “allow board notifications” are ON. Otherwise, regardless of these settings, Highlight Posts notifications are not send.',
	'ACP_HLPOSTS_NOTIFY_DEFAULT'			=>	'Send notification default',
	'ACP_HLPOSTS_NOTIFY_DEFAULT_EXPLAIN'	=>	'Preset the checkbox for sending the notification to the users.',
	'ACP_HLPOSTS_PM_DEFAULT'				=>	'Send PM notification default',
	'ACP_HLPOSTS_PM_DEFAULT_EXPLAIN'		=>	'Preset the checkbox for sending a PM notification to the users.',

	// Highlight colors
	'ACP_HLPOSTS_STYLING'					=>	'Select a style',
	'ACP_HLPOSTS_STYLING_EXPLAIN'			=>	'Configure the highlight’s colors for each syle',
	'ACP_HLPOSTS_STYLES_SELECTOR'			=>	'Please select a style',
	'ACP_HLPOSTS_BCKG'						=>	'Background',
	'ACP_HLPOSTS_TEXT'						=>	'System Text',
	'ACP_HLPOSTS_TEXT_EXPLAIN'				=>	'Note, <strong>not</strong> the content of your highlight',
	'ACP_HLPOSTS_BORD'						=>	'Box-shadow',
	'ACP_HLPOSTS_COLORPICKER_EXPLAIN'		=>	'Input a color in #HexDec value or use the color-picker.',
	'ACP_HLPOSTS_COLORPICKER_STORED'		=>	'Color #HexDec value and actual color stored in the DB.',
	'ACP_HLPOSTS_SETTINGS_HEX_STORED'		=>	'Now',

	// PMs related
	'ACP_HLPOSTS_PM_SIG_HOLDER'				=>	'Insert your text or leave it empty...',
	'ACP_HLPOSTS_PM_TITLE_HOLDER'			=>	'Insert your title...',
	'ACP_HLPOSTS_PM_MESSAGE_HOLDER'			=>	'Insert your message...',
	'ACP_HLPOSTS_PM_ON_CREATE'				=>	'PM on creation',
	'ACP_HLPOSTS_PM_ON_EDIT'				=>	'PM on edition',
	'ACP_HLPOSTS_PM_ON_DELETE'				=>	'PM on deletion',
	'ACP_HLPOSTS_PM_INPUT'					=>	'Input your message and title',
	'ACP_HLPOSTS_PM_EMOJIS'					=>	'Note, Emojis used in the message subject will be replaced by the char “�”.',
	'ACP_HLPOSTS_PM_TOKENS_PREVIEW'			=>	'Tokens (<em>{RECIP_NAME}, {RECIP_NAME_FULL}, {SIG}, {P_LINK}</em>) are not parsed in some parts of the preview.',
	'ACP_HLPOSTS_PM_TOKENS'					=>	'Understanding Tokens',
	'ACP_HLPOSTS_PM_TOKENS_EXPLAIN'			=>	'Here below you may copy&paste some <strong>tokens</strong> which should help you to compose the PM.',
	'ACP_HLPOSTS_PM_TOKEN'					=>	'Token',
	'ACP_HLPOSTS_PM_TOKEN_DEFINITION'		=>	'Definition and availability',

	'hlposts_tokens'	=> array(
		'SENDER_NAME'		=> 'The plain username of the Sender<br><em>Who highlighted the post</em>',
		'RECIP_NAME'		=> 'The plain username of the Recipient<br><em>The Author of the post</em>',
		'SENDER_NAME_FULL'	=> 'The username of the Sender with its color and clickable profile link.<br>(<em>Will be replaced by {SENDER_NAME} if used in PM Title</em>)',
		'RECIP_NAME_FULL'	=> 'The username of the Recipient with its color and clickable profile link.<br>(<em>Will be replaced by {RECIP_NAME} if used in PM Title</em>)',
		'SIG'				=> 'The custom signature you created in the Settings page.<br>(<em>Not available in PM Title, replaced by nothing if used there</em>)',
		'P_LINK'			=> 'The clickable link to the highlight.<br>(<em>Not available in PM Title, replaced by nothing if used there</em>)',
	),

	// Errors
	'ACP_HLPOSTS_ERRORS'					=>	'Errors report',
	'ACP_HLPOSTS_EMPTY_TITLE'				=>	'<strong>The Subject cannot be empty.</strong>',
	'ACP_HLPOSTS_EMPTY_MESSAGE'				=>	'<strong>The Message cannot be empty.</strong>',
	'ACP_HLPOSTS_MIN_TPL_WRONG'				=>	'<strong>Please chose at least one location for the “marked read” icon.</strong>',

	// Common
	'ACP_HLPOSTS_OVERVIEW'					=>	'Overview',
	'ACP_HLPOSTS_SETTINGS_SAVED'			=>	'<strong>Highlight posts settings saved.</strong>',
));
