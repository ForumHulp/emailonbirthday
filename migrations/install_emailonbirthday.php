<?php
/**
*
* @package E-mail on birthday
* @copyright (c) 2015 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\emailonbirthday\migrations;

class install_emailonbirthday extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['email_on_birthday_version']) && version_compare($this->config['email_on_birthday_version'], '3.1.2', '>=');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'email_on_birthday'		=> array('UINT:11', 0)
				)
			)
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				USERS_TABLE	=> array(
					'email_on_birthday',
				),
			)
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('email_on_birthday', '1')),
			array('config.add', array('email_on_birthday_gc', '43200')),
			array('config.add', array('email_on_birthday_last_gc', '0', '1')),
			array('config.add', array('email_on_birthday_version', '3.1.1')),
			array('custom', array(array(&$this, 'update_email_on_birthday'))),
		);
	}

	public function update_email_on_birthday()
	{
		$sql = 'UPDATE ' . USERS_TABLE . ' SET email_on_birthday = ' . time();
		$this->db->sql_query($sql);
	}
}
