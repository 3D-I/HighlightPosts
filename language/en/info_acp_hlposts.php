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
	'ACP_HLPOSTS_TITLE'						=> 'Highlight posts',
	// ACP modes
	'ACP_HLPOSTS_SETTINGS'					=>	'Settings',
	'ACP_HLPOSTS_PM_CREATE'					=>	'Manage PM on create',
	'ACP_HLPOSTS_PM_EDIT'					=>	'Manage PM on edit',
	'ACP_HLPOSTS_PM_DELETE'					=>	'Manage PM on delete',

	// Logs
	'ACP_HLPOSTS_LOG_HIGHLIGHT_ADDED'		=>	'<strong>Highlighted a post with the following comment:</strong><br>» %s',
	'ACP_HLPOSTS_LOG_HIGHLIGHT_DELETED'		=>	'<strong>Deleted a highlight from a post</strong>',
	'ACP_HLPOSTS_LOG_HIGHLIGHT_EDITED'		=>	'<strong>Edited a highlight for a post with the following comment:</strong><br>» %s',

	'ACP_HLPOSTS_LOG_SETTINGS_SAVED'		=>	'<strong>Highlight posts settings saved.</strong>',
	'ACP_HLPOSTS_LOG_PM_CREATE_SAVED'		=>	'<strong>Highlight posts PM on create settings saved.</strong>',
	'ACP_HLPOSTS_LOG_PM_EDIT_SAVED'			=>	'<strong>Highlight posts PM on edit settings saved.</strong>',
	'ACP_HLPOSTS_LOG_PM_DELETE_SAVED'		=>	'<strong>Highlight posts PM on delete settings saved.</strong>',

	// ACP
	'ACP_HLPOSTS_SETTINGS_TITLE'			=>	'Highlight Posts Settings',

	// ACP Forums
	'ACP_HLPOSTS_FORUMS_LEGEND'				=>	'Extended settings',
	'ACP_HLPOSTS_FORUMS_ENABLE'				=>	'Enable Highlight Posts extension',
	'ACP_HLPOSTS_FORUMS_ENABLE_EXPLAIN'		=>	'If set to <strong>Yes</strong> the functions provided will be in use here.<br>Setting back to <strong>No</strong> does preserve the existing Highlights as well, so to be used again if you change your mind.',
));
