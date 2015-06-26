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
	'BIRTHDAYSEND'					=> '<strong>Birthday mail send to</strong><br />» %s',
	'E_MAIL_ON_BIRTHDAY_NOTICE'		=> '<div style="width:80%;margin:20px auto;"><p style="text-align:center;">Settings for this extension are in General >> Board configuration >> Board features.</p></div>',
));
