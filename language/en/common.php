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
	'HLPOSTS_ERROR_CAN_NOT'		=> 'You <strong>do not</strong> have the permission to highlight a post.',
	'HLPOSTS_ERROR_DISABLED'	=> 'Highlight Posts has been <strong>disabled</strong> for this specific forum.',
	'HLPOSTS_ERROR_TEXT_NONE'	=> 'Your message contains <strong>too few</strong> characters.',
	'HLPOSTS_ERROR_NO_GROUP'	=> 'Group not found!',

	'HLPOSTS_ADD'				=> 'Add highlight',
	'HLPOSTS_ADDED_TEXT'		=> 'You have successfully highlighted this post.',
	'HLPOSTS_ADDED_TITLE'		=> 'Highlight added',

	'HLPOSTS_DELETE'			=> 'Delete highlight',
	'HLPOSTS_DELETE_CONFIRM'	=> 'Are you sure you want to delete the highlight for this post?',
	'HLPOSTS_DELETED_TEXT'		=> 'You have successfully deleted the highlight for this post.',
	'HLPOSTS_DELETED_TITLE'		=> 'Highlight deleted',

	'HLPOSTS_EDIT'				=> 'Edit highlight',
	'HLPOSTS_EDITED_TEXT'		=> 'You have successfully edited the highlight for this post.',
	'HLPOSTS_EDITED_TITLE'		=> 'Highlight edited',

	'HLPOSTS_MARK_TIME'			=> 'Mark time',

	'HLPOSTS_NOTIFY_BOARD'		=> 'Send notification',
	'HLPOSTS_NOTIFY_PM'			=> 'Send PM notification',

	'HLPOSTS_POST'				=> 'Highlight post',

	'HLPOSTS_POST_MARK_READ_INTERVAL'	=> 'You cannot mark another post read so soon after your last.',
	'HLPOSTS_POST_MARKED_READ'			=> 'Post has been marked read.',
	'HLPOSTS_POST_MARKED_USERS'			=> 'Users who have marked this post read',
	'HLPOSTS_POST_MARKED_VIEW'			=> 'View who have marked this post read',
	'HLPOSTS_POST_RETURN'				=> 'Return to post',

	'HLPOSTS_SEARCH_EXPLAIN'			=> 'Use this form to search for specific members. You do not need to fill out all fields.<br>To match partial username data use * as a wildcard.<br>When entering dates use the <code>YYYY-MM-DD</code> format, e.g. <code>2018-02-14</code>.',

	/*
	 * Notification language info
	 * ---------------------------
	 * %1$s is the Username of the user highlighting the post
	 * Post subject is added automatically on the next line (by reference)
	 */
	'HLPOSTS_NOTIFICATION_ADDED'		=> '<strong>Post highlight added</strong> by %1$s:',
	'HLPOSTS_NOTIFICATION_EDITED'		=> '<strong>Post highlight edited</strong> by %1$s:',
	'HLPOSTS_NOTIFICATION_DELETED'		=> '<strong>Post highlight deleted</strong> by %1$s:',
));
