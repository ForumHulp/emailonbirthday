<?php
/**
*
* @package E-mail on birthday
* @copyright (c) 2015 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\emailonbirthday;

/**
 * This ext class is optional and can be omitted if left empty.
 * However you can add special (un)installation commands in the
 * methods enable_step(), disable_step() and purge_step(). As it is,
 * these methods are defined in \phpbb\extension\base, which this
 * class extends, but you can overwrite them to give special
 * instructions for those cases.
 */
class ext extends \phpbb\extension\base
{
	public function is_enableable()
	{
		if (!class_exists('forumhulp\helper\helper'))
		{
			$this->container->get('user')->add_lang_ext('forumhulp/emailonbirthday', 'info_acp_emailonbirthday');
			trigger_error($this->container->get('user')->lang['FH_HELPER_NOTICE'], E_USER_WARNING);
		}

		if (!$this->container->get('ext.manager')->is_enabled('forumhulp/helper'))
		{
			$this->container->get('ext.manager')->enable('forumhulp/helper');
		}

		return class_exists('forumhulp\helper\helper');
	}

	public function enable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
			if (empty($old_state))
			{
				$this->container->get('user')->add_lang_ext('forumhulp/emailonbirthday', 'info_acp_emailonbirthday');
				$this->container->get('template')->assign_var('L_EXTENSION_ENABLE_SUCCESS', $this->container->get('user')->lang['EXTENSION_ENABLE_SUCCESS'] .
				(isset($this->container->get('user')->lang['E_MAIL_ON_BIRTHDAY_NOTICE']) ?
					sprintf($this->container->get('user')->lang['E_MAIL_ON_BIRTHDAY_NOTICE'],
							$this->container->get('user')->lang['ACP_CAT_GENERAL'],
							$this->container->get('user')->lang['ACP_BOARD_CONFIGURATION'],
							$this->container->get('user')->lang['ACP_BOARD_FEATURES']) : ''));
			}

			// Enable birthday system notifications
				$phpbb_notifications = $this->container->get('notification_manager');
				$phpbb_notifications->enable_notifications('forumhulp.emailonbirthday.notification.type.birthday');

				$sql = 'SELECT COUNT(item_type) AS total
						FROM ' . $this->container->getParameter('tables.user_notifications') . "
						WHERE item_type = '" . $this->container->get('dbal.conn')->sql_escape('forumhulp.emailonbirthday.notification.type.birthday') . "'";
				$this->container->get('dbal.conn')->sql_query($sql);
				$total = $this->container->get('dbal.conn')->sql_fetchfield('total');

				if (!$total)
				{
					$insert_buffer = new \phpbb\db\sql_insert_buffer($this->container->get('dbal.conn'), $this->container->getParameter('tables.user_notifications'));

					$config = $this->container->get('config');
					$method = (version_compare($config['version'], '3.2.*', '<')) ? '' : 'notification.method.board'; 
					$sql = 'SELECT user_id FROM ' . USERS_TABLE . ' WHERE ' . $this->container->get('dbal.conn')->sql_in_set('user_type', array(USER_INACTIVE, USER_IGNORE), true);
					$result = $this->container->get('dbal.conn')->sql_query($sql);
					while ($row = $this->container->get('dbal.conn')->sql_fetchrow($result))
					{
						$insert_buffer->insert(array(
							'item_type'	=> 'forumhulp.emailonbirthday.notification.type.birthday',
							'item_id'	=> 0,
							'user_id'	=> $row['user_id'],
							'method'	=> $method,
							'notify'	=> 1)
						);
						$insert_buffer->insert(array(
							'item_type'	=> 'forumhulp.emailonbirthday.notification.type.birthday',
							'item_id'	=> 0,
							'user_id'	=> $row['user_id'],
							'method'	=> 'notification.method.email',
							'notify'	=> 1)
						);
					}

					// Flush the buffer
					$insert_buffer->flush();
				}

				return 'notifications';
			break;

			default:

				// Run parent enable step method
				return parent::enable_step($old_state);

			break;
		}
	}

	/**
	* Overwrite disable_step to disable notifications
	* before the extension is disabled.
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	* @access public
	*/
	public function disable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet

				// Disable board rules notifications
				$phpbb_notifications = $this->container->get('notification_manager');
				$phpbb_notifications->disable_notifications('forumhulp.emailonbirthday.notification.type.birthday');
				return 'notifications';

			break;

			default:

				// Run parent disable step method
				return parent::disable_step($old_state);

			break;
		}
	}

	/**
	* Overwrite purge_step to purge notifications before
	* any included and installed migrations are reverted.
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	* @access public
	*/
	public function purge_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet

				// Purge notifications
				$phpbb_notifications = $this->container->get('notification_manager');
				$phpbb_notifications->purge_notifications('forumhulp.emailonbirthday.notification.type.birthday');
				return 'notifications';

			break;

			default:

				// Run parent purge step method
				return parent::purge_step($old_state);

			break;
		}
	}
}
