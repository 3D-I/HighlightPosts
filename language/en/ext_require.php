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
	'HLPO_ERROR_321_VERSION'	=>	'Minimum phpBB version required is 3.2.1 but less than 3.3.0@dev',
	'HLPO_ERROR_PHP_VERSION'	=>	'PHP version must be equal or greater than 5.4.7',
));
