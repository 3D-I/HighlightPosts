<?php
/**
 *
 * Highlight Posts. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, 3Di & Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace threedi\hlposts\core;

/**
 * Highlight Posts helper service.
 */
class operator
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\group\helper */
	protected $group_helper;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string PHP extension */
	protected $php_ext;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth					$auth			Authentication object
	 * @param \phpbb\config\config				$config			Configuration object
	 * @param \phpbb\config\db_text				$config_text
	 * @param \phpbb\db\driver\driver_interface	$db				Database object
	 * @param \phpbb\group\helper				$group_helper	Group helper object
	 * @param \phpbb\language\language			$language		Language object
	 * @param \phpbb\user						$user			User object
	 * @param \phpbb\template\template			$template		Template object
	 * @access public
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\config\db_text $config_text, \phpbb\db\driver\driver_interface $db, \phpbb\group\helper $group_helper, \phpbb\language\language $lang, \phpbb\user $user, \phpbb\template\template $template, $root_path, $php_ext)
	{
		$this->auth			=	$auth;
		$this->config		=	$config;
		$this->config_text	=	$config_text;
		$this->db			=	$db;
		$this->group_helper	=	$group_helper;
		$this->lang			=	$lang;
		$this->user			=	$user;
		$this->template		=	$template;

		$this->root_path	=	$root_path;
		$this->php_ext		=	$php_ext;
	}

	/**
	 * Returns whether the user is authed
	 *
	 * @return bool
	 * @access public
	 */
	public function is_authed()
	{
		return (bool) ($this->auth->acl_get('u_allow_hlposts_view') || $this->auth->acl_get('a_hlposts_admin') || $this->auth->acl_get('m_hlposts_can_view_read'));
	}

	/**
	 * Template switches over viewtopic and the "view" page
	 *
	 * @return void
	 * @access public
	 */
	public function template_switches_over_viewtopic()
	{
		/* Let's read the style configuration */
		$color_configs = json_decode($this->config_text->get('hlposts_colors'), true);

		/* Fallback for "style overriden"'s roll backs */
		$color_configs_style_default = (int) $this->config['default_style'];

		$this->template->assign_vars(array(
			/*
			 * CSS highlight editor colors
			 * Error suppression if the default style has not yet custom colours preset, fallback is the main CSS
			 */
			'HLPOSTS_BCKG'	=>	((bool) isset($color_configs[(int) $this->user->data['user_style']]['bckg'])) ? $color_configs[(int) $this->user->data['user_style']]['bckg'] : @$color_configs["{$color_configs_style_default}"]['bckg'],

			'HLPOSTS_TEXT'	=>	((bool) isset($color_configs[(int) $this->user->data['user_style']]['text'])) ? $color_configs[(int) $this->user->data['user_style']]['text'] : @$color_configs["{$color_configs_style_default}"]['text'],

			'HLPOSTS_BORD'	=>	((bool) isset($color_configs[(int) $this->user->data['user_style']]['bord'])) ? $color_configs[(int) $this->user->data['user_style']]['bord'] : @$color_configs["{$color_configs_style_default}"]['bord'],
		));
	}

	/**
	 * Template switches over all
	 *
	 * @return void
	 * @access public
	 */
	public function template_switches_over_all()
	{
		$this->template->assign_vars(array(
			/* Auths */
			'S_HLPOSTS'						=>	(bool) $this->is_authed(),
			/* Display 'post marked read' icon */
			'S_HLPOSTS_READ_ICON_DISPLAY'	=>	(bool) $this->config['hlposts_read_icon_display'],
			/* Location of 'post read' icon' (P = posting area - M = miniprofile) */
			'S_HLPOSTS_READ_ICON_TPL_POST'	=>	(bool) $this->config['hlposts_read_icon_tpl_p'],
			'S_HLPOSTS_READ_ICON_TPL_MINI'	=>	(bool) $this->config['hlposts_read_icon_tpl_m'],
			/* Notifications */
			'HLPOSTS_NOTIFY_BOARD'			=>	(bool) $this->config['hlposts_notify_default'],
			'HLPOSTS_NOTIFY_PM'				=>	(bool) $this->config['hlposts_pm_default'],
		));
	}

	/**
	 * Read the PM configuration and return the array
	 *
	 * @return	array
	 * @access public
	 */
	public function json_decode_pm_create()
	{
		return json_decode($this->config_text->get('hlposts_pm_create'), true);
	}

	/**
	 * Read the PM configuration and return the array
	 *
	 * @return	array
	 * @access public
	 */
	public function json_decode_pm_edit()
	{
		return json_decode($this->config_text->get('hlposts_pm_edit'), true);
	}

	/**
	 * Read the PM configuration and return the array
	 *
	 * @return	array
	 * @access public
	 */
	public function json_decode_pm_delete()
	{
		return json_decode($this->config_text->get('hlposts_pm_delete'), true);
	}

	/**
	 * Tokens to be replaced with configurated values
	 *
	 * @return	array
	 * @access public
	 */
	public function hlposts_tokens()
	{
		/* Map arguments for tokens */
		return $hlposts_tokens = array(
			'{SENDER_NAME_FULL}',
			'{SENDER_NAME}',
			'{RECIP_NAME_FULL}',
			'{RECIP_NAME}',
			'{SIG}',
			'{P_LINK}',
		);
	}

	/**
	 * Replacement values for tokens
	 *
	 * @return	array
	 * @access public
	 */
	public function hlposts_tokens_replacements($sen_uname_full, $sen_uname, $rec_uname_full, $rec_uname, $pm_sig, $post_url)
	{
		/* Map arguments for replacement */
		return $hlposts_tokens_replacements = array(
			$sen_uname_full,
			$sen_uname,
			$rec_uname_full,
			$rec_uname,
			$pm_sig,
			$post_url,
		);
	}

	/**
	 * Executes the main SQL query, called on request
	 *
	 * @return	array	$row	user data
	 * @access public
	 */
	public function hlposts_sql_users($user_id)
	{
		$sql = 'SELECT user_id, user_ip, username, user_colour
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . (int) $user_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $row;
	}

	/**
	 * Mimic get_username_string() due to the new parser
	 *
	 * Param	int				$user_id	The user ID
	 * @return	array|string				Array of strings formatted with BBcode markup
	 * @access public
	 */
	public function hlposts_get_username_string($user_id)
	{
		/* Call the main SQL query */
		$row_user = $this->hlposts_sql_users($user_id);

		$username_flat = $row_user['username'];

		/**
		* That's for Rhea - Mimic get_username_string due to the new parser
		*/
		if ($this->auth->acl_get('u_viewprofile'))
		{
			$url_profile = generate_board_url() . '/memberlist.' . $this->php_ext . '?mode=viewprofile&u=' . $row_user['user_id'];
			$color = $row_user['user_colour'] ? '[color=#' . $row_user['user_colour'] . ']' . $row_user['username'] . '[/color]' : $row_user['username'];
			$username_full = '[b]' . '[url=' . $url_profile . ']' . $color . '[/url]' . '[/b]';
		}
		else
		{
			$username_full = $row_user['username'];
		}

		return array($username_full, $username_flat);
	}

	/**
	 * Send PM
	 *
	 * @param	int		$user_id		The user ID of the sender
	 * @param	int		$user_id_to		The user ID of the recipient
	 * @param	string	$pm_title		Passed via config_text
	 * @param	string	$pm_message		Passed via config_text
	 * @param	int		$forum_id		The forum ID
	 * @param	int		$post_id		The post ID
	 * @return	void
	 * @access	public
	 */
	public function send_pm($user_id, $user_id_to, $pm_title, $pm_message, $forum_id, $post_id)
	{
		if (!function_exists('generate_text_for_storage'))
		{
			include($this->phpbb_root_path . 'includes/functions_content.' . $this->php_ext);
		}

		if (!function_exists('submit_pm'))
		{
			include($this->root_path . 'includes/functions_privmsgs.' . $this->php_ext);
		}

		$post_url = append_sid(generate_board_url() . '/viewtopic.' . $this->php_ext, "f={$forum_id}&amp;p={$post_id}#p{$post_id}", false);

		/* Call the mimic */
		list($sen_uname_full, $sen_uname) = $this->hlposts_get_username_string($user_id);
		list($rec_uname_full, $rec_uname) = $this->hlposts_get_username_string($user_id_to);

		/* PMs signature if any, set in ACP/settings */
		$pm_sig = htmlspecialchars_decode($this->config['hlposts_pm_sig'], ENT_COMPAT);

		/**
		 * Prepare the PM and tokens
		 */
		$pm_title = htmlspecialchars_decode($pm_title, ENT_COMPAT);
		$pm_title = str_replace($this->hlposts_tokens(), $this->hlposts_tokens_replacements($sen_uname, $sen_uname, $rec_uname, $rec_uname, false, false), $pm_title);
		$uid_pm = $bitfield_pm = $options_pm = '';
		$allow_bbcode_pm = $allow_urls_pm = $allow_smilies_pm = false;
		generate_text_for_storage($pm_title, $uid_pm, $bitfield_pm, $options_pm, $allow_bbcode_pm, $allow_urls_pm, $allow_smilies_pm);

		$pm_message = htmlspecialchars_decode($pm_message, ENT_COMPAT);
		$pm_message = str_replace($this->hlposts_tokens(), $this->hlposts_tokens_replacements($sen_uname_full, $sen_uname, $rec_uname_full, $rec_uname, $pm_sig, $post_url), $pm_message);
		$uid = $bitfield = '';
		$m_flags = 3;
		$allow_bbcode = $allow_urls = $allow_smilies = true;
		generate_text_for_storage($pm_message, $uid, $bitfield, $m_flags, $allow_bbcode, $allow_urls, $allow_smilies);

		/* Call the main SQL query */
		$row_data = $this->hlposts_sql_users($user_id);

		/* Are we using the PMs Bot? */
		$hlposts_from_user_id = ((bool) $this->config['hlposts_use_bot']) ? (int) $this->config['hlposts_bot'] : (int) $row_data['user_id'];

		$pm_data = array(
			'address_list'		=> array('u' => array((int) $user_id_to => 'to')),
			'from_user_id'		=> (int) $hlposts_from_user_id,
			'from_user_ip'		=> $row_data['user_ip'],
			'enable_sig'		=> true,
			'enable_bbcode'		=> $allow_bbcode,
			'enable_smilies'	=> $allow_smilies,
			'enable_urls'		=> $allow_urls,
			'icon_id'			=> false,
			'bbcode_bitfield'	=> $bitfield,
			'bbcode_uid'		=> $uid,
			'message'			=> $pm_message,
		);

		/**
		 * We do not want the sent PMs to be stored into the User's PM's outbox or sentbox
		 */
		submit_pm('post', $pm_title, $pm_data, false);
	}

	/**
	 * Check if Highlight Posts is enabled for this forum
	 *
	 * @param  int	$forum_id
	 * @return bool
	 * @access public
	 */
	public function forum_enabled($forum_id)
	{
		$sql = 'SELECT forum_id
			FROM ' . FORUMS_TABLE . '
			WHERE hlposts_f_enable = ' . true . '
				AND forum_id = ' . (int) $forum_id;
		$result = $this->db->sql_query($sql);
		$forum = $this->db->sql_fetchfield('forum_id');
		$this->db->sql_freeresult($result);

		return (bool) $forum;
	}

	/**
	 * Return the HTML list of users from where to chose the Bot
	 *
	 * @param  int		$hlposts_u_id	The user ID
	 * @return string	$allowed_users	HTML
	 * @access public
	 */
	public function pms_bot_selector($hlposts_u_id = false)
	{
		$allowed_users = '';

		/* No BOTs or Inactive users */
		$ignore_list = array(USER_IGNORE, USER_INACTIVE);

		$sql = 'SELECT user_id, username, username_clean
			FROM ' . USERS_TABLE . '
			WHERE ' . $this->db->sql_in_set('user_type', $ignore_list, true, true) . '
				AND user_id <> ' . ANONYMOUS . '
			ORDER BY username_clean';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$selected = ((int) $row['user_id'] == (int) $hlposts_u_id) ? ' selected="selected"' : '';
			$allowed_users .= '<option value="' . $row['user_id'] . '"' . $selected . '>' . $row['username'] . '</option>';
		}
		$this->db->sql_freeresult($result);

		return $allowed_users;
	}

	/**
	 * The drop down for the Bot's selection
	 *
	 * @param  int		$hlposts_bot_id		The Bot ID
	 * @return string	selector			HTML
	 * @access public
	 */
	public function hlposts_bot_select($hlposts_bot_id)
	{
		return '<select id="hlposts_bot" name="hlposts_bot">' . $this->pms_bot_selector($hlposts_bot_id) . '</select>';
	}

	/**
	 * Get all groups for display in a select box
	 *
	 * @param  int			$group_selected		The already selected group id, if any
	 * @return string		$s_group_select		Formatted string for options display
	 * @access private
	 */
	public function get_groups_for_select($group_selected)
	{
		$s_group_select = '<option value="0"' . ((!$group_selected) ? ' selected="selected"' : '') . '>&nbsp;</option>';
		$group_ids = array();

		if ($this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
		{
			$sql = 'SELECT group_id, group_name, group_type
				FROM ' . GROUPS_TABLE;
			if (!$this->config['coppa_enable'])
			{
				$sql .= " WHERE group_name <> 'REGISTERED_COPPA'";
			}
			$sql .= ' ORDER BY group_name ASC';
		}
		else
		{
			$sql = 'SELECT g.group_id, g.group_name, g.group_type
					FROM ' . GROUPS_TABLE . ' g
					LEFT JOIN ' . USER_GROUP_TABLE . ' ug
						ON (
							g.group_id = ug.group_id
							AND ug.user_id = ' . $user->data['user_id'] . '
							AND ug.user_pending = 0
						)
					WHERE (g.group_type <> ' . GROUP_HIDDEN . ' OR ug.user_id = ' . (int) $user->data['user_id'] . ')';
			if (!$this->config['coppa_enable'])
			{
				$sql .= " AND g.group_name <> 'REGISTERED_COPPA'";
			}
			$sql .= ' ORDER BY g.group_name ASC';
		}
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$group_ids[] = $row['group_id'];
			$s_group_select .= '<option value="' . $row['group_id'] . '"' . (($group_selected == $row['group_id']) ? ' selected="selected"' : '') . '>' . $this->group_helper->get_name($row['group_name']) . '</option>';
		}
		$this->db->sql_freeresult($result);

		if ($group_selected !== 0 && !in_array($group_selected, $group_ids))
		{
			trigger_error($this->lang->lang('HLPOSTS_ERROR_NO_GROUP'), E_USER_WARNING);
		}

		return $s_group_select;
	}
}
