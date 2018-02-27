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

class install_config_txt_pms extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\threedi\hlposts\migrations\install_configs');
	}

	public function update_data()
	{
		return array(

			/* Highlight's colors */
			array('config_text.add', array('hlposts_colors', json_encode(array(1 => array(
								'bckg' => '#b6e0b6',
								'text' => '#2c3645',
								'bord' => '#2ea7a4',
							)
						)
					)
				)
			),

			/* PM on Create action */
			array('config_text.add', array('hlposts_pm_create', json_encode(array(
							'title'		=> 'Lorem ipsum {RECIP_NAME}',
							'message'	=> '{P_LINK} Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. {SIG}',
						)
					)
				),
			),

			/* PM on Edit action */
			array('config_text.add', array('hlposts_pm_edit', json_encode(array(
							'title'		=> 'Lorem ipsum {RECIP_NAME}',
							'message'	=> '{P_LINK} Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. {SIG}',
						)
					)
				),
			),

			/* PM on Delete action */
			array('config_text.add', array('hlposts_pm_delete', json_encode(array(
							'title'		=> 'Lorem ipsum {RECIP_NAME}',
							'message'	=> '{P_LINK} Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. {SIG}',
						)
					)
				),
			),
		);
	}

	public function revert_data()
	{
		return array(
			array('config_text.remove', array('hlposts_colors')),
			array('config_text.remove', array('hlposts_pm_create')),
			array('config_text.remove', array('hlposts_pm_edit')),
			array('config_text.remove', array('hlposts_pm_delete')),
		);
	}
}
