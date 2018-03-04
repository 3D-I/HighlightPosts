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

class install_mark_config extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		/* If does NOT exist go ahead */
		return isset($this->config['hlposts_mark_confirmation']);
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v32x\v321');
	}

	public function update_data()
	{
		return array(
			/* (BOOL) Display 'Marked read' confirmation message */
			array('config.add', array('hlposts_mark_confirm_show', 0)),
			/* (INT) Display time for 'Marked read' confirmation message */
			array('config.add', array('hlposts_mark_confirm_time', 3000)),
		);
	}
}
