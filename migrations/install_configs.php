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

class install_configs extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		/* If does NOT exist go ahead */
		return isset($this->config['hlposts_mark_interval']);
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v32x\v321');
	}

	public function update_data()
	{
		return array(
			/* (INT) notifications sent counter */
			array('config.add', array('hlposts_notification_id', 0)),

			/* (BOOL) 'Marked read' icon visibility (show/hide) */
			array('config.add', array('hlposts_read_icon_display', 0)),

			/* (BOOL) 'Marked read' icon tpl location = posting area*/
			array('config.add', array('hlposts_read_icon_tpl_p', 1)),

			/* (BOOL) 'Marked read' icon tpl location = miniprofile) */
			array('config.add', array('hlposts_read_icon_tpl_m', 1)),

			/* (DOUBLE) Flood's time limit in minutes - 0 means disabled */
			array('config.add', array('hlposts_mark_interval', 15)),

			/* (BOOL) Notification's default - 0 means disabled */
			array('config.add', array('hlposts_notify_default', 0)),

			/* (BOOL) PM's default - 0 means disabled */
			array('config.add', array('hlposts_pm_default', 0)),

			/* (STRING) PM's default - Signature */
			array('config.add', array('hlposts_pm_sig', 'Lorem ipsum, cogito ergo sum.')),

			/* (INT) Bot user ID */
			array('config.add', array('hlposts_bot', 2)),

			/* (BOOL) Use of the Bot - 0 means disabled */
			array('config.add', array('hlposts_use_bot', 0)),

			/* (INT) Users per pagination's page - 0 means disabled */
			array('config.add', array('hlposts_users_page', 6)),

			/* (BOOL) Post's overview in view page - 0 means disabled */
			array('config.add', array('hlposts_post_overview', 1)),
		);
	}
}
