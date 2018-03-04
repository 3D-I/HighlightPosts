<?php
/**
 *
 * Highlight Posts. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, 3Di & Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace threedi\hlposts\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Highlight Posts Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.viewtopic_assign_template_vars_before'	=> 'hlposts_template_switches_vt',
			'core.viewtopic_get_post_data'					=> 'viewtopic_get_post_data',
			'core.viewtopic_post_rowset_data'				=> 'viewtopic_post_rowset_data',
			'core.viewtopic_modify_post_row'				=> 'viewtopic_modify_post_row',
			'core.viewtopic_highlight_modify'				=> 'viewtopic_highlight_modify',
			'core.permissions'								=> 'add_permission',
			'core.page_header_after'						=> 'hlposts_template_switches',
			'core.acp_manage_forums_request_data'			=> 'acp_manage_forums_request_data',
			'core.acp_manage_forums_initialise_data'		=> 'acp_manage_forums_initialise_data',
			'core.acp_manage_forums_display_form'			=> 'acp_manage_forums_display_form',
		);
	}

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var \phpbb\textformatter\s9e\renderer */
	protected $renderer;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\user */
	protected $user;

	/* @var \threedi\hlposts\core\operator */
	protected $hlposts_utils;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string .php File extension */
	protected $php_ext;

	/** @var string Post read table */
	protected $post_read_table;

	/** @var string Highlights table */
	protected $highlights_table;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth						$auth				Authentication object
	 * @param \phpbb\config\config					$config				Configuration object
	 * @param \phpbb\db\driver\driver_interface		$db					Database object
	 * @param \phpbb\controller\helper				$helper				Controller helper object
	 * @param \phpbb\language\language				$lang				Language object
	 * @param \phpbb\textformatter\s9e\renderer		$renderer			Textformatter renderer object
	 * @param \phpbb\request\request				$request			Request object
	 * @param \phpbb\user							$user				User object
	 * @param \threedi\hlposts\core\operator		$hlpost_utils		Highlight Posts utilities
	 * @param string								$root_path			phpBB root path
	 * @param string								$php_ext			.php File extension
	 * @param string								$post_read_table	Highlight Posts post read table
	 * @param string								$highlights_table	Highlight Posts highlights table
	 * @access public
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, \phpbb\language\language $lang, \phpbb\textformatter\s9e\renderer $renderer, \phpbb\request\request $request, \phpbb\user $user, \threedi\hlposts\core\operator $hlposts_utils, $root_path, $php_ext, $post_read_table, $highlights_table)
	{
		$this->auth				= $auth;
		$this->config			= $config;
		$this->db				= $db;
		$this->helper			= $helper;
		$this->lang				= $lang;
		$this->renderer			= $renderer;
		$this->request			= $request;
		$this->user				= $user;
		$this->hlposts_utils	= $hlposts_utils;

		$this->root_path		= $root_path;
		$this->php_ext			= $php_ext;

		$this->post_read_table	= $post_read_table;
		$this->highlights_table = $highlights_table;
	}

	/**
	 * Template switches over viewtopic
	 *
	 * @event	core.viewtopic_assign_template_vars_before
	 * @return	void
	 * @access	public
	 */
	public function hlposts_template_switches_vt()
	{
		/**
		 * Check perms first
		 */
		if ($this->hlposts_utils->is_authed())
		{
			$this->hlposts_utils->template_switches_over_viewtopic();
		}
	}

	/**
	 * Add to the SQL to grab the highlight data.
	 *
	 * @param	\phpbb\event\data		$event		Event object
	 * @event	core.viewtopic_get_post_data
	 * @return	void
	 * @access	public
	 */
	public function viewtopic_get_post_data($event)
	{
		$sql_ary = $event['sql_ary'];
		$topic_data = $event['topic_data'];
		$hlposts_f_enabled = $topic_data['hlposts_f_enable'];

		/* If Highlight Posts is enabled for this forum, we add the Highlight tables */
		if ($hlposts_f_enabled)
		{
			$sql_ary['SELECT'] .= ', hl.hl_text, hl.hl_time, hl.hl_user_id, hl.hl_username, hl.hl_user_colour, pr.read_time';

			$sql_ary['LEFT_JOIN'][] = array(
					'FROM'	=> array($this->highlights_table => 'hl'),
					'ON'	=> 'p.post_id = hl.post_id',
			);

			$sql_ary['LEFT_JOIN'][] = array(
					'FROM'	=> array($this->post_read_table => 'pr'),
					'ON'	=> 'p.post_id = pr.post_id AND pr.user_id = ' . (int) $this->user->data['user_id'],
			);

			$event['sql_ary'] = $sql_ary;
		}
	}

	/**
	 * Add the highlight data to the rowset
	 *
	 * @param	\phpbb\event\data		$event		Event object
	 * @event	core.viewtopic_post_rowset_data
	 * @return	void
	 * @access	public
	 */
	public function viewtopic_post_rowset_data($event)
	{
		$rowset_data = $event['rowset_data'];
		$row = $event['row'];

		$rowset_data['highlight_text'] = !empty($row['hl_text']) ? $row['hl_text'] : '';
		$rowset_data['highlight_time'] = !empty($row['hl_time']) ? $row['hl_time'] : '';
		$rowset_data['highlight_user_id'] = !empty($row['hl_user_id']) ? $row['hl_user_id'] : '';
		$rowset_data['highlight_username'] = !empty($row['hl_username']) ? $row['hl_username'] : '';
		$rowset_data['highlight_user_colour'] = !empty($row['hl_user_colour']) ? $row['hl_user_colour'] : '';

		$rowset_data['is_highlighted'] = !empty($row['is_highlighted']) ? $row['is_highlighted'] : false;

		$rowset_data['post_read_time'] = !empty($row['read_time']) ? $row['read_time'] : 0;

		$event['rowset_data'] = $rowset_data;
	}

	/**
	 * Assign the rowset to the postrow data
	 *
	 * @param	\phpbb\event\data		$event		Event object
	 * @event	core.viewtopic_modify_post_row
	 * @return	void
	 * @access	public
	 */
	public function viewtopic_modify_post_row($event)
	{
		/* The event parameters */
		$topic_data	= $event['topic_data'];
		$poster_id	= $event['poster_id'];
		$post_row	= $event['post_row'];
		$row		= $event['row'];

		$hlposts_f_enabled = $topic_data['hlposts_f_enable'] ? true : false;

		$post_row['HLPOSTS_F_ENABLED'] = $hlposts_f_enabled;

		if ($hlposts_f_enabled)
		{
			$post_row['HIGHLIGHT_TEXT'] = !empty($row['highlight_text']) ? $this->renderer->render($row['highlight_text']) : '';
			$post_row['HIGHLIGHT_TIME'] = !empty($row['highlight_time']) ? $this->user->format_date($row['highlight_time']) : '';
			$post_row['HIGHLIGHT_USER'] = !empty($row['highlight_user_id']) ? get_username_string('full', $row['highlight_user_id'], $row['highlight_username'], $row['highlight_user_colour']) : '';

			$post_row['S_CAN_HIGHLIGHT']	= (bool) $this->auth->acl_get('m_hlposts_can_highlight');
			$post_row['S_HIGHLIGHTED']		= !empty($row['is_highlighted']) ? $row['is_highlighted'] : false;

			$post_row['U_HIGHLIGHT_ADD']	= $this->helper->route('threedi_hlposts_controller', array('action' => 'add', 'post_id' => $row['post_id'], 'topic_id' => $row['topic_id'], 'forum_id' => $row['forum_id'], 'author_id' => $poster_id));
			$post_row['U_HIGHLIGHT_DELETE']	= $this->helper->route('threedi_hlposts_controller', array('action' => 'delete', 'post_id' => $row['post_id'], 'topic_id' => $row['topic_id'], 'forum_id' => $row['forum_id'], 'author_id' => $poster_id));
			$post_row['U_HIGHLIGHT_EDIT']	= $this->helper->route('threedi_hlposts_controller', array('action' => 'edit', 'post_id' => $row['post_id'], 'topic_id' => $row['topic_id'], 'forum_id' => $row['forum_id'], 'author_id' => $poster_id));

			$post_row['S_CAN_VIEW_READ']	= $this->auth->acl_get('m_hlposts_can_view_read');
			$post_row['S_POST_BEEN_READ']	= !empty($row['post_read_time']) ? true : false;
			$post_row['U_MARK_POST_READ']	= ($this->user->data['is_registered']) ? append_sid("{$this->root_path}viewtopic.{$this->php_ext}", 'p=' . $row['post_id'] . '&amp;hash=' . generate_link_hash('global') . '&amp;hl_mark=read') : '';
			$post_row['U_VIEW_POST_READ']	= $this->helper->route('threedi_hlposts_view', array('forum_id' => (int) $row['forum_id'], 'post_id' => (int) $row['post_id']));
		}

		$event['post_row'] = $post_row;
	}

	/**
	 * Add the 'read'-mark for a post
	 *
	 * @param	\phpbb\event\data		$event		The event object
	 * @event	core.viewtopic_highlight_modify
	 * @return	\phpbb\json_response
	 * @access	public
	 */
	public function viewtopic_highlight_modify($event)
	{
		$topic_data = $event['topic_data'];
		$hlposts_f_enabled = $topic_data['hlposts_f_enable'];

		$hl_mark = $this->request->variable('hl_mark', '');

		if (!empty($hl_mark) && $this->request->is_ajax())
		{
			/* Set up a json response */
			$json_response = new \phpbb\json_response();

			/* Check if Highlight Posts has been disabled for this forum */
			if (empty($hlposts_f_enabled))
			{
				$json_response->send(array(
					'MESSAGE_TITLE'	=> $this->lang->lang('INFORMATION'),
					'MESSAGE_TEXT'	=> $this->lang->lang('HLPOSTS_ERROR_DISABLED'),
				));
			}

			if (check_link_hash($this->request->variable('hash', ''), 'global'))
			{
				/* The variables */
				$read_array = array(
					'user_id'	=> (int) $this->user->data['user_id'],
					'post_id'	=> (int) $this->request->variable('p', 0),
					'read_time'	=> (int) time()
				);

				/* Grab the last read post by this user and check if it's beneath the mark flood interval. */
				$sql = 'SELECT read_time as last_read_time
						FROM ' . $this->post_read_table . '
						WHERE user_id = ' . (int) $read_array['user_id'];
				$result = $this->db->sql_query($sql);
				$last_read_time = (int) $this->db->sql_fetchfield('last_read_time');
				$this->db->sql_freeresult($result);

				/* Check if user even marked a post before and if the interval hasn't been disabled (Set to 0) */
				if (!empty($last_read_time) && ($this->config['hlposts_mark_interval'] !== 0))
				{
					/* If	current mark time	minus	last mark time	is less than	interval * 60 seconds */
					if (($read_array['read_time'] - $last_read_time) < ($this->config['hlposts_mark_interval'] * 60))
					{
						$json_response->send(array(
							'MESSAGE_TITLE'	=> $this->lang->lang('INFORMATION'),
							'MESSAGE_TEXT'	=> $this->lang->lang('HLPOSTS_POST_MARK_READ_INTERVAL'),
						));
					}
				}

				/* Update the database */
				$sql = 'INSERT INTO ' . $this->post_read_table . ' ' . $this->db->sql_build_array('INSERT', $read_array);
				$this->db->sql_query($sql);

				/* JSON Response array */
				$response_array = array(
					'POST_ID'				=> $read_array['post_id'],
					'READ_DISPLAY_BOOL'		=> $this->config['hlposts_read_icon_display'] ? true : false,
					'BACK2TOP_BOOL'			=> $this->config['hlposts_read_icon_tpl_m'] ? true : false,
					'POSTING_BUTTONS_BOOL'	=> $this->config['hlposts_read_icon_tpl_p'] ? true : false,
					'CONFIRM_DISPLAY'		=> $this->config['hlposts_mark_confirm_show'] ? true : false,
					'CONFIRM_DISPLAY_TIME'	=> $this->config['hlposts_mark_confirm_time'],
				);

				if ($this->config['hlposts_mark_confirm_show'])
				{
					$response_array['MESSAGE_TITLE'] = $this->lang->lang('INFORMATION');
					$response_array['MESSAGE_TEXT']  = $this->lang->lang('HLPOSTS_POST_MARKED_READ');
				}

				/* Send a response */
				$json_response->send($response_array);
			}
			else
			{
				$json_response->send(array(
					'MESSAGE_TITLE'	=> $this->lang->lang('INFORMATION'),
					'MESSAGE_TEXT'	=> $this->lang->lang('FORM_INVALID'),
				));
			}
		}
	}

	/**
	 * Add permissions for Highlight Posts
	 *
	 * @param	\phpbb\event\data		$event		The event object
	 * @event	core.permissions
	 * @return	void
	 * @access	public
	 */
	public function add_permission($event)
	{
		$permissions = $event['permissions'];

		$permissions += [
			'a_hlposts_admin' => [
				'lang'	=> 'ACL_A_HLPOSTS_ADMIN',
				'cat'	=> 'misc',
			],
			'm_hlposts_can_highlight' => [
				'lang'	=> 'ACL_M_HLPOSTS_CAN_HIGHLIGHT',
				'cat'	=> 'post_actions',
			],
			'm_hlposts_can_view_read' => [
				'lang'	=> 'ACL_M_HLPOSTS_CAN_VIEW_READ',
				'cat'	=> 'post_actions',
			],
			'u_allow_hlposts_view' => [
				'lang'	=> 'ACL_U_HLPOSTS_VIEW',
				'cat'	=> 'misc',
			],
		];

		$event['permissions'] = $permissions;
	}

	/**
	 * Template switches over all
	 *
	 * @event	core.page_header_after
	 * @return	mixed (bool/void)
	 * @access	public
	 */
	public function hlposts_template_switches()
	{
		/**
		 * Check perms first
		 */
		if ($this->hlposts_utils->is_authed())
		{
			$this->hlposts_utils->template_switches_over_all();
		}
	}

	/**
	 * (Add/update actions) - Submit form
	 *
	 * @event	core.acp_manage_forums_request_data
	 * @return	void
	 * @access	public
	 */
	public function acp_manage_forums_request_data($event)
	{
		$forum_data = $event['forum_data'];

		$forum_data['hlposts_f_enable'] = $this->request->variable('hlposts_f_enable', 0);

		$event['forum_data'] = $forum_data;
	}

	/**
	 * New Forums added (default enabled)
	 *
	 * @event	core.acp_manage_forums_initialise_data
	 * @return	void
	 * @access	public
	 */
	public function acp_manage_forums_initialise_data($event)
	{
		if ($event['action'] == 'add')
		{
			$forum_data = $event['forum_data'];

			$forum_data['hlposts_f_enable'] = (int) 1;

			$event['forum_data'] = $forum_data;
		}
	}

	/**
	 * ACP forums (template data)
	 *
	 * @event	core.acp_manage_forums_display_form
	 * @return	void
	 * @access	public
	 */
	public function acp_manage_forums_display_form($event)
	{
		$template_data = $event['template_data'];

		$template_data['S_HLPOSTS_F_ENABLE'] = $event['forum_data']['hlposts_f_enable'];

		$event['template_data'] = $template_data;
	}
}
