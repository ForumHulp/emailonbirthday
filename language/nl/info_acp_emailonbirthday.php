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
	'E_MAIL_ON_BIRTHDAY'			=> 'E-mail op je verjaardag',
	'E_MAIL_ON_BIRTHDAY_EXPLAIN'	=> 'Stuur ieder lid een email op zijn verjaardag',
	'HTML_EMAIL_ON_BIRTHDAY'		=> 'Gebruik html in je  email',
	'HTML_EMAIL_ON_BIRTHDAY_EXPLAIN'=> 'Stuur een html email in plaats van plain-tekst.',
	'BIRTHDAYSEND'					=> '<strong>Verjaardagskaart gestuurd naar</strong><br />» %s',
	'E_MAIL_ON_BIRTHDAY_NOTICE'		=> '<div style="width:80%;margin:20px auto;"><p style="text-align:left;">Settings for this extension are in General >> Board configuration >> Board features.</p></div>'
));
