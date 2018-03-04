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

class install_forums_switch extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		/* If doesn't exist go ahead */
		return $this->db_tools->sql_column_exists($this->table_prefix . 'forums', 'hlposts_f_enable');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v32x\v321');
	}

	public function update_schema()
	{
		/* All the existing forums will be enabled as default */
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'forums'	=> array(
					'hlposts_f_enable'	=> array('BOOL', 1),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'forums'	=> array(
					'hlposts_f_enable',
				),
			),
		);
	}
}
