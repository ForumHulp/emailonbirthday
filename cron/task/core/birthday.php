<?php
/**
*
* @package E-mail on birthday
* @copyright (c) 2015 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\emailonbirthday\cron\task\core;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* @ignore
*/

class birthday extends \phpbb\cron\task\base
{
	protected $phpbb_root_path;
	protected $php_ext;
	protected $config;
	protected $user;
	protected $db;
	protected $log;
	protected $manager;
	protected $container;

	/**
	* Constructor.
	*
	* @param string $phpbb_root_path The root path
	* @param string $php_ext The PHP extension
	* @param phpbb_config $config The config
	* @param phpbb_db_driver $db The db connection
	*/
	public function __construct($phpbb_root_path, $php_ext, \phpbb\config\config $config, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, \phpbb\log\log $log, \forumhulp\emailonbirthday\lang_manager\manager $manager, ContainerInterface $container)
	{
		$this->phpbb_root_path	= $phpbb_root_path;
		$this->php_ext 			= $php_ext;
		$this->config			= $config;
		$this->user				= $user;
		$this->db				= $db;
		$this->log				= $log;
		$this->lang_manager		= $manager;
		$this->container		= $container;
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
				email_on_birthday + 15778463 < UNIX_TIMESTAMP(now())';
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
				if (!class_exists('messenger'))
				{
					include($this->phpbb_root_path . 'includes/functions_messenger.' . $this->php_ext);
				}

				$server_url = generate_board_url();
				$manager = $this->container->get('ext.manager');
				$use_html = $manager->is_enabled('forumhulp/htmlemail');
				$messenger = new \messenger(false);

				foreach ($msg_list as $key => $value)
				{
					$messenger->template('@forumhulp_emailonbirthday/emailonbirthday', $value['lang']);
					($use_html) ? $messenger->set_mail_html($this->config['html_email_on_birthday']) : null;

					$messenger->to($value['email'], $value['name']);

					$messenger->headers('X-AntiAbuse: Board servername - ' . $this->config['server_name']);
					$messenger->headers('X-AntiAbuse: User_id - ' . $value['user_id']);
					$messenger->headers('X-AntiAbuse: Username - ' . $value['name']);
					$messenger->headers('X-AntiAbuse: User IP - ' . $this->user->ip);

					$messenger->assign_vars(array(
						'USERNAME'		=> htmlspecialchars_decode($value['name']),
						'BIRTHDAY'		=> $value['age'],
						'SITENAME'		=> $this->config['sitename']
						)
					);

					$messenger->send(NOTIFY_EMAIL);

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
