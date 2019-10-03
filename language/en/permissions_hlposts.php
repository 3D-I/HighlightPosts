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
	'ACL_U_HLPOSTS_VIEW'				=> '<strong>Highlight Posts</strong> - Can mark posts as read',
	'ACL_U_ALLOW_HLPOSTS_ICON_EYE'		=> '<strong>Highlight Posts</strong> - Can see the eye icon',
	'ACL_M_HLPOSTS_CAN_HIGHLIGHT'		=> '<strong>Highlight Posts</strong> - Can add/edit/delete Highlights',
	'ACL_M_HLPOSTS_CAN_VIEW_READ'		=> '<strong>Highlight Posts</strong> - Can view who marked posts as read',
	'ACL_A_HLPOSTS_ADMIN'				=> '<strong>Highlight Posts</strong> - Can manage the Extension',
));
