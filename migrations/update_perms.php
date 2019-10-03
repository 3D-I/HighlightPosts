<?php
/**
 *
 * Highlight Posts. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, 2019 - 3Di & Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace threedi\hlposts\migrations;

class update_perms extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return ['\threedi\hlposts\migrations\install_perms'];
	}

	public function update_data()
	{
		return [
			/* User permission set (no guests and bots) */
			['permission.add', ['u_allow_hlposts_icon_eye']],
			['permission.permission_set', ['REGISTERED', 'u_allow_hlposts_icon_eye', 'group']],
		];
	}
}
