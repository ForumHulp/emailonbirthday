<?php
/**
*
* @package E-mail on birthday
* @copyright (c) 2015 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\emailonbirthday\migrations\v31x;

use phpbb\db\migration\container_aware_migration;

/**
 * Migration stage 1: Initial schema
 */
class m1_initial_schema extends \phpbb\db\migration\migration
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
		return array('\phpbb\db\migration\data\v310\gold');
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
}
