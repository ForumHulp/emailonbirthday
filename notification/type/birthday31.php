<?php
/**
*
* @package E-mail on birthday
* @copyright (c) 2015 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\emailonbirthday\notification\type;

/**
* E-mail on birthday notification class
*/

class birthday extends \phpbb\notification\type\base
{
	/**
	* Get notification type name
	*
	* @return string
	*/
	public function get_type()
	{
		return 'forumhulp.emailonbirthday.notification.type.birthday';
	}

	/**
	* Language key used to output the text
	*
	* @var string
	*/
	protected $language_key = 'NOTIFICATION_BIRTHDAY';

	/**
	* Notification option data (for outputting to the user)
	*
	* @var bool|array False if the service should use it's default data
	* 					Array of data (including keys 'id', 'lang', and 'group')
	*/
	static public $notification_option = array(
		'lang'	=> 'NOTIFICATION_TYPE_BIRTHDAY',
		'group'	=> 'NOTIFICATION_GROUP_MISCELLANEOUS',
	);

	/**
	* Is available
	*/
	public function is_available()
	{
		return true;
	}

	/**
	* Get the id of the item
	*
	* @param array $post The data from the post
	* @return int The post id
	*/
	static public function get_item_id($data)
	{
		return substr(time(), -5);
	}

	/**
	* Get the id of the parent
	*
	* @param array $post The data from the post
	* @return int The topic id
	*/
	static public function get_item_parent_id($post)
	{
		return 0;
	}

	/**
	* Find the users who want to receive notifications
	*
	* @param array $post Data from submit_post
	* @param array $options Options for finding users for notification
	*
	* @return array
	*/
	public function find_users_for_notification($data, $options = array())
	{
		$options = array_merge(array(
			'ignore_users'		=> array(),
		), $options);

		$users = array();
		$users[] = (int) $data['user_id'];

		return $this->check_user_notification_options($users, $options);
	}

	/**
	* Get the user's avatar
	*/
	public function get_avatar()
	{
		return $this->user_loader->get_avatar($this->get_data['user_id'], false, true);
	}

	/**
	* Get the HTML formatted title of this notification
	*
	* @return string
	*/
	public function get_title()
	{
		return $this->user->lang('BIRTHDAY_NOTIFICATION', $this->get_data('name'), $this->get_data('age'));
	}

	/**
	* Get email template
	*
	* @return string|bool
	*/
	public function get_email_template()
	{
		return '@forumhulp_emailonbirthday/emailonbirthday';
	}

	/**
	* Get email template variables
	*
	* @return array
	*/
	public function get_email_template_variables()
	{
		return array(
			'USERNAME'		=> htmlspecialchars_decode($this->get_data('name')),
			'BIRTHDAY'		=> $this->get_data('age'),
			'SITENAME'		=> $this->config['sitename']
		);
	}

	/**
	* Get the url to this item
	*
	* @return string URL
	*/
	public function get_url()
	{
		return '';
	}

	/**
	* {inheritDoc}
	*/
	public function get_redirect_url()
	{
		return '';
	}

	/**
	* Users needed to query before this notification can be displayed
	*
	* @return array Array of user_ids
	*/
	public function users_to_query()
	{
		return array();
	}

	/**
	* {@inheritdoc}
	*/
	public function create_insert_array($data, $pre_create_data = array())
	{
		$this->set_data('user_id', $data['user_id']);
		$this->set_data('age', $data['age']);
		$this->set_data('name', $data['name']);
		$this->set_data('user_email', $data['user_email']);

		return parent::create_insert_array($data, $pre_create_data);
	}
}
