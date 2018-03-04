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

class install_read_schema extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . 'post_read');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v32x\v321');
	}

	public function update_schema()
	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix . 'post_read'		=> array(
					'COLUMNS'		=> array(
						'user_id'			=> array('UINT', 0),
						'post_id'			=> array('UINT', 0),
						'read_time'			=> array('TIMESTAMP', 0),
					),
					'KEYS'			=> array(
						'user_id'			=> array('INDEX', 'user_id'),
						'post_id'			=> array('INDEX', 'post_id'),
					),
				),
			),
		);
	}
}
