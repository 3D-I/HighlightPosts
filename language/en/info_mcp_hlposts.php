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
	'ACP_HLPOSTS_LOG_HIGHLIGHT_ADDED'		=> '<strong>Highlighted a post with the following comment:</strong><br />» %s',
	'ACP_HLPOSTS_LOG_HIGHLIGHT_DELETED'		=> '<strong>Deleted a highlight from a post</strong>',
	'ACP_HLPOSTS_LOG_HIGHLIGHT_EDITED'		=> '<strong>Edited a highlight for a post with the following comment:</strong><br />» %s'
));
