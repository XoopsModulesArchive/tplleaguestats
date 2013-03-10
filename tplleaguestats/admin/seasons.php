<?php
/*
************************************************************
TPLLeagueStats is a league stats software designed for football (soccer)
team.

Copyright (C) 2003  Timo Leppänen / TPL Design
email:     info@tpl-design.com
www:       www.tpl-design.com/tplleaguestats

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

************************************************************
Ported to xoops by 
Mythrandir http://www.web-udvikling.dk
and 
ralf57 http://www.madeinbanzi.it

************************************************************
*/
include_once 'admin_header.php';
include '../../../include/cp_header.php'; //Include file, which checks for permissions and sets navigation

$seasonid = isset($_GET['season_id']) ? intval($_GET['season_id']) : 0;
$seasonname = isset($_GET['season_name']) ? $_GET['season_name'] : "";

$PHP_SELF = $_SERVER['PHP_SELF'];
$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
$action = isset($_GET['action']) ? $_GET['action'] : null;
$action = isset($_POST['action']) ? $_POST['action'] : $action;

$add_submit = isset($_POST['add_submit']) ? $_POST['add_submit'] : false;
$modify_submit = isset($_POST['modify_submit']) ? $_POST['modify_submit'] : false;
$delete_submit = isset($_POST['delete_submit']) ? $_POST['delete_submit'] : false;

xoops_cp_header();

