<?php
// $Id: xoops_version.php,v 1.3 2004/02/13 14:29:41 mithyt2 Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
$modversion['name'] = "TPL League Stats";
$modversion['version'] = 1.1;
$modversion['description'] = 'A nice league stats software';
$modversion['credits'] = "Mithrandir and ralf57";
$modversion['help']        = 'page=help';
$modversion['license']     = 'GNU GPL 2.0';
$modversion['license_url'] = "www.gnu.org/licenses/gpl-2.0.html/";
$modversion['official'] = 0;
$modversion['image'] = "images/tplleague_slogo.png";
$modversion['dirname'] = "tplleaguestats";
$modversion['dirmoduleadmin'] = '/Frameworks/moduleclasses/moduleadmin';
$modversion['icons16'] = '../../Frameworks/moduleclasses/icons/16';
$modversion['icons32'] = '../../Frameworks/moduleclasses/icons/32';

//about
$modversion["module_website_url"] = "http://www.xoops.org/";
$modversion["module_website_name"] = "XOOPS";
$modversion["release_date"] = "2012/03/14";
$modversion["module_status"] = "Beta";
$modversion["author_website_url"] = "http://www.xoops.org/";
$modversion["author_website_name"] = "XOOPS";
$modversion['min_php']='5.2';
$modversion['min_xoops']='2.5';
$modversion['min_admin']='1.1';
$modversion['min_db']= array('mysql'=>'5.0.7', 'mysqli'=>'5.0.7');

// Sql file (must contain sql generated by phpMyAdmin or phpPgAdmin)
// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";


// Tables created by sql file (without prefix!)
$modversion['tables'][0] = "tplls_deductedpoints";
$modversion['tables'][1] = "tplls_leaguematches";
$modversion['tables'][2] = "tplls_opponents";
$modversion['tables'][3] = "tplls_seasonnames";

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

// Templates
// No templates yet

// Blocks
$modversion['blocks'][1]['file'] = "minitable.php";
$modversion['blocks'][1]['name'] = _MI_MINITABLE;
$modversion['blocks'][1]['description'] = "Shows default season";
$modversion['blocks'][1]['show_func'] = "b_minitable_show";

// Menu
$modversion['hasMain'] = 1;
$modversion['sub'][1]['name'] = _MI_MENU_HEAD2HEAD;
$modversion['sub'][1]['url'] = "headtohead.php";
$modversion['sub'][2]['name'] = _MI_MENU_SEASSTATS;
$modversion['sub'][2]['url'] = "season.php";

// Search
$modversion['hasSearch'] = 0;
/* No search
$modversion['search']['file'] = "include/search.inc.php";
$modversion['search']['func'] = "news_search";
*/

// Comments
$modversion['hasComments'] = 0;

// Notification
$modversion['hasNotification'] = 0;


// Config Settings (only for modules that need config settings generated automatically) 
// name of config option for accessing its specified value. i.e. $xoopsModuleConfig['storyhome']

global $xoopsDB;
$get_seasons = $xoopsDB->query("SELECT SeasonID, SeasonName FROM ".$xoopsDB->prefix("tplls_seasonnames")." WHERE SeasonPublish = '1' ORDER BY SeasonName");
while($thisseason = $xoopsDB->fetchArray($get_seasons)) {
    $allseasons[$thisseason['SeasonName']] = $thisseason['SeasonID'];
}
$modversion['config'][1] = array('name' => 'defaulttable', 'title' => '_MI_PREFDEFLEAGTAB', 'description' => '', 'formtype' => 'select', 'valuetype' => 'int', 'default' => 1);
$modversion['config'][1]['options'] = array(_MI_PREFTABSIM => 4, _MI_PREFTABTRA => 1, _MI_PREFTABMAT => 2, _MI_PREFTABREC => 3);

$modversion['config'][2] = array('name' => 'defaultshow', 'title' => '_MI_PREFCAL', 'description' => '', 'formtype' => 'select', 'valuetype' => 'int', 'default' => 1);
$modversion['config'][2]['options'] = array(_MI_PREFCALALL => 1, _MI_PREFCALOWN => 2, _MI_PREFCALNONE => 3);

$modversion['config'][3]= array('name' => 'printdate', 'title' => '_MI_PREFDATE', 'description' => '', 'formtype' => 'select', 'valuetype' => 'int', 'default' => 1);
$modversion['config'][3]['options'] = array(_MI_PREFDATE1 => 1, _MI_PREFDATE2 => 2, _MI_PREFDATE3 => 3);

$modversion['config'][4] = array('name' => 'forwin', 'title' => '_MI_PREFPTSWIN', 'description' => '', 'formtype' => 'textbox', 'valuetype' => 'int', 'default' => 3);

$modversion['config'][5] = array('name' => 'fordraw', 'title' => '_MI_PREFPTSDRAW', 'description' => '', 'formtype' => 'textbox', 'valuetype' => 'int', 'default' => 1);

$modversion['config'][6] = array('name' => 'forloss', 'title' => '_MI_PREFPTSLOSS', 'description' => '', 'formtype' => 'textbox', 'valuetype' => 'int', 'default' => 0);

$modversion['config'][7] = array('name' => 'topoftable', 'title' => '_MI_PREFTOPCOL', 'description' => '', 'formtype' => 'textbox', 'valuetype' => 'text', 'default' => '#CCCCCC');

$modversion['config'][8] = array('name' => 'bg1', 'title' => '_MI_PREFLISTTMBG', 'description' => '', 'formtype' => 'textbox', 'valuetype' => 'text', 'default' => '#DEDEDE');

$modversion['config'][9] = array('name' => 'bg2', 'title' => '_MI_PREFMAINCOLSBG', 'description' => '', 'formtype' => 'textbox', 'valuetype' => 'text', 'default' => '#FFFFCC');

$modversion['config'][10] = array('name' => 'inside', 'title' => '_MI_PREFTTABBGCOL', 'description' => '', 'formtype' => 'textbox', 'valuetype' => 'text', 'default' => '#FFFFFF');

$modversion['config'][11] = array('name' => 'bordercolour', 'title' => '_MI_PREFTABBDRCOL', 'description' => '', 'formtype' => 'textbox', 'valuetype' => 'text', 'default' => '#CCCCCC');

$modversion['config'][12] = array('name' => 'tablewidth', 'title' => '_MI_PREFWIDTH', 'description' => '', 'formtype' => 'textbox', 'valuetype' => 'int', 'default' => '650');

?>