<?php
/**
*
* @package E-mail on birthday
* @copyright (c) 2015 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\emailonbirthday\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	protected $helper;

	/**
	* Constructor
	*/
	public function __construct(\phpbb\controller\helper $helper)
	{
		$this->helper = $helper;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'					=> 'load_language_on_setup',
			'core.acp_board_config_edit_add'	=> 'load_config_on_setup',
		);
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext 		= $event['lang_set_ext'];
		$lang_set_ext[] 	= array(
			'ext_name' 		=> 'forumhulp/emailonbirthday',
			'lang_set' 		=> 'emailonbirthday',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function load_config_on_setup($event)
	{
		if ($event['mode'] == 'features')
		{
			$display_vars = $event['display_vars'];

			$add_config_var = array(
				'email_on_birthday' =>
					array(
						'lang'		=> 'E_MAIL_ON_BIRTHDAY',
						'validate'	=> 'bool',
						'type'		=> 'radio:yes_no',
						'explain'	=> true),
				'html_email_on_birthday' =>
					array(
						'lang'		=> 'HTML_EMAIL_ON_BIRTHDAY',
						'validate'	=> 'bool',
						'type'		=> 'custom',
						'function'	=> __NAMESPACE__.'\listener::html_email_on_birthday',
						'explain'	=> true),
			);

			$display_vars['vars'] = phpbb_insert_config_array($display_vars['vars'], $add_config_var, array('after' =>'allow_quick_reply'));
			$event['display_vars'] = array('title' => $display_vars['title'], 'vars' => $display_vars['vars']);
		}
	}

	static function html_email_on_birthday($value, $key)
	{
		global $config, $user, $phpbb_container;

		$manager = $phpbb_container->get('ext.manager');
		$use_html = $manager->is_enabled('forumhulp/htmlemail');

		$radio_ary = array(1 => 'YES', 0 => 'NO');
		return str_replace('value="1"', 'value="1"' . ((!$use_html) ? 'disabled' : ''), h_radio('config[html_email_on_birthday]', $radio_ary, (!$use_html) ? 0 : $value) . ((!$use_html) ? $user->lang['HTML_EMAIL_ENABLED'] : ''));
	}
}
