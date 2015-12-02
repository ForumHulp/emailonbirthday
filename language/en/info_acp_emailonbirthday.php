<?php
/**
*
* @package E-mail on birthday
* @copyright (c) 2015 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'E_MAIL_ON_BIRTHDAY'			=> 'E-mail on your birthday',
	'E_MAIL_ON_BIRTHDAY_EXPLAIN'	=> 'Send every member a birthday email on his / her birthday',
	'HTML_EMAIL_ON_BIRTHDAY'		=> 'Use html in email',
	'HTML_EMAIL_ON_BIRTHDAY_EXPLAIN'=> 'Send a html birthday email instead of plain-text.',
	'HTML_EMAIL_ENABLED'			=> '(Only possible with enabled extension, <a href="https://github.com/ForumHulp/htmlemail" target="_blank">forumhulp\htmlemail</a>)',
	'BIRTHDAYSEND'					=> '<strong>Birthday mail send to</strong><br />» %s',
	'FH_HELPER_NOTICE'				=> 'Forumhulp helper application does not exist!<br />Download <a href="">forumhulp/helper</a> and copy the helper folder to your forumhulp extension folder.',
	'E_MAIL_ON_BIRTHDAY_NOTICE'		=> '<div class="phpinfo"><p class="entry">Settings for this extension are in %1$s » %2$s » %3$s. To use html in your birthday-mail you also need to enable <a href="https://github.com/ForumHulp/htmlemail" target="_blank">htmlemail</a> and change the text in your language folder email.</p></div>',
));
