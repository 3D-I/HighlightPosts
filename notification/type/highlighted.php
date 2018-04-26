<?php
/**
 *
 * Highlight Posts. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, 3Di & Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace threedi\hlposts\notification\type;

/**
 * Highlight Posts Notification class.
 */
class highlighted extends \phpbb\notification\type\base
{
	/** @var \phpbb\user_loader */
	protected $user_loader;

	/**
	 * Set the user loader
	 * @param \phpbb\user_loader $user_loader
	 * @return void
	 */
	public function set_user_loader(\phpbb\user_loader $user_loader)
	{
		$this->user_loader = $user_loader;
	}

	/**
	 * Get notification type name
	 *
	 * @return string
	 */
	public function get_type()
	{
		return 'threedi.hlposts.notification.type.highlighted';
	}

	/**
	 * Is this type available to the current user (defines whether or not it will be shown in the UCP Edit notification options)
	 *
	 * @return bool True/False whether or not this is available to the user
	 */
	public function is_available()
	{
		return false;
	}

	/**
	 * Get the id of the notification
	 *
	 * @param array $data The type specific data
	 *
	 * @return int Id of the notification
	 */
	public static function get_item_id($data)
	{
		return $data['notification_id'];
	}

	/**
	 * Get the id of the parent
	 *
	 * @param array $data The type specific data
	 *
	 * @return int Id of the parent
	 */
	public static function get_item_parent_id($data)
	{
		return $data['topic_id'];
	}

	/**
	 * Find the users who want to receive notifications
	 *
	 * @param array $data		The type specific data
	 * @param array $options	Options for finding users for notification
	 * 							ignore_users => array of users and user types that should not receive notifications from this type because they've already been notified
	 * 							e.g.: array(2 => array(''), 3 => array('', 'email'), ...)
	 *
	 * @return array
	 */
	public function find_users_for_notification($data, $options = array())
	{
		$users = array();

		$users[$data['author_id']] = $this->notification_manager->get_default_methods();

		return $users;
	}

	/**
	 * Users needed to query before this notification can be displayed
	 *
	 * @return array Array of user_ids
	 */
	public function users_to_query()
	{
		return array($this->get_data('actionee_id'));
	}

	/**
	* Get the user's avatar
	*/
	public function get_avatar()
	{
		return $this->user_loader->get_avatar($this->get_data('actionee_id'), false, true);
	}

	/**
	 * Get the HTML formatted title of this notification
	 *
	 * @return string
	 */
	public function get_title()
	{
		/* $action: 'added', 'edited', 'deleted' */
		$action = $this->get_data('action');
		$username = $this->user_loader->get_username($this->get_data('actionee_id'), 'no_profile');

		return $this->language->lang('HLPOSTS_NOTIFICATION_' . strtoupper($action), $username);
	}

	/**
	* Get the HTML formatted reference of the notification
	*
	* @return string
	*/
	public function get_reference()
	{
		$sql = 'SELECT post_subject FROM ' . POSTS_TABLE . ' WHERE post_id = ' . (int) $this->get_data('post_id');
		$result = $this->db->sql_query($sql);
		$post_subject = $this->db->sql_fetchfield('post_subject');
		$this->db->sql_freeresult($result);

		return $this->language->lang(
			'NOTIFICATION_REFERENCE',
			censor_text($post_subject)
		);
	}

	/**
	 * Get the url to this item
	 *
	 * @return string URL
	 */
	public function get_url()
	{
		return append_sid($this->phpbb_root_path . 'viewtopic.' . $this->php_ext, "p={$this->get_data('post_id')}#p{$this->get_data('post_id')}");
	}

	/**
	 * Get email template
	 *
	 * @return string|bool
	 */
	public function get_email_template()
	{
		return false;
	}

	/**
	 * Get email template variables
	 *
	 * @return array
	 */
	public function get_email_template_variables()
	{
		return array();
	}

	/**
	 * Function for preparing the data for insertion in an SQL query
	 * (The service handles insertion)
	 *
	 * @param array $data				The type specific data
	 * @param array $pre_create_data	Data from pre_create_insert_array()
	 *
	 * @return void|array				Array of data ready to be inserted into the database
	 */
	public function create_insert_array($data, $pre_create_data = array())
	{
		/* $action: 'added', 'edited', 'deleted' */
		$this->set_data('action', $data['action']);
		$this->set_data('post_id', $data['post_id']);
		$this->set_data('actionee_id', $data['actionee_id']);

		parent::create_insert_array($data, $pre_create_data);
	}
}
