<?php
/**
 *
 * Highlight Posts. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, 3Di & Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace threedi\hlposts\controller;

/**
 * Highlight Posts Controller
 */
class main_controller
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\group\helper */
	protected $group_helper;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\notification\manager */
	protected $notification_manager;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\textformatter\s9e\parser */
	protected $parser;

	/** @var \phpbb\textformatter\s9e\renderer */
	protected $renderer;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\textformatter\s9e\utils */
	protected $utils;

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
	 * @param \phpbb\auth\auth					$auth					Authentication object
	 * @param \phpbb\config\config				$config					Configuration object
	 * @param \phpbb\db\driver\driver_interface	$db						Database object
	 * @param \phpbb\group\helper				$group_helper			Group helper object
	 * @param \phpbb\controller\helper			$helper					Controller helper object
	 * @param \phpbb\language\language			$language				Language object
	 * @param \phpbb\log\log					$log					Log object
	 * @param \phpbb\notification\manager		$notification_manager	Notification manager
	 * @param \phpbb\pagination					$pagination				Pagination object
	 * @param \phpbb\textformatter\s9e\parser	$parser					Textformatter parser object
	 * @param \phpbb\textformatter\s9e\renderer	$renderer				Textformatter renderer object
	 * @param \phpbb\request\request			$request				Request object
	 * @param \phpbb\template\template			$template				Template object
	 * @param \phpbb\user						$user					User object
	 * @param \phpbb\textformatter\s9e\utils	$utils					Textformatter utils object
	 * @param \threedi\hlposts\core\operator	$hlposts_utils			Functions to be used by Classes
	 * @param string							$root_path				phpBB root path
	 * @param string							$php_ext				.php file extension
	 * @param string							$post_read_table		Post read table
	 * @param string							$highlights_table		Highlights table
	 * @access public
	 */
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\group\helper $group_helper,
		\phpbb\controller\helper $helper,
		\phpbb\language\language $lang,
		\phpbb\log\log $log,
		\phpbb\notification\manager $notification_manager,
		\phpbb\pagination $pagination,
		\phpbb\textformatter\s9e\parser $parser,
		\phpbb\textformatter\s9e\renderer $renderer,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\textformatter\s9e\utils $utils,
		\threedi\hlposts\core\operator $hlposts_utils,
		$root_path,
		$php_ext,
		$post_read_table,
		$highlights_table)
	{
		$this->auth					= $auth;
		$this->config				= $config;
		$this->db					= $db;
		$this->group_helper			= $group_helper;
		$this->helper				= $helper;
		$this->lang					= $lang;
		$this->log					= $log;
		$this->notification_manager	= $notification_manager;
		$this->pagination			= $pagination;
		$this->parser				= $parser;
		$this->renderer				= $renderer;
		$this->request				= $request;
		$this->template				= $template;
		$this->user					= $user;
		$this->utils				= $utils;
		$this->hlposts_utils		= $hlposts_utils;

		$this->root_path			= $root_path;
		$this->php_ext				= $php_ext;

		$this->post_read_table		= $post_read_table;
		$this->highlights_table		= $highlights_table;
	}

	/**
	 * Handling any highlight action for a certain post
	 *
	 * @param	int		$forum_id		The forum id
	 * @param	int		$topic_id		The topic id
	 * @param	int		$post_id		The post id
	 * @param	string	$action			add, edit, delete
	 * @return	mixed	render page
	 * @access public
	 */
	public function handle($forum_id, $topic_id, $post_id, $author_id, $action)
	{
		/* Submit variables */
		$submit = $this->request->is_set_post('submit');
		$cancel = $this->request->is_set_post('cancel');
		$preview = $this->request->is_set_post('preview');

		/* Set up post URL and return message */
		$post_url = append_sid($this->root_path . 'viewtopic.' . $this->php_ext, "f={$forum_id}&amp;p={$post_id}#p{$post_id}", false);
		$return_msg = '<br /><br />' . $this->lang->lang('RETURN_PAGE', '<a href="' . $post_url . '">', '</a>');

		/* Check if Highlight Post is enabled for this forum */
		if (!$this->hlposts_utils->forum_enabled($forum_id))
		{
			return $this->helper->message($this->lang->lang('HLPOSTS_ERROR_DISABLED') . $return_msg);
		}

		/* If it's cancelled, we return to the post we were viewing. */
		if ($cancel)
		{
			redirect($post_url);
		}

		/* Check permission if user is allowed to highlight */
		if (!$this->auth->acl_get('m_hlposts_can_highlight'))
		{
			return $this->helper->message($this->lang->lang('HLPOSTS_ERROR_CAN_NOT'));
		}

		/* Set up parser settings */
		$this->config['allow_bbcode'] ? $this->parser->enable_bbcodes() : $this->parser->disable_bbcodes();
		$this->config['allow_smilies'] ? $this->parser->enable_smilies() : $this->parser->disable_smilies();
		$this->config['allow_post_links'] ? $this->parser->enable_magic_url() : $this->parser->disable_magic_url();

		/* Grab post data */
		$sql = 'SELECT p.post_subject, p.post_text, p.post_time, u.user_id, u.username, u.user_colour
		 		FROM ' . POSTS_TABLE . ' p
				JOIN ' . USERS_TABLE . ' u
				WHERE p.poster_id = u.user_id
					AND p.post_id = ' . (int) $post_id;
		$result = $this->db->sql_query($sql);
		$post = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		/* Request notification options, for all three (add, edit, delete) actions */
		$hlposts_notify_board = $this->request->variable('hlposts_notify_board', false);
		$hlposts_notify_pm = $this->request->variable('hlposts_notify_pm', false);

		/* Add the lang file needed by BBCodes */
		$this->lang->add_lang('posting');

		/* Include files needed for displaying BBCodes */
		if (!function_exists('display_custom_bbcodes'))
		{
			include $this->root_path . 'includes/functions_display.' . $this->php_ext;
		}

		display_custom_bbcodes();

		/* Include files needed for displaying Smilies */
		if (!function_exists('generate_smilies'))
		{
			include $this->root_path . 'includes/functions_posting.' . $this->php_ext;
		}

		generate_smilies('inline', 0);

		/* Add some security layer */
		add_form_key('highlight_update');

		/* If we're submitting or previewing, request the highlight text */
		if ($submit || $preview)
		{
			/* Request the textarea input for the Highlight comment */
			$highlight_text = $this->request->variable('highlight_text', '', true);
			$highlight_text = htmlspecialchars_decode($highlight_text, ENT_COMPAT);
			$highlight_text_parsed = $this->parser->parse($highlight_text);
		}

		switch ($action)
		{
			case 'edit':
				/* Grab the highlight data */
				$sql = 'SELECT hl_text, hl_time, hl_user_id, hl_username, hl_user_colour
						FROM ' . $this->highlights_table . '
						WHERE post_id = ' . (int) $post_id;
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				if ($submit)
				{
					/* Check the form key for security */
					if (!check_form_key('highlight_update'))
					{
						throw new \phpbb\exception\http_exception(400, $this->lang->lang('FORM_INVALID'));
					}

					/* Check if we actually have a string */
					if (empty($highlight_text))
					{
						meta_refresh(3, $post_url);
						throw new \phpbb\exception\http_exception(411, $this->lang->lang('HLPOSTS_ERROR_TEXT_NONE'));
					}

					/* Set up highlight data */
					$highlight_data = array(
						'hl_text'	=> $highlight_text_parsed,
					);

					/* Update the highlights table */
					$sql = 'UPDATE ' . $this->highlights_table . ' SET ' . $this->db->sql_build_array('UPDATE', $highlight_data) . ' WHERE post_id = ' . (int) $post_id;
					$this->db->sql_query($sql);

					/* Send a notification, only when the checkbox is checked and not user's own post. */
					if ($author_id != $this->user->data['user_id'])
					{
						if (!empty($hlposts_notify_board))
						{
							/* Increment our notifications sent counter */
							$this->config->increment('hlposts_notification_id', 1);

							/* Send out notification */
							$this->notification_manager->add_notifications('threedi.hlposts.notification.type.highlighted', array(
								'action'				=> 'edited',
								'post_id'				=> (int) $post_id,
								'topic_id'				=> (int) $topic_id,
								'forum_id'				=> (int) $forum_id,
								'author_id'				=> (int) $author_id,
								'actionee_id'			=> (int) $this->user->data['user_id'],
								'notification_id'		=> (int) $this->config['hlposts_notification_id'],
							));
						}

						if (!empty($hlposts_notify_pm))
						{
							/* Send out PM notification */
							$hlposts_pm_edit = $this->hlposts_utils->json_decode_pm_edit();
							$this->hlposts_utils->send_pm($this->user->data['user_id'], $author_id, $hlposts_pm_edit['title'], $hlposts_pm_edit['message'], $forum_id, $post_id);
						}
					}

					/* Add it to the log */
					$this->log->add('mod', $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_HLPOSTS_LOG_HIGHLIGHT_EDITED', time(), array('topic_id' => $topic_id, $this->utils->clean_formatting($highlight_data['hl_text'])));

					/* Show success message and refresh the page */
					meta_refresh(3, $post_url);
					return $this->helper->message($this->lang->lang('HLPOSTS_EDITED_TEXT') . $return_msg, array(), $this->lang->lang('HLPOSTS_EDITED_TITLE'));
				}
			break;

			case 'add':
				if ($submit)
				{
					/* Check the form key for security */
					if (!check_form_key('highlight_update'))
					{
						throw new \phpbb\exception\http_exception(400, $this->lang->lang('FORM_INVALID'));
					}

					/* Check if we actually have a string */
					if (empty($highlight_text))
					{
						meta_refresh(3, $post_url);
						throw new \phpbb\exception\http_exception(411, $this->lang->lang('HLPOSTS_ERROR_TEXT_NONE'));
					}

					/* Add the bool to the posts table */
					$sql = 'UPDATE ' . POSTS_TABLE . ' SET is_highlighted = 1 WHERE post_id = ' . (int) $post_id;
					$this->db->sql_query($sql);

					/* Set up highlight data */
					$highlight_data = array(
						'post_id'			=> (int) $post_id,
						'hl_text'			=> (string) $highlight_text_parsed,
						'hl_time'			=> time(),
						'hl_user_id'		=> (int) $this->user->data['user_id'],
						'hl_username'		=> $this->user->data['username'],
						'hl_user_colour'	=> $this->user->data['user_colour'],
					);

					/* Update the highlights table */
					$sql = 'INSERT INTO ' . $this->highlights_table . ' ' . $this->db->sql_build_array('INSERT', $highlight_data);
					$this->db->sql_query($sql);

					/* Send a notification, only when checkbox is check and not user's own post. */
					if ($author_id != $this->user->data['user_id'])
					{
						if (!empty($hlposts_notify_board))
						{
							/* Increment our notifications sent counter */
							$this->config->increment('hlposts_notification_id', 1);

							/* Send out notification */
							$this->notification_manager->add_notifications('threedi.hlposts.notification.type.highlighted', array(
								'action'				=> 'added',
								'post_id'				=> (int) $post_id,
								'topic_id'				=> (int) $topic_id,
								'forum_id'				=> (int) $forum_id,
								'author_id'				=> (int) $author_id,
								'actionee_id'			=> (int) $this->user->data['user_id'],
								'notification_id'		=> (int) $this->config['hlposts_notification_id'],
							));
						}

						if (!empty($hlposts_notify_pm))
						{
							/* Send out PM notification */
							$hlposts_pm_create = $this->hlposts_utils->json_decode_pm_create();
							$this->hlposts_utils->send_pm($this->user->data['user_id'], $author_id, $hlposts_pm_create['title'] , $hlposts_pm_create['message'], $forum_id, $post_id);
						}
					}

					/* Add it to the log */
					$this->log->add('mod', $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_HLPOSTS_LOG_HIGHLIGHT_ADDED', time(), array('topic_id' => $topic_id, $this->utils->clean_formatting($highlight_data['hl_text'])));

					/* Show success message and refresh the page */
					meta_refresh(3, $post_url);
					return $this->helper->message($this->lang->lang('HLPOSTS_ADDED_TEXT') . $return_msg, array(), $this->lang->lang('HLPOSTS_ADDED_TITLE'));
				}
			break;

			case 'delete':
				if (confirm_box(true))
				{
					/* Update the bool in the POSTS table */
					$sql = 'UPDATE ' . POSTS_TABLE . ' SET is_highlighted = 0 WHERE post_id = ' . (int) $post_id;
					$this->db->sql_query($sql);

					/* Remove the highlight from the Highlight table */
					$sql = 'DELETE FROM ' . $this->highlights_table . ' WHERE post_id = ' . (int) $post_id;
					$this->db->sql_query($sql);

					/* Add it to the log */
					$this->log->add('mod', $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_HLPOSTS_LOG_HIGHLIGHT_DELETED', time(), array('topic_id' => $topic_id));

					/* Send a notification, only when checkbox is checked and not user's own post. */
					if ($author_id != $this->user->data['user_id'])
					{
						if (!empty($hlposts_notify_board))
						{
							/* Increment our notifications sent counter */
							$this->config->increment('hlposts_notification_id', 1);

							/* Send out notification */
							$this->notification_manager->add_notifications('threedi.hlposts.notification.type.highlighted', array(
								'action'				=> 'deleted',
								'post_id'				=> (int) $post_id,
								'topic_id'				=> (int) $topic_id,
								'forum_id'				=> (int) $forum_id,
								'author_id'				=> (int) $author_id,
								'actionee_id'			=> (int) $this->user->data['user_id'],
								'notification_id'		=> (int) $this->config['hlposts_notification_id'],
							));
						}

						if (!empty($hlposts_notify_pm))
						{
							/* Send out PM notification */
							$hlposts_pm_delete = $this->hlposts_utils->json_decode_pm_delete();
							$this->hlposts_utils->send_pm($this->user->data['user_id'], $author_id, $hlposts_pm_delete['title'] , $hlposts_pm_delete['message'], $forum_id, $post_id);
						}
					}

					/* Show success message and refresh the page */
					meta_refresh(3, $post_url);
					return $this->helper->message($this->lang->lang('HLPOSTS_DELETED_TEXT') . $return_msg, array(), $this->lang->lang('HLPOSTS_DELETED_TITLE'));
				}
				else
				{
					confirm_box(false, $this->lang->lang('HLPOSTS_DELETE_CONFIRM'), build_hidden_fields(array(
						'action'	=> $action,
						'post_id'	=> (int) $post_id,
						'topic_id'	=> (int) $topic_id,
						'forum_id'	=> (int) $forum_id,
						'a'			=> (int) $author_id,
					)), 'delete_highlight.html');
				}
			break;
		}

		/* Statuses */
		$bbcode_status	= ($this->auth->acl_get('f_bbcode', $forum_id) && $this->config['allow_bbcode']);
		$flash_status	= ($this->auth->acl_get('f_bbcode', $forum_id) && $this->auth->acl_get('f_flash', $forum_id) && $this->config['allow_post_flash']);
		$img_status		= ($this->auth->acl_get('f_bbcode', $forum_id) && $this->auth->acl_get('f_img', $forum_id));
		$links_status	= ($this->auth->acl_get('f_bbcode', $forum_id) && $this->config['allow_post_links']);
		$quote_status	= ($this->auth->acl_get('f_bbcode', $forum_id) && $this->config['allow_bbcode']);
		$smilies_status	= ($this->auth->acl_get('f_smilies', $forum_id) && $this->config['allow_smilies']);

		/* Template switches also over here, if needed */
		$this->hlposts_utils->template_switches_over_viewtopic();

		$this->template->assign_vars(array(
			'BBCODE_STATUS'				=> $bbcode_status ? $this->lang->lang('BBCODE_IS_ON', '<a href="' . $this->helper->route('phpbb_help_bbcode_controller') . '">', '</a>') : $this->lang->lang('BBCODE_IS_OFF', '<a href="' . $this->helper->route('phpbb_help_bbcode_controller') . '">', '</a>'),
			'IMG_STATUS'				=> $img_status ? $this->lang->lang('IMAGES_ARE_ON') : $this->lang->lang('IMAGES_ARE_OFF'),
			'FLASH_STATUS'				=> $flash_status ? $this->lang->lang('FLASH_IS_ON') : $this->lang->lang('FLASH_IS_OFF'),
			'SMILIES_STATUS'			=> $smilies_status ? $this->lang->lang('SMILIES_ARE_ON') : $this->lang->lang('SMILIES_ARE_OFF'),
			'URL_STATUS'				=> $links_status ? $this->lang->lang('URL_IS_ON') : $this->lang->lang('URL_IS_OFF'),

			'POST_AUTHOR'				=> get_username_string('full', $post['user_id'], $post['username'], $post['user_colour']),
			'POST_SUBJECT'				=> $post['post_subject'],
			'POST_TEXT'					=> $this->renderer->render($post['post_text']),
			'POST_TIME'					=> $this->user->format_date($post['post_time']),

			'HIGHLIGHT_TEXT_EDIT'		=> ($submit || $preview) ? $this->utils->unparse($highlight_text_parsed) : ($action === 'edit' ? $this->utils->unparse($row['hl_text']) : ''),
			'HIGHLIGHT_TEXT_PREVIEW'	=> ($submit || $preview) ? $this->renderer->render($highlight_text_parsed) : ($action === 'edit' ? $this->renderer->render($row['hl_text']) : ''),
			'HIGHLIGHT_TIME'			=> ($action === 'edit') ? $this->user->format_date($row['hl_time']) : $this->user->format_date(time()),
			'HIGHLIGHT_USER'			=> ($action === 'edit') ? get_username_string('full', $row['hl_user_id'], $row['hl_username'], $row['hl_user_colour']) : get_username_string('full', $this->user->data['user_id'], $this->user->data['username'], $this->user->data['user_colour']),

			'S_HIGHLIGHTING'			=> true,
			'S_BBCODE_ALLOWED'			=> $bbcode_status,
			'S_BBCODE_FLASH'			=> $flash_status,
			'S_BBCODE_IMG'				=> $img_status,
			'S_BBCODE_QUOTE'			=> $quote_status,
			'S_LINKS_ALLOWED'			=> $links_status,
			'S_SMILIES_ALLOWED'			=> $smilies_status,

			'U_POST'					=> $post_url,
			'U_ACTION'					=> $this->helper->route('threedi_hlposts_controller', array('forum_id' => (int) $forum_id, 'topic_id' => (int) $topic_id, 'post_id' => (int) $post_id, 'action' => $action, 'author_id' => (int) $author_id)),
		));

		/* Output a page. */
		return $this->helper->render('add_highlight.html', $this->lang->lang('HLPOSTS_' . strtoupper($action)));
	}

	/**
	 * Viewing user's who have marked a post read.
	 *
	 * @param	int		$forum_id	The forum id
	 * @param	int		$post_id	The post id
	 * @param	int		$page		The page we're on from pagination
	 * @return	mixed	rendered page
	 * @access public
	 */
	public function view($forum_id, $post_id, $page)
	{
		/* Can not view users list */
		if (!$this->auth->acl_get('m_hlposts_can_view_read'))
		{
			throw new \phpbb\exception\http_exception(403, $this->lang->lang('NOT_AUTHORISED'));
		}

		/* Check if Highlight Post is enabled for this forum */
		if (!$this->hlposts_utils->forum_enabled($forum_id))
		{
			// Should we better use an exception here as well?
			return $this->helper->message($this->lang->lang('HLPOSTS_ERROR_DISABLED') . $return_msg);
		}

		/* Template switches also over here, if needed */
		$this->hlposts_utils->template_switches_over_viewtopic();

		/* Set up post URL and return message */
		$post_url = append_sid($this->root_path . 'viewtopic.' . $this->php_ext, "f={$forum_id}&amp;p={$post_id}#p{$post_id}", false);
		$return_msg = '<br /><br />' . $this->lang->lang('RETURN_PAGE', '<a href="' . $post_url . '">', '</a>');

		/* Include memberlist language */
		$this->lang->add_lang('memberlist');

		/* Set number of users variable for pagination */
		$hlposts_users = ($this->config['hlposts_users_page']) ? (int) $this->config['hlposts_users_page'] : (int) $this->config['posts_per_page'];

		/* Set start variable for pagination */
		$start = (($page - 1) * (int) $hlposts_users);

		/* Set up ordering, by NAME(n)/TIME(t)/GROUP(g)/JOINED(j)/VISIT(v) & ASC(a)/DESC(d) */
		$default_key = 't';
		$sort_key = $this->request->variable('sk', $default_key);
		$sort_dir = $this->request->variable('sd', 'd');

		/* Request the variables for searching */
		$marked_select	= $this->request->variable('ms', 'lt');
		$joined_select	= $this->request->variable('js', 'lt');
		$active_select	= $this->request->variable('as', 'lt');
		$marked			= explode('-', $this->request->variable('m', ''));
		$joined			= explode('-', $this->request->variable('j', ''));
		$active			= explode('-', $this->request->variable('a', ''));
		$username		= $this->request->variable('u', '');
		$group_selected	= $this->request->variable('g', 0);
		$s_group_select = $this->hlposts_utils->get_groups_for_select($group_selected);
		$find_key_match = array('lt' => '<', 'gt' => '>', 'eq' => '=');

		$sql_order_by = '';
		switch ($sort_key)
		{
			case 'n':
				$sql_order_by = 'u.username';
			break;

			case 't':
				$sql_order_by = 'pr.read_time';
			break;

			case 'g':
				$sql_order_by = 'g.group_name';
			break;

			case 'j':
				$sql_order_by = 'u.user_regdate';
			break;

			case 'v':
				$sql_order_by = 'u.user_lastvisit';
			break;
		}
		$sql_order_dir = $sort_dir == 'a' ? 'ASC' : 'DESC';

		/* Grab the post data */
		$sql_ary = array(
			'SELECT'	=> 't.topic_title, t.topic_id, p.forum_id, p.post_subject, p.post_text, p.post_time, p.post_attachment, p.is_highlighted, h.*, u.user_id, u.username, u.user_colour',
			'FROM'		=> array(
				POSTS_TABLE		=> 'p',
				USERS_TABLE		=> 'u',
				TOPICS_TABLE	=> 't',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array($this->highlights_table => 'h'),
					'ON'	=> 'p.post_id = h.post_id',
				),
			),
			'WHERE'		=> 'p.poster_id = u.user_id
								AND p.topic_id = t.topic_id
								AND p.post_id = ' . (int) $post_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query($sql);
		$post = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		/* SQL FROM initial */
		$sql_from = array(
			$this->post_read_table	=> 'pr',
			USERS_TABLE				=> 'u',
			GROUPS_TABLE			=> 'g',
		);

		/* SQL WHERE initial */
		$sql_where = 'pr.user_id = u.user_id
						AND u.group_id = g.group_id
						AND pr.post_id = ' . (int) $post_id;

		/* SQL WHERE for the username */
		$sql_where .= ($username) ? ' AND u.username_clean ' . $this->db->sql_like_expression(str_replace('*', $this->db->get_any_char(), utf8_clean_string($username))) : '';

		/* SQL WHERE for marked time */
		if (isset($find_key_match[$marked_select]) && count($marked) == 3)
		{
			$marked_time = gmmktime(0, 0, 0, (int) $marked[1], (int) $marked[2], (int) $marked[0]);
			if ($marked_time !== false)
			{
				$sql_where .= " AND pr.read_time " . $find_key_match[$marked_select] . ' '. $marked_time;
			}
		}

		/* SQL WHERE for joined date */
		if (isset($find_key_match[$joined_select]) && count($joined) == 3)
		{
			$joined_time = gmmktime(0, 0, 0, (int) $joined[1], (int) $joined[2], (int) $joined[0]);
			if ($joined_time !== false)
			{
				$sql_where .= " AND u.user_regdate " . $find_key_match[$joined_select] . ' ' . $joined_time;
			}
		}

		/* SQL WHERE for last visit time */
		if (isset($find_key_match[$active_select]) && count($active) == 3)
		{
			$active_time = gmmktime(0, 0, 0, (int) $active[1], (int) $active[2], (int) $active[0]);
			if ($active_time !== false)
			{
				$sql_where .= " AND u.user_lastvisit " . $find_key_match[$active_select] . ' ' . $active_time;
			}
		}

		/* SQL FROM and WHERE for group id */
		if ($group_selected)
		{
			$sql_from[USER_GROUP_TABLE] = 'ug';
			$sql_where .= " AND u.user_id = ug.user_id AND ug.group_id = $group_selected AND ug.user_pending = 0 ";
		}

		/* Grab all the users */
		$sql_array = array(
			'SELECT'	=> 'pr.read_time, g.group_id, g.group_colour, g.group_name, u.user_id, u.username, u.user_colour, u.user_lastvisit, u.user_regdate',
			'FROM'		=> $sql_from,
			'WHERE'		=> $sql_where,
			'ORDER_BY'	=> $sql_order_by . ' ' . $sql_order_dir,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, (int) $hlposts_users, $start);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('users', array(
				'USER'			=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'GROUP_NAME'	=> $this->group_helper->get_name($row['group_name']),
				'GROUP_COLOUR'	=> $row['group_colour'],
				'JOIN_DATE'		=> $this->user->format_date($row['user_regdate']),
				'LAST_VISIT'	=> $this->user->format_date($row['user_lastvisit']),
				'READ_TIME'		=> $this->user->format_date($row['read_time']),

				'U_GROUP'		=> append_sid("{$this->root_path}memberlist.{$this->php_ext}", 'mode=group&amp;g=' . $row['group_id']),
			));
		}
		$this->db->sql_freeresult($result);

		/* Count the total number of users */
		$sql = 'SELECT COUNT(user_id) as user_count FROM ' . $this->post_read_table . ' WHERE post_id = ' . (int) $post_id;
		$result = $this->db->sql_query($sql);
		$user_count = (int) $this->db->sql_fetchfield('user_count');
		$this->db->sql_freeresult($result);

		/* Start pagination */
		$this->pagination->generate_template_pagination(
			array(
				'routes' => array(
					'threedi_hlposts_view',
					'threedi_hlposts_viewpage',
				),
				'params' => array('forum_id' => (int) $forum_id, 'post_id' => (int) $post_id, 'sk' => $sort_key, 'sd' => $sort_dir),
			), 'pagination', 'page', $user_count, (int) $hlposts_users, $start);

		/* Set up before or after time options */
		$find_time = array('lt' => $this->lang->lang('BEFORE'), 'gt' => $this->lang->lang('AFTER'));
		$s_find_mark_time = '';
		foreach ($find_time as $key => $value)
		{
			$selected = ($marked_select == $key) ? ' selected="selected"' : '';
			$s_find_mark_time .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
		}

		$s_find_join_time = '';
		foreach ($find_time as $key => $value)
		{
			$selected = ($joined_select == $key) ? ' selected="selected"' : '';
			$s_find_join_time .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
		}

		$s_find_active_time = '';
		foreach ($find_time as $key => $value)
		{
			$selected = ($active_select == $key) ? ' selected="selected"' : '';
			$s_find_active_time .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
		}

		/* Check if there are any search parameters */
		$params = array();
		$check_params = array(
			'ms'	=> $marked_select,
			'js'	=> $joined_select,
			'as'	=> $active_select,
			'm'		=> !empty($marked) ? implode('-', $marked) : '',
			'j'		=> !empty($joined) ? implode('-', $joined) : '',
			'a'		=> !empty($active) ? implode('-', $active) : '',
			'u'		=> $username,
			'g'		=> $group_selected,
		);

		foreach ($check_params as $key => $value)
		{
			if (empty($value))
			{
				/* If the value is empty, we do not have to add the parameter to the URL */
				continue;
			}

			/* Encode strings, convert everything else to int in order to prevent empty parameters. */
			$param = urlencode($key) . '=' . ((is_string($value)) ? urlencode($value) : (int) $value);

			/* Add this parameter to the 'ALL' parameters array */
			$params[] = $param;
		}

		/* Set up sort URL, we have to add a '?' for the first param. */
		$sort_url = $this->helper->route('threedi_hlposts_view', array('forum_id' => (int) $forum_id, 'post_id' => (int) $post_id)) . '?' . implode('&amp;', $params);

		/* First let's render the text */
		$text = $this->renderer->render($post['post_text']);

		/**
		 * Attachments - Include files needed for display attachments
		 */
		if (!function_exists('parse_attachments'))
		{
			include $this->root_path . 'includes/functions_content.' . $this->php_ext;
		}

		if ($post['post_attachment'])
		{
			$sql = 'SELECT *
				FROM ' . ATTACHMENTS_TABLE . '
				WHERE post_msg_id = ' . (int) $post_id . '
					AND in_message = 0
				ORDER BY attach_id DESC';
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$attachments[] = $row;
			}
			$this->db->sql_freeresult($result);

			if (count($attachments))
			{
				$update_count = array();

				/* Only parses attachments placed inline? */
				parse_attachments((int) $post['forum_id'], $text, $attachments, $update_count);
			}
		}

		/* Assign template variables */
		$this->template->assign_vars(array(
			'PAGE_NUMBER'		=> $this->pagination->on_page($user_count, (int) $hlposts_users, $start),
			'TOTAL_USERS'		=> $this->lang->lang('TOTAL_USERS', $user_count),

			'S_POST_OVERVIEW'	=> (bool) $this->config['hlposts_post_overview'],
			'POST_AUTHOR'		=> get_username_string('full', $post['user_id'], $post['username'], $post['user_colour']),
			'POST_SUBJECT'		=> $post['post_subject'],
			'POST_TEXT'			=> $text,
			'POST_TIME'			=> $this->user->format_date($post['post_time']),
			'U_POST'			=> $post_url,

			'S_HIGHLIGHT'		=> !empty($post['is_highlighted']) ? true : false,
			'HIGHLIGHT_TEXT'	=> !empty($post['hl_text']) ? $this->renderer->render($post['hl_text']) : '',
			'HIGHLIGHT_TIME'	=> !empty($post['hl_time']) ? $this->user->format_date($post['hl_time']) : '',
			'HIGHLIGHT_USER'	=> !empty($post['hl_user_id']) ? get_username_string('full', $post['hl_user_id'], $post['hl_username'], $post['hl_user_colour']) : '',

			/*
			 * Search
			 */
			'ACTIVE'				=> implode('-', $active),
			'JOINED'				=> implode('-', $joined),
			'MARKED'				=> implode('-', $marked),
			'USERNAME'				=> $username,
			'S_ACTIVE_TIME_OPTIONS'	=> $s_find_active_time,
			'S_JOINED_TIME_OPTIONS'	=> $s_find_join_time,
			'S_MARKED_TIME_OPTIONS'	=> $s_find_mark_time,
			'S_GROUP_SELECT'		=> $s_group_select,

			'S_FORM_ACTION'		=> $this->helper->route('threedi_hlposts_view', array('forum_id' => (int) $forum_id, 'post_id' => (int) $post_id)),

			'U_SORT_JOIN'		=> $sort_url . '&amp;sk=j&amp;sd=' . (($sort_key == 'j' && $sort_dir == 'd') ? 'a' : 'd'),
			'U_SORT_GROUP'		=> $sort_url . '&amp;sk=g&amp;sd=' . (($sort_key == 'g' && $sort_dir == 'd') ? 'a' : 'd'),
			'U_SORT_NAME'		=> $sort_url . '&amp;sk=n&amp;sd=' . (($sort_key == 'n' && $sort_dir == 'd') ? 'a' : 'd'),
			'U_SORT_TIME'		=> $sort_url . '&amp;sk=t&amp;sd=' . (($sort_key == 't' && $sort_dir == 'd') ? 'a' : 'd'),
			'U_SORT_VISIT'		=> $sort_url . '&amp;sk=v&amp;sd=' . (($sort_key == 'v' && $sort_dir == 'd') ? 'a' : 'd'),
		));

		/**
		 * Breadcrumbs
		 *
		 * Grab forum data
		 */
		$sql = 'SELECT * FROM ' . FORUMS_TABLE . ' WHERE forum_id = ' . (int) $post['forum_id'];
		$result = $this->db->sql_query($sql);
		$forum_data_ary = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		/**
		 * And include files needed for create forum navigation links for given forum
		 */
		if (!function_exists('generate_forum_nav'))
		{
			include $this->root_path . 'includes/functions_display.' . $this->php_ext;
		}

		generate_forum_nav($forum_data_ary);

		/*
		 * Jumpbox
		 */
		make_jumpbox(append_sid("{$this->root_path}viewforum.{$this->php_ext}"));

		return $this->helper->render('view_users_read.html', $this->lang->lang('VIEWING_MEMBERS'));
	}
}
