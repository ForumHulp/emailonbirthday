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
	public function enable_step($old_state)
	{
		if (empty($old_state))
		{
			global $user;
			$user->add_lang_ext('forumhulp/emailonbirthday', 'info_acp_emailonbirthday');
			$user->lang['EXTENSION_ENABLE_SUCCESS'] .= (isset($user->lang['E_MAIL_ON_BIRTHDAY_NOTICE']) ? sprintf($user->lang['E_MAIL_ON_BIRTHDAY_NOTICE'], $user->lang['ACP_CAT_GENERAL'], $user->lang['ACP_BOARD_CONFIGURATION'], $user->lang['ACP_BOARD_FEATURES']) : '');
		}
		// Run parent enable step method
		return parent::enable_step($old_state);
	}
}
