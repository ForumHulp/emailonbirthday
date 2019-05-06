<?php
/**
*
* @package E-mail on birthday
* @copyright (c) 2015 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\emailonbirthday\migrations\v31x;

/**
 * Migration stage 2: Config data
 */
class m2_config_data extends \phpbb\db\migration\migration
{
	/**
	 * Assign migration file dependencies for this migration
	 *
	 * @return array Array of migration files
	 * @static
	 * @access public
	 */
	static public function depends_on()
	{
		return array('\forumhulp\emailonbirthday\migrations\v31x\m1_initial_schema');
	}

	/**
	 * Add or update data in the database
	 *
	 * @return array Array of table data
	 * @access public
	 */
	public function update_data()
	{
		return array(
			array('config.add', array('email_on_birthday', '1')),
			array('config.add', array('html_email_on_birthday', '0')),
			array('config.add', array('emailonbirthday_gc', '43200')),
			array('config.add', array('emailonbirthday_last_gc', '0', '1')),
			array('custom', array(array(&$this, 'update_email_on_birthday'))),
		);
	}

	public function update_email_on_birthday()
	{
		$sql = 'UPDATE ' . USERS_TABLE . ' SET email_on_birthday = ' . (time() - 86400);
		$this->db->sql_query($sql);
	}
}