$indexAdmin = new ModuleAdmin();
echo $indexAdmin->addNavigation('seasons.php');
if($add_submit)
{
    $name = $xoopsDB->quoteString(trim($_POST['name']));
    $drawline = trim($_POST['drawline']);
    
    //Query to check if there are already a submitted season name in the database
    $query = $xoopsDB->query("SELECT SeasonName FROM ".$xoopsDB->prefix("tplls_seasonnames")." WHERE SeasonName = $name");
    
    if($xoopsDB->getRowsNum($query) > 0)
    {
        echo "<font color='red'><b>". _AM_SEASONDUPLICATE."</b></font><br><br>";
        exit();
    }
    
    mysql_free_result($query);
    
    if($name != '')
    {
        $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("tplls_seasonnames")." SET
			SeasonName = $name,
			SeasonLine = '$drawline',
            SeasonDefault = '$defseason'");
        
        header("Location: $PHP_SELF");
    }
}
elseif($modify_submit)
{
    $name = $xoopsDB->quoteString(trim($_POST['name']));
    $drawline = trim($_POST['drawline']);
    $publish = $_POST['publish'];
    $seasonid = intval($_POST['seasonid']);
    $defseason = intval($_POST['defseason']);
    
    //
    //If published is checked
    //
    if(!isset($publish))
    {
        $publish = 0;
    }
    if(!isset($defseason))
    {
        $defseason = 0;
    }
    
    if($name != '')
    {
        //
        //If default season->delete the default status from the previous one
        //
        if($defseason == 1)
        {
            $xoopsDB->query("UPDATE ".$xoopsDB->prefix("tplls_seasonnames")." SET
				SeasonDefault = '0'");
        }
        $xoopsDB->query("UPDATE ".$xoopsDB->prefix("tplls_seasonnames")." SET
			SeasonName = $name,
			SeasonLine = '$drawline',
			SeasonPublish = '$publish',
            SeasonDefault = '$defseason'
			WHERE SeasonID = '$seasonid'");
    }
    
    header("Location: $HTTP_REFERER");
}
elseif($delete_submit)
{
    $seasonid = intval($_POST['seasonid']);
    
    //
    //Query to check if there are already matches in the season->can't delete
    //
    $query = $xoopsDB->query("SELECT M.LeagueMatchID
		FROM ".$xoopsDB->prefix("tplls_leaguematches")." M, ".$xoopsDB->prefix("tplls_seasonnames")." S
		WHERE M.LeagueMatchSeasonID = '$seasonid'");
    
    if($xoopsDB->getRowsNum($query) == 0)
    {
        $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("tplls_seasonnames")." WHERE SeasonID = '$seasonid'");
    }
    else
    {
        echo "<font color='red'><b>". _AM_SEASONHASMATCHES."</b></font><br><br>";
        exit();
    }
    
    header("Location: $PHP_SELF");
}


?>
	
	<?php
	include('head.php');
	?>
	<table align="center" width="600">
		<tr>
		<td>
		<?php
		if(!isset($action))
		{
		?>
		<form method="post" action="<?php echo "$PHP_SELF" ?>">
		<h3><?php echo _AM_ADDSEASON;?></h3>
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_SEASONNAMEYEARS;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="name">
			</td>
		</tr>

		<tr>
			<td align="left" valign="top">
			<?php echo _AM_SEASONDRAWLINE;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="drawline" value="" size="10">
			</td>
		</tr>
		</table>
		<input type="submit" name="add_submit" value="<?php echo _AM_SEASONADD;?>">
		</form>
		<?php
		}
		elseif($action == 'modify')
		{
		    $seasonid = intval($_REQUEST['season']);
		    $get_season = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("tplls_seasonnames")." WHERE SeasonID = '$seasonid' LIMIT 1");
		    $data = $xoopsDB->fetchArray($get_season);
		?>

		<form method="post" action="<?php echo "$PHP_SELF"?>">
		<h3><?php echo _AM_SEASONMODIFYDELETE;?></h3>
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_SEASONNAMEYEARS;?>
                        </td>
                        <td>
                        <input type="text" name="name" value="<?php echo $data['SeasonName'] ?>">
			<input type="hidden" name="seasonid" value="<?php echo $data['SeasonID'] ?>">
			</td>
	   </tr>
	   <tr>
		    <td align="left" valign="top">
			<?php echo _AM_DEFAULTSEASON;?>
			</td>
			<td align="left" valign="top">
			<?php
			
			if($data['SeasonDefault'] == 1)
			echo"<input type=\"checkbox\" name=\"defseason\" value=\"1\" CHECKED>\n";
			else
			echo"<input type=\"checkbox\" name=\"defseason\" value=\"1\">\n";
			
			?>
			</td>
		</tr>

		<tr>
			<td align="left" valign="top">
			<?php echo _AM_SEASONDRAWLINE;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="drawline" value="<?= $data['SeasonLine'] ?>" size="10">
			</td>
		</tr>

		<tr>
			<td align="left" valign="top">
			<?php echo _AM_SEASONPUBLISHED;?>
			</td>
			<td align="left" valign="top">
			<?php
			//
			//If season is published
			//
			if($data['SeasonPublish'] == 1)
			echo'<input type="checkbox" name="publish" value="1" CHECKED>';
			else
			echo'<input type="checkbox" name="publish" value="1">';
			
			?>
			</td>
		</tr>

		</table>
		<input type="submit" name="modify_submit" value="<?php echo _AM_SEASONMODIFY;?>"> <input type="submit" name="delete_submit" value="<?php echo _AM_SEASONDELETE;?>">
		</form>

		<a href="<?php echo "$PHP_SELF"?>"><?php echo _AM_ADDSEASON;?></a>

		<?php
		mysql_free_result($get_season );
		}
		?>
		</td>

		<td align="left" valign="top">
		<?php
		$get_seasons = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("tplls_seasonnames")." ORDER BY SeasonName");
		
		if($xoopsDB->getRowsNum($get_seasons) < 1)
		{
		    echo "<b>"._AM_NOSEASONS."</b>";
		}
		else
		{
		    echo "<b>". _AM_SEASONSAVAILABLE."</b><br><br>";
		    
		    while($data = $xoopsDB->fetchArray($get_seasons))
		    {
		        echo "<a href=\"$PHP_SELF?action=modify&amp;season=$data[SeasonID]\">$data[SeasonName]</a>";
		        
		        //
		        //Season published?
		        //
		        if($data['SeasonPublish'] == 0)
		        echo "&nbsp;" ._AM_SEASONNP."<br>\n";
		        else
		        echo"<br>\n";
		    }
		}
		
		?>
		<br><br>
		<?php echo _AM_SEASONNOTE;?>
		</td>
		</tr>
	</table>
	

<?php
xoops_cp_footer();
?>