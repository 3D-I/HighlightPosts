<?php
/**
 *
 * Highlight Posts. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, 3Di & Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace threedi\hlposts\acp;

/**
 * Highlight Posts ACP module info.
 */
class hlposts_info
{
	public function module()
	{
		return array(
			'filename'	=> '\threedi\hlposts\acp\hlposts_module',
			'title'		=> 'ACP_HLPOSTS_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_HLPOSTS_SETTINGS',
					'auth'	=> 'ext_threedi/hlposts && acl_a_hlposts_admin',
					'cat'	=> array('ACP_HLPOSTS_TITLE')
				),
				'pm_create'	=> array(
					'title'	=> 'ACP_HLPOSTS_PM_CREATE',
					'auth'	=> 'ext_threedi/hlposts && acl_a_hlposts_admin',
					'cat'	=> array('ACP_HLPOSTS_TITLE')
				),
				'pm_edit'	=> array(
					'title'	=> 'ACP_HLPOSTS_PM_EDIT',
					'auth'	=> 'ext_threedi/hlposts && acl_a_hlposts_admin',
					'cat'	=> array('ACP_HLPOSTS_TITLE')
				),
				'pm_delete'	=> array(
					'title'	=> 'ACP_HLPOSTS_PM_DELETE',
					'auth'	=> 'ext_threedi/hlposts && acl_a_hlposts_admin',
					'cat'	=> array('ACP_HLPOSTS_TITLE')
				),
			),
		);
	}
}
