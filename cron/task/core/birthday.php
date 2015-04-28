<?php
/**
*
* @package E-mail on birthday
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\emailonbirthday\cron\task\core;

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

	/**
	* Constructor.
	*
	* @param string $phpbb_root_path The root path
	* @param string $php_ext The PHP extension
	* @param phpbb_config $config The config
	* @param phpbb_db_driver $db The db connection
	*/
	public function __construct($phpbb_root_path, $php_ext, \phpbb\config\config $config, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, \phpbb\log\log $log)
	{
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->config = $config;
		$this->user = $user;
		$this->db = $db;
		$this->log = $log;
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
				'age'		=> $this->convertNumber($row['age']) . $this->text_number($row['age']),
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
				$messenger = new \messenger(false);

				foreach($msg_list as $key => $value)
				{
					$messenger->template('@forumhulp_emailonbirthday/emailonbirthday', $value['lang']);

					$messenger->to($value['email'], $value['name']);

					$messenger->headers('X-AntiAbuse: Board servername - ' . $this->config['server_name']);
					$messenger->headers('X-AntiAbuse: User_id - ' . $value['user_id']);
					$messenger->headers('X-AntiAbuse: Username - ' . $value['name']);
					$messenger->headers('X-AntiAbuse: User IP - ' . '127.0.0.1');

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
		$this->config->set('email_on_birthday_last_gc', time());
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
		return $this->config['email_on_birthday_last_gc'] < (time() - $this->config['email_on_birthday_gc']);
	}

	public function convertNumber($number)
	{
		list($integer) = explode(".", (string) $number);
	
		$output = "";
	
		if ($integer{0} == "-")
		{
			$output = "negative ";
			$integer    = ltrim($integer, "-");
		}
		else if ($integer{0} == "+")
		{
			$output = "positive ";
			$integer    = ltrim($integer, "+");
		}
	
		if ($integer{0} == "0")
		{
			$output .= "zero";
		}
		else
		{
			$integer = str_pad($integer, 36, "0", STR_PAD_LEFT);
			$group   = rtrim(chunk_split($integer, 3, " "), " ");
			$groups  = explode(" ", $group);
	
			$groups2 = array();
			foreach ($groups as $g)
			{
				$groups2[] = $this->convertThreeDigit($g{0}, $g{1}, $g{2});
			}
	
			for ($z = 0; $z < count($groups2); $z++)
			{
				if ($groups2[$z] != "")
				{
					$output .= $groups2[$z] . $this->convertGroup(11 - $z) . (
							$z < 11
							&& !array_search('', array_slice($groups2, $z + 1, -1))
							&& $groups2[11] != ''
							&& $groups[11]{0} == '0'
								? " and "
								: ", "
						);
				}
			}
	
			$output = rtrim($output, ", ");
		}
		return $output;
	}

	public function text_number($n)
	{
		$teen_array = array(2, 3, 4 ,5 ,6 ,7 ,9 ,10, 11, 12, 13, 14, 15, 16, 17, 18, 19);
		$single_array = array(1 => 'ste', 2 => 'de', 3 => 'de', 4 => 'de', 5 => 'de', 6 => 'de', 7 => 'de', 8 => 'ste', 9 => 'de', 0 => 'ste');
		$if_teen = substr($n, -2, 2);
		$single = substr($n, -1, 1);
		if ( in_array($if_teen, $teen_array) )
		{
			$new_n = 'de';
		} elseif ( $if_teen == '00' ||  $if_teen == '01' ||  $if_teen > 20 )
		{
			$new_n = 'ste';
		} elseif ( $single_array[$single] )
		{
			$new_n = $single_array[$single];    
		}
		return $new_n;
	}

	public function convertGroup($index)
	{
		switch ($index)
		{
			case 11:
				return " decillion";
			case 10:
				return " nonillion";
			case 9:
				return " octillion";
			case 8:
				return " septillion";
			case 7:
				return " sextillion";
			case 6:
				return " quintrillion";
			case 5:
				return " quadrillion";
			case 4:
				return " trillion";
			case 3:
				return " billion";
			case 2:
				return " million";
			case 1:
				return " thousand";
			case 0:
				return "";
		}
	}

	public function convertThreeDigit($digit1, $digit2, $digit3)
	{
		$buffer = "";
	
		if ($digit1 == "0" && $digit2 == "0" && $digit3 == "0")
		{
			return "";
		}
	
		if ($digit1 != "0")
		{
			$buffer .= $this->convertDigit($digit1) . " hundred";
			if ($digit2 != "0" || $digit3 != "0")
			{
				$buffer .= " and ";
			}
		}
	
		if ($digit2 != "0")
		{
			$buffer .= $this->convertTwoDigit($digit2, $digit3);
		}
		else if ($digit3 != "0")
		{
			$buffer .= $this->convertDigit($digit3, $digit2);
		}
	
		return $buffer;
	}

	public function convertTwoDigit($digit1, $digit2)
	{
		if ($digit2 == "0")
		{
			switch ($digit1)
			{
				case "1":
					return "tien";
				case "2":
					return "twintig";
				case "3":
					return "dertig";
				case "4":
					return "veertig";
				case "5":
					return "vijftig";
				case "6":
					return "zestig";
				case "7":
					return "zeventig";
				case "8":
					return "tachtig";
				case "9":
					return "negentig";
			}
		} else if ($digit1 == "1")
		{
			switch ($digit2)
			{
				case "1":
					return "elf";
				case "2":
					return "twaalf";
				case "3":
					return "dertien";
				case "4":
					return "veertien";
				case "5":
					return "vijftien";
				case "6":
					return "zestien";
				case "7":
					return "zeventien";
				case "8":
					return "achttien";
				case "9":
					return "negentien";
			}
		} else
		{
			$temp = $this->convertDigit($digit2);
			switch ($digit1)
			{
				case "2":
					return "{$temp}entwing";
				case "3":
					return "{$temp}endertig";
				case "4":
					return "{$temp}enveertig";
				case "5":
					return "{$temp}envijftig";
				case "6":
					return "{$temp}enzestig";
				case "7":
					return "{$temp}enzeventig";
				case "8":
					return "{$temp}entachtig";
				case "9":
					return "{$temp}ennegentig";
			}
		}
	}

	public function convertDigit($digit, $digit1 = 1 )
	{
		switch ($digit)
		{
			case "0":
				return "";
			case "1":
				return ($digit1 == 0) ? "eer" : "een";
			case "2":
				return "twee";
			case "3":
				return ($digit1 == 0) ? "der" : "drie";
			case "4":
				return "vier";
			case "5":
				return "vijf";
			case "6":
				return "zes";
			case "7":
				return "zeven";
			case "8":
				return "acht";
			case "9":
				return "negen";
		}
	}
}
