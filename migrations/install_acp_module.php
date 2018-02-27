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

class install_acp_module extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v32x\v321');
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_HLPOSTS_TITLE',
			)),
			array('module.add', array(
				'acp',
				'ACP_HLPOSTS_TITLE',
				array(
					'module_basename'	=> '\threedi\hlposts\acp\hlposts_module',
					'modes'				=> array('settings', 'pm_create', 'pm_edit', 'pm_delete'),
				),
			)),
		);
	}
}
