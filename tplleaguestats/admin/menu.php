<?php

defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

$dirname = basename(dirname(dirname(__FILE__)));
$module_handler = xoops_gethandler('module');
$module = $module_handler->getByDirname($dirname);
$pathIcon32 = $module->getInfo('icons32');

xoops_loadLanguage('admin', $dirname);

$i = 0;

// Index
$adminmenu[$i]['title'] =  _MI_TPLLS_ADMENU0;
$adminmenu[$i]['link'] = "admin/index.php";
$adminmenu[$i]["icon"] = $pathIcon32.'/home.png';
$i++;

$adminmenu[$i]['title'] = _MI_TPLLS_ADMENU1;
$adminmenu[$i]['link'] = "admin/seasons.php";
$adminmenu[$i]["icon"] = './images/icons/32/karm.png';
$i++;

$adminmenu[$i]['title'] = _MI_TPLLS_ADMENU2;
$adminmenu[$i]['link'] = "admin/opponents.php";
$adminmenu[$i]["icon"] =  './images/icons/32/users.png';
$i++;

$adminmenu[$i]['title'] = _MI_TPLLS_ADMENU3;
$adminmenu[$i]['link'] = "admin/leaguematches.php";
$adminmenu[$i]["icon"] =  './images/icons/32/game.png';

$i++;
$adminmenu[$i]['title'] =  _MI_TPLLS_ABOUT;
$adminmenu[$i]['link'] =  "admin/about.php";
$adminmenu[$i]["icon"] = $pathIcon32.'/about.png';