<?php

/**
 * The purpose of this script is to handle the throwing the various types of error page and loading them prettily onto the screen
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.9.1
 */

use App\Structure\Footer;
use App\Structure\TopBar;
use Framework\Auth\SitePermissions;
use App\Structure\HTMLHeader as Header;
use App\Structure\NavSideBar as SideBar;

$path = explode( 'src', $_SERVER['DOCUMENT_ROOT'] )[0];

require "{$path}/src/includes/general-loader.php";

@session_start();

require CONFIG_FILE;

$permit = new SitePermissions;

Header::draw( $permit->logged_in );

$permit->check_logout();
$permit->check_login();

TopBar::draw();
SideBar::draw();

$page = isset ( $_GET['p'] ) ? $_GET['p'] : '404';
require "{$page}.php";

Footer::draw();