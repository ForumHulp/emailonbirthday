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
	'HTML_EMAIL_ENABLED'			=> '(Alleen bij ingeschakelde extensie forumhulp\htmlemail)',
	'BIRTHDAYSEND'					=> '<strong>Verjaardagskaart gestuurd naar</strong><br />» %s',
	'E_MAIL_ON_BIRTHDAY_NOTICE'		=> '<div class="attach-image"><p>Instellingen voor deze extensie vindt je in %1$s » %2$s » %3$s. Om html in je verjaardgsmail te te gebruiken installeer je ook <a href="https://github.com/ForumHulp/htmlemail" target="_blank">htmlemail</a> en past de mail aan aan je behoefte in de map language - email.</p></div>',
));
