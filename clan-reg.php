<?php
/**
 * Plugin Name: Highland Games Clan Registration
 * Plugin URI: http://steampowercode.com
 * Description: Provides ui for registration and accepting payments on PayPal.
 * Version: 1.0
 * Author: Samuel J. Lawson
 * Author URI: http://sjlawson.sdf.org
 * License:
 */

defined('ABSPATH') or die("No script kiddies please!");
require_once('clan-reg/includes/ClanRegistration.class.php');

add_filter('the_content','show_clan_list');


function show_clan_list($content)
{
    if (strpos($content, '[CLANLIST]') === false) {
        return $content;
    }

    $app = new ClanRegistration();
    $clanListMarkup = $app->printCurrentRegistrations();
    if(empty($clanListMarkup)) {
        $clanListMarkup = "<pre>" . print_r($app, true) . "</pre>";
    }

    $content = str_replace('[CLANLIST]', $clanListMarkup, $content);

    return $content;
}
