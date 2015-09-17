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
	'HTML_EMAIL_ENABLED'			=> '(Only possible with other enabled extension, forumhulp\htmlemail)',
	'BIRTHDAYSEND'					=> '<strong>Birthday mail send to</strong><br />» %s',
	'E_MAIL_ON_BIRTHDAY_NOTICE'		=> '<div class="attach-image"><p>There are no settings for this extension. To use this function you have to switch html to on in your application. Use $messenger->use_html(true); to activate html email.</p></div>',
));
