<?php
/**
*
* @package E-mail on birthday
* @copyright (c) 2015 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\emailonbirthday\cron\task\core;

use phpbb\config\config;
use phpbb\user;
use phpbb\db\driver\driver_interface;
use phpbb\log\log;
use forumhulp\emailonbirthday\lang_manager\lang_manager;
use phpbb\extension\manager;

/**
* @ignore
*/

class birthday extends \phpbb\cron\task\base
{
	protected $config;
	protected $user;
	protected $db;
	protected $log;
	protected $lang_manager;
	protected $extension_manager;
	protected $notification_manager;
	protected $phpbb_root_path;
	protected $php_ext;

	/**
	* Constructor.
	*/
	public function __construct(config $config, user $user, driver_interface $db, log $log, lang_manager $lang_manager, manager $extension_manager, \phpbb\notification\manager $notification_manager, $phpbb_root_path, $php_ext)
	{
		$this->config			= $config;
		$this->user				= $user;
		$this->db				= $db;
		$this->log				= $log;
		$this->lang_manager		= $lang_manager;
		$this->ext_manager		= $extension_manager;
		$this->noti_manager		= $notification_manager;
		$this->phpbb_root_path	= $phpbb_root_path;
		$this->php_ext 			= $php_ext;
	}

	/**
	* Runs this cron task.
	*
	* @return null
	*/
	public function run()
	{
		$time = $this->user->create_datetime();
		$now = phpbb_gmgetdate($time->getTimestamp() + $time->getOffset());

		// Display birthdays of 29th february on 28th february in non-leap-years
		$leap_year_birthdays = '';
		if ($now['mday'] == 28 && $now['mon'] == 2 && !$time->format('L'))
		{
			$leap_year_birthdays = ' OR user_birthday LIKE "' . $this->db->sql_escape(sprintf("%2d-%2d-", 29, 2)) . '%"';
		}

		$sql = 'SELECT user_id, username, user_email, user_lang, 
				YEAR(CURRENT_TIMESTAMP) - YEAR(str_to_date(user_birthday, "%d-%m-%Y")) AS age
				FROM ' . USERS_TABLE . ' 
				WHERE user_birthday <> " 0- 0-   0" AND user_birthday <> "" AND 
				(user_birthday LIKE "' . $this->db->sql_escape(sprintf("%2d-%2d-", $now["mday"], $now["mon"])) . '%"' . $leap_year_birthdays . ') AND 
				email_on_birthday + 86400 < UNIX_TIMESTAMP(now())';
		$result = $this->db->sql_query($sql);
		$msg_list = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$msg_list[] = array(
				'user_id'	=> $row['user_id'],
				'name'		=> $row['username'],
				'email' 	=> $row['user_email'],
				'lang'		=> $row['user_lang'],
				'age'		=> $this->lang_manager->numbertext($row['age'], $row['user_lang']),
				'time'		=> time()
			);
		}

		if (sizeof($msg_list))
		{
			if ($this->config['email_enable'])
			{
				foreach ($msg_list as $key => $value)
				{
					$this->noti_manager->add_notifications('forumhulp.emailonbirthday.notification.type.birthday', array(
						  'user_id'		=> $value['user_id'],
						  'age'			=> $value['age'],
						  'name'		=> $value['name'],
						  'user_email'	=> $value['email']
					));

					$sql = 'UPDATE ' . USERS_TABLE . ' SET email_on_birthday = ' . time() . ' WHERE user_id = ' . $value['user_id'];
					$this->db->sql_query($sql);
				}
				$userlist = array_map(function ($entry)
				{
					return $entry['name'];
				}, $msg_list);

				$this->log->add('admin', $this->user->data['user_id'], $this->user->data['session_ip'], 'BIRTHDAYSEND', false, array(implode(', ', $userlist)));
			}
		}
		$this->config->set('emailonbirthday_last_gc', time());
	}

	/**
	* Returns whether this cron task can run, given current board configuration.
	*
	* @return bool
	*/
	public function is_runnable()
	{
		return $this->config['email_on_birthday'];
	}

	/**
	* Returns whether this cron task should run now, because enough time
	* has passed since it was last run.
	*
	* @return bool
	*/
	public function should_run()
	{
		return $this->config['emailonbirthday_last_gc'] < (time() - $this->config['emailonbirthday_gc']);
	}
}
