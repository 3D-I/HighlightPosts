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
 * Highlight Posts ACP module.
 */
class hlposts_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	public function main($id, $mode)
	{
		global $phpbb_container;

		$auth = $phpbb_container->get('auth');
		$config = $phpbb_container->get('config');
		$config_text = $phpbb_container->get('config_text');
		$db = $phpbb_container->get('dbal.conn');
		$language = $phpbb_container->get('language');
		$phpbb_log = $phpbb_container->get('log');
		$request = $phpbb_container->get('request');
		$template = $phpbb_container->get('template');
		$user = $phpbb_container->get('user');

		$phpbb_root_path = $phpbb_container->getParameter('core.root_path');
		$php_ext = $phpbb_container->getParameter('core.php_ext');

		$hlposts_utils = $phpbb_container->get('threedi.hlposts.hlposts_utils');

		/* Add our lang file */
		$language->add_lang('acp_hlposts', 'threedi/hlposts');

		/* Add the lang file needed by BBCodes */
		$language->add_lang('posting');

		/* Set the page title for our ACP page */
		$this->page_title = $language->lang('ACP_HLPOSTS_TITLE');

		/* Load a template from adm/style for our ACP page */
		$this->tpl_name = 'acp_hlposts';

		/* Add some security layer */
		add_form_key('threedi/hlposts');

		/* Set an empty ary of errors */
		$errors = array();

		/* Include files needed for displaying BBCodes */
		if (!function_exists('display_custom_bbcodes'))
		{
			include $phpbb_root_path . 'includes/functions_display.' . $php_ext;
		}

		/* Include files needed for displaying Smilies */
		if (!function_exists('generate_smilies'))
		{
			include $phpbb_root_path . 'includes/functions_posting.' . $php_ext;
		}

		/* Let's read the style configuration */
		$color_configs = json_decode($config_text->get('hlposts_colors'), true);

		/**
		 * Let's get the styles for this board
		 *
		 * If user style is overwritten, only get the default style, because...
		 * ... Replaces user’s (and guest’s) style with the style as defined under "Default style".
		 */
		$hlposts_where = ($config['override_user_style']) ? ' WHERE style_id = ' . (int) $config['default_style'] . '' : ' WHERE style_active = 1';
		$hlposts_order_by = ($config['override_user_style']) ? ' ' : ' ORDER BY style_id ASC';

		$sql = 'SELECT style_id, style_name
			FROM ' . STYLES_TABLE . '
			' . $hlposts_where . '
			' . $hlposts_order_by . '';
		$result = $db->sql_query($sql);
		$styles_rowset = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		/* Let's read the PMs configuration */
		$hlposts_pm_create = json_decode($config_text->get('hlposts_pm_create'), true);
		$hlposts_pm_edit = json_decode($config_text->get('hlposts_pm_edit'), true);
		$hlposts_pm_delete = json_decode($config_text->get('hlposts_pm_delete'), true);

		/* The main stuff */
		if ($request->is_set_post('update'))
		{
			/* Check the form key for security */
			if (!check_form_key('threedi/hlposts'))
			{
				$errors[] = $language->lang('FORM_INVALID');
			}

			if ($mode === 'settings')
			{
				$m_icon_post = $request->variable('hlposts_read_icon_tpl_p', '');
				$m_icon_mini = $request->variable('hlposts_read_icon_tpl_m', '');

				/* Minimum one template location for “marked read” icon */
				if (!$m_icon_post && !$m_icon_mini)
				{
					$errors[] = $language->lang('ACP_HLPOSTS_MIN_TPL_WRONG');
				}
			}

			if ($mode === 'pm_create' || $mode === 'pm_edit' || $mode === 'pm_delete')
			{
				$hlp_title1 = $request->variable('hlposts_pm_create_title', '');
				$hlp_title2 = $request->variable('hlposts_pm_edit_title', '');
				$hlp_title3 = $request->variable('hlposts_pm_delete_title', '');

				/* If empty title no way */
				if (empty($hlp_title1 || $hlp_title2 || $hlp_title3))
				{
					$errors[] = $language->lang('ACP_HLPOSTS_EMPTY_TITLE');
				}

				$hlp_msg1 = $request->variable('hlposts_pm_create_message', '');
				$hlp_msg2 = $request->variable('hlposts_pm_edit_message', '');
				$hlp_msg3 = $request->variable('hlposts_pm_delete_message', '');

				/* If empty message no way */
				if (empty($hlp_msg1 || $hlp_msg2 || $hlp_msg3))
				{
					$errors[] = $language->lang('ACP_HLPOSTS_EMPTY_MESSAGE');
				}
			}

			/* No errors? Great, let's go. */
			if (!count($errors))
			{
				if ($mode === 'settings')
				{
					/* (DOUBLE) Flood's time limit in minutes - 0 means disabled */
					$config->set('hlposts_mark_interval', $request->variable('hlposts_mark_interval', (double) $config['hlposts_mark_interval']));

					/* (BOOL) Display 'Marked read' confirmation message - Yes/No */
					$config->set('hlposts_mark_confirm_show', $request->variable('hlposts_mark_confirm_show', (int) $config['hlposts_mark_confirm_show']));

					/* (INT) Display time for 'Marked read' confirmation message - milliseconds */
					$config->set('hlposts_mark_confirm_time', $request->variable('hlposts_mark_confirm_time', (int) $config['hlposts_mark_confirm_time']));

					/* (BOOL) 'Marked read' icon visibility - Yes/No */
					$config->set('hlposts_read_icon_display', $request->variable('hlposts_read_icon_display', (int) $config['hlposts_read_icon_display']));

					/* (BOOL) 'Marked read' icon tpl location (posting area) */
					$config->set('hlposts_read_icon_tpl_p', $request->variable('hlposts_read_icon_tpl_p', (int) $config['hlposts_read_icon_tpl_p']));

					/* (BOOL) 'Marked read' icon tpl location (miniprofile) */
					$config->set('hlposts_read_icon_tpl_m', $request->variable('hlposts_read_icon_tpl_m', (int) $config['hlposts_read_icon_tpl_m']));

					/* (BOOL) Notification's default - Yes/No */
					$config->set('hlposts_notify_default', $request->variable('hlposts_notify_default', (int) $config['hlposts_notify_default']));

					/* (BOOL) PM's default - Yes/No */
					$config->set('hlposts_pm_default', $request->variable('hlposts_pm_default', (int) $config['hlposts_pm_default']));

					/* (STRING) PM's default - Signature */
					$config->set('hlposts_pm_sig', $request->variable('hlposts_pm_sig', (string) $config['hlposts_pm_sig'], true));

					/* (INT) User ID of the Bot */
					$config->set('hlposts_bot', $request->variable('hlposts_bot', (int) $config['hlposts_bot']));

					/* (BOOL) Use of the Bot - Yes/No */
					$config->set('hlposts_use_bot', $request->variable('hlposts_use_bot', (int) $config['hlposts_use_bot']));

					/* (INT) Users per pagination's page - 0 means disabled */
					$config->set('hlposts_users_page', $request->variable('hlposts_users_page', (int) $config['hlposts_users_page']));

					/* (BOOL) Post overview - Yes/No */
					$config->set('hlposts_post_overview', $request->variable('hlposts_post_overview', (int) $config['hlposts_post_overview']));

					/* Styles' specific color vars */
					$color_configs = $request->variable('color_configs', $color_configs);
					$config_text->set('hlposts_colors', json_encode($color_configs));
				}

				if ($mode === 'pm_create')
				{
					/* PMs' specific vars */
					$hlposts_pm_create['title'] = $request->variable('hlposts_pm_create_title', $hlposts_pm_create['title'], true);
					$hlposts_pm_create['message'] = $request->variable('hlposts_pm_create_message', $hlposts_pm_create['message'], true);
					$config_text->set('hlposts_pm_create', json_encode($hlposts_pm_create));
				}

				if ($mode === 'pm_edit')
				{
					$hlposts_pm_edit['title'] = $request->variable('hlposts_pm_edit_title', $hlposts_pm_edit['title'], true);
					$hlposts_pm_edit['message'] = $request->variable('hlposts_pm_edit_message', $hlposts_pm_edit['message'], true);
					$config_text->set('hlposts_pm_edit', json_encode($hlposts_pm_edit));
				}

				if ($mode === 'pm_delete')
				{
					$hlposts_pm_delete['title'] = $request->variable('hlposts_pm_delete_title', $hlposts_pm_delete['title'], true);
					$hlposts_pm_delete['message'] = $request->variable('hlposts_pm_delete_message', $hlposts_pm_delete['message'], true);
					$config_text->set('hlposts_pm_delete', json_encode($hlposts_pm_delete));
				}

				/* Log the action. */
				$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'ACP_HLPOSTS_LOG_' . strtoupper($mode) . '_SAVED', false, array());

				/* Succes, give them a back-link */
				trigger_error($language->lang('ACP_HLPOSTS_SETTINGS_SAVED') . adm_back_link($this->u_action));
			}
		}

		/**
		 * Simply copied from phpBB's compose PM
		  *
		 * @var \phpbb\controller\helper $controller_helper
		 */
		$controller_helper = $phpbb_container->get('controller.helper');

		$bbcode_status	= ($config['allow_bbcode'] && $config['auth_bbcode_pm'] && $auth->acl_get('u_pm_bbcode')) ? true : false;
		$smilies_status	= ($config['allow_smilies'] && $config['auth_smilies_pm'] && $auth->acl_get('u_pm_smilies')) ? true : false;
		$img_status		= ($config['auth_img_pm'] && $auth->acl_get('u_pm_img')) ? true : false;
		$flash_status	= ($config['auth_flash_pm'] && $auth->acl_get('u_pm_flash')) ? true : false;
		$url_status		= ($config['allow_post_links']) ? true : false;

		/* Check if we're previewing PM's */
		$preview = $request->is_set_post('preview');

		if ($preview)
		{
			/* Add the UCP language */
			$user->add_lang('ucp');

			/* Get the text formatters */
			$renderer	= $phpbb_container->get('text_formatter.renderer');
			$parser		= $phpbb_container->get('text_formatter.parser');
			$utils		= $phpbb_container->get('text_formatter.utils');

			/* Request the variables - here we need htmlspecialchars_decode */
			$preview_ttl	= htmlspecialchars_decode($request->variable('hlposts_' . $mode . '_title', '', true), ENT_COMPAT);
			$preview_msg	= htmlspecialchars_decode($request->variable('hlposts_' . $mode . '_message', '', true), ENT_COMPAT);

			/* Set up parser settings */
			$bbcode_status ? $parser->enable_bbcodes() : $parser->disable_bbcodes();
			$smilies_status ? $parser->enable_smilies() : $parser->disable_smilies();
			$img_status ? $parser->enable_bbcode('img') : $parser->disable_bbcode('img');
			$flash_status ? $parser->enable_bbcode('flash') : $parser->disable_bbcode('flash');
			$url_status ? $parser->enable_magic_url() : $parser->disable_magic_url();

			/* Parse the message */
			$preview_message = $parser->parse($preview_msg);

			/* Set up unparsed message for edit */
			$preview_message_edit = $utils->unparse($preview_message);

			/* Set up rendered message for display */
			$preview_message_show = $renderer->render($preview_message);

			/* Set up sender's username strings */
			$sender_name = get_username_string('username', $user->data['user_id'], $user->data['username'], $user->data['user_colour']);
			$sender_full = get_username_string('full', $user->data['user_id'], $user->data['username'], $user->data['user_colour']);

			/* Set up tokens */
			$tokens = array(
				'{SENDER_NAME}',
				'{SENDER_NAME_FULL}',
				'{SIG}',
			);

			/* Set up token replacements */
			$token_replacements = array(
				$sender_name,
				$sender_full,
				htmlspecialchars_decode($config['hlposts_pm_sig'], ENT_COMPAT),
			);

			/* Replace tokens */
			$preview_title	= str_replace(array($tokens[0], $tokens[1]), array($sender_name, $sender_name), $preview_ttl);
			$preview_text	= str_replace($tokens, $token_replacements, $preview_message_show);

			if ($user->data['user_sig'])
			{
				$parse_flags = ($user->data['user_sig_bbcode_bitfield'] ? OPTION_FLAG_BBCODE : 0) | OPTION_FLAG_SMILIES;
				$preview_sig = generate_text_for_display($user->data['user_sig'], $user->data['user_sig_bbcode_uid'], $user->data['user_sig_bbcode_bitfield'], $parse_flags, true);
			}
			else
			{
				$preview_sig = false;
			}
		}

		/* Guess what.. */
		$template->assign_vars(array(
			// Common variables
			'S_ERRORS'						=> ($errors) ? true : false,
			'ERRORS_MSG'					=> implode('<br /><br />', $errors),
			'U_ACTION'						=> $this->u_action,
			'HLPOSTS_MODE'					=> $mode,
			// Flood interval
			'HLPOSTS_MARK_INTERVAL'			=> (double) $config['hlposts_mark_interval'],
			// Display 'marked read' confirmation message
			'HLPOSTS_MARK_CONFIRM_SHOW'		=> (bool) $config['hlposts_mark_confirm_show'],
			// Display time 'marked read' confirmmation message
			'HLPOSTS_MARK_CONFIRM_TIME'		=> (int) $config['hlposts_mark_confirm_time'],
			// Display 'post read' icon'
			'HLPOSTS_READ_ICON_DISPLAY'		=> (bool) $config['hlposts_read_icon_display'],
			// Location of 'post read' icon' (P = posting area - M = miniprofile)
			'HLPOSTS_READ_ICON_TPL_P'		=> (bool) $config['hlposts_read_icon_tpl_p'],
			'HLPOSTS_READ_ICON_TPL_M'		=> (bool) $config['hlposts_read_icon_tpl_m'],
			// Notifications & PMs default
			'HLPOSTS_NOTIFY_DEFAULT'		=> (bool) $config['hlposts_notify_default'],
			'HLPOSTS_PM_DEFAULT'			=> (bool) $config['hlposts_pm_default'],
			'HLPOSTS_PM_SIG'				=> htmlspecialchars_decode($config['hlposts_pm_sig'], ENT_COMPAT),
			// PMS Bot
			'HLPOSTS_BOT'					=> $hlposts_utils->hlposts_bot_select($config['hlposts_bot']),
			'HLPOSTS_USE_BOT'				=> (bool) $config['hlposts_use_bot'],
			// Style configuration
			'HLPOSTS_STYLES'				=> $styles_rowset,
			'HLPOSTS_COLORS'				=> $color_configs,
			// Number of users to show at once in the "view who marked page"
			'HLPOSTS_USERS_PAGE'			=> (int) $config['hlposts_users_page'],
			// Show the highlighted post in the "view who marked page"
			'HLPOSTS_POST_OVERVIEW'			=> (bool) $config['hlposts_post_overview'],

			// PMs
			'U_MORE_SMILIES'				=> append_sid("{$phpbb_root_path}posting.$php_ext", 'mode=smilies'),

			'BBCODE_STATUS'					=> $language->lang(($bbcode_status ? 'BBCODE_IS_ON' : 'BBCODE_IS_OFF'), '<a href="' . $controller_helper->route('phpbb_help_bbcode_controller') . '">', '</a>'),
			'IMG_STATUS'					=> ($img_status) ? $language->lang('IMAGES_ARE_ON') : $language->lang('IMAGES_ARE_OFF'),
			'FLASH_STATUS'					=> ($flash_status) ? $language->lang('FLASH_IS_ON') : $language->lang('FLASH_IS_OFF'),
			'SMILIES_STATUS'				=> ($smilies_status) ? $language->lang('SMILIES_ARE_ON') : $language->lang('SMILIES_ARE_OFF'),
			'URL_STATUS'					=> ($url_status) ? $language->lang('URL_IS_ON') : $language->lang('URL_IS_OFF'),

			'S_BBCODE_ALLOWED'				=> ($bbcode_status) ? 1 : 0,
			'S_SMILIES_ALLOWED'				=> $smilies_status,
			'S_LINKS_ALLOWED'				=> $url_status,
			'S_SHOW_SMILEY_LINK'			=> true,
			'S_BBCODE_IMG'					=> $img_status,
			'S_BBCODE_FLASH'				=> $flash_status,
			'S_BBCODE_QUOTE'				=> true,
			'S_BBCODE_URL'					=> $url_status,

			'HLPOSTS_PM_CREATE_TITLE'		=> $preview ? $preview_ttl : htmlspecialchars_decode($hlposts_pm_create['title'], ENT_COMPAT),
			'HLPOSTS_PM_CREATE_MESSAGE'		=> $preview ? $preview_message_edit : htmlspecialchars_decode($hlposts_pm_create['message'], ENT_COMPAT),
			'HLPOSTS_PM_EDIT_TITLE'			=> $preview ? $preview_ttl : htmlspecialchars_decode($hlposts_pm_edit['title'], ENT_COMPAT),
			'HLPOSTS_PM_EDIT_MESSAGE'		=> $preview ? $preview_message_edit : htmlspecialchars_decode($hlposts_pm_edit['message'], ENT_COMPAT),
			'HLPOSTS_PM_DELETE_TITLE'		=> $preview ? $preview_ttl : htmlspecialchars_decode($hlposts_pm_delete['title'], ENT_COMPAT),
			'HLPOSTS_PM_DELETE_MESSAGE'		=> $preview ? $preview_message_edit : htmlspecialchars_decode($hlposts_pm_delete['message'], ENT_COMPAT),

			'HLPOSTS_PM_PREVIEW_SIG'		=> $preview ? $preview_sig : '',
			'HLPOSTS_PM_PREVIEW_TEXT'		=> $preview ? $preview_text : '',
			'HLPOSTS_PM_PREVIEW_TITLE'		=> $preview ? $preview_title : '',
			'HLPOSTS_PM_PREVIEW_TIME'		=> $user->format_date(time()),
			'HLPOSTS_PM_PREVIEW_USER'		=> $preview ? $sender_full : '',
			'S_HLPOSTS_PM_PREVIEW'			=> (bool) $preview,
		));

		/* Assign tokens array loop */
		$tokens_ary = $language->lang_raw('hlposts_tokens');

		foreach ($tokens_ary as $token => $explain)
		{
			$template->assign_block_vars('hlposts_tokens', array(
				'TOKEN'		=> '{' . $token . '}',
				'EXPLAIN'	=> $explain,
			));
		}

		/* Build custom bbcodes array*/
		display_custom_bbcodes();

		/* Build smilies */
		generate_smilies('inline', 0);
	}
}
