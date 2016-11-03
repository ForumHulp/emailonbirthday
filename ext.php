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
			return $this->notification_handler('enable', array(
				'forumhulp.emailonbirthday.notification.type.birthday',
			));

			break;

			default:

				// Run parent enable step method
				return parent::enable_step($old_state);

			break;
		}
	}

	/**
	 * Overwrite purge_step to purge BBdownloads system notifications before
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

				// Purge Forum BBdownloads system notifications
				return $this->notification_handler('purge', array(
					'forumhulp.emailonbirthday.notification.type.birthday',
				));

			break;

			default:

				// Run parent purge step method
				return parent::purge_step($old_state);

			break;
		}
	}

	/**
	 * Notification handler to call notification enable/disable/purge steps
	 *
	 * @param string $step               The step (enable, disable, purge)
	 * @param array  $notification_types The notification type names
	 * @return string Return notifications as temporary state
	 * @access protected
	 */
	protected function notification_handler($step, $notification_types)
	{
		$phpbb_notifications = $this->container->get('notification_manager');

		foreach ($notification_types as $notification_type)
		{
			call_user_func(array($phpbb_notifications, $step . '_notifications'), $notification_type);
		}

		return 'notifications';
	}
}
