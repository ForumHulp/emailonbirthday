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
	'BIRTHDAY_NOTIFICATION'			=> '<i class="icon fa-bell fa-fw" aria-hidden="true"></i><strong>Gefeliciteerd %1s</strong><br />met je %2s verjaardag'
));
