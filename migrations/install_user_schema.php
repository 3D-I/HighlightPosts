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

class install_user_schema extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		/* If doesn't exist go ahead */
		return $this->db_tools->sql_column_exists($this->table_prefix . 'posts', 'is_highlighted');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v32x\v321');
	}

	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'posts'			=> array(
					'is_highlighted'		=> array('BOOL', 0),
				),
			),
			'add_tables'	=> array(
				$this->table_prefix . 'highlights'		=> array(
					'COLUMNS'		=> array(
						'post_id'			=> array('UINT', 0),
						'hl_text'			=> array('MTEXT', ''),
						'hl_time'			=> array('TIMESTAMP', 0),
						'hl_user_id'		=> array('UINT', 0),
						'hl_username'		=> array('VCHAR:255', ''),
						'hl_user_colour'	=> array('VCHAR:6', ''),
					),
					'KEYS'			=> array(
						'post_id'			=> array('INDEX', 'post_id'),
					),
				),
			),
		);
	}
}
