<?php
/**
 *
 * Highlight Posts. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, 3Di & Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace threedi\hlposts\migrations;

class install_perms extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v32x\v321');
	}

	public function update_data()
	{
		return array(
			/* User permission set (no guests and bots) */
			array('permission.add', array('u_allow_hlposts_view')),
			array('permission.permission_set', array('REGISTERED', 'u_allow_hlposts_view', 'group')),

			/* Moderative permissions not set */
			array('permission.add', array('m_hlposts_can_highlight')),
			array('permission.add', array('m_hlposts_can_view_read')),

			/* Admin's permission set */
			array('permission.add', array('a_hlposts_admin')),
			array('permission.permission_set', array('ADMINISTRATORS', 'a_hlposts_admin', 'group')),
		);
	}
}
