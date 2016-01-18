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

if (isset($_POST['season_select'])) {
    $season = explode("____",$_POST['season_select']);
}
elseif (isset($_POST['seasonid'])) {
    $season = array (intval($_POST['seasonid']), $_POST['seasonname']);
}
elseif (!isset($_SESSION['season_id'])) {
    $sql = "SELECT SeasonID, SeasonName FROM ".$xoopsDB->prefix("tplls_seasonnames")." WHERE SeasonDefault=1";
    $seasonname = $xoopsDB->query($sql);
    $seasonname = $xoopsDB->fetchArray($seasonname);
    $season = array($seasonname['SeasonID'], $seasonname['SeasonName']);
}
else {
    $season = array(intval($_SESSION['season_id']), $_SESSION['season_name']);
}
$_SESSION['season_id'] = $season[0];
$_SESSION['season_name'] = $season[1];
$seasonid = $_SESSION['season_id'];
$seasonname = $_SESSION['season_name'];

$PHP_SELF = $_SERVER['PHP_SELF'];
$action = isset($_GET['action']) ? $_GET['action'] : null;
$action = isset($_POST['action']) ? $_POST['action'] : $action;

$add_submit = isset($_POST['add_submit']) ? $_POST['add_submit'] : false;
$modify_submit = isset($_POST['modify_submit']) ? $_POST['modify_submit'] : false;
$delete_submit = isset($_POST['delete_submit']) ? $_POST['delete_submit'] : false;

$d_points_add = isset($_POST['d_points_add']) ? $_POST['d_points_add'] : null;
$d_points_modify = isset($_POST['d_points_modify']) ? $_POST['d_points_modify'] : null;

xoops_cp_header();

$indexAdmin = new ModuleAdmin();
echo $indexAdmin->addNavigation('opponents.php');

//
//Add
//
if($add_submit)
{
    $opponent = trim($_POST['opponent']);
    $opponent = $xoopsDB->quoteString($opponent);
    //query to check if there are already a team with submitted name
    $query = $xoopsDB->query("SELECT OpponentName FROM ".$xoopsDB->prefix("tplls_opponents")." WHERE OpponentName = $opponent");
    
    if($xoopsDB->getRowsNum($query) > 0)
    {
        echo "<font color='red'><b>". _AM_TEAMDUPLICATE."</b></font><br><br>";
        exit();
    }
    
    if($opponent != '')
    {
        $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("tplls_opponents")." SET OpponentName = $opponent");
        
        header("Location: $PHP_SELF");
    }
}
//
//Modify
//
elseif($modify_submit)
{
    $opponent = $xoopsDB->quoteString(trim($_POST['opponent']));
    $opponentid = intval($_POST['opponentid']);
    $own = $_POST['own'];
    //
    //Checked own
    //
    if(!isset($own))
    {
        $own = 0;
    }
    
    if($opponent != '')
    {
        //
        //If own team->delete the own status from the previous one
        //
        if($own == 1)
        {
            $xoopsDB->query("UPDATE ".$xoopsDB->prefix("tplls_opponents")." SET
				OpponentOwn = '0'
				WHERE OpponentOwn = '1'
				");
        }
        
        $xoopsDB->query("UPDATE ".$xoopsDB->prefix("tplls_opponents")." SET
			OpponentName = $opponent,
			OpponentOwn = '$own'
			WHERE OpponentID = $opponentid");
    }
    
    header("Location: $HTTP_REFERER");
}
//
//Delete
//
elseif($delete_submit)
{
    $opponentid = intval($_POST['opponentid']);
    
    //
    //query to check, if team already exists in the leaguetables
    //
    $query = $xoopsDB->query("SELECT LeagueMatchID
		FROM ".$xoopsDB->prefix("tplls_leaguematches")."
		WHERE LeagueMatchHomeID = $opponentid OR LeagueMatchAwayID = $opponentid");
    
    if($xoopsDB->getRowsNum($query) == 0)
    {
        $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("tplls_opponents")." WHERE OpponentID = $opponentid");
        
        header("Location: $PHP_SELF");
    }
    else
    {
        echo "<font color='red'><b>". _AM_TEAMISINUSE."</b></font><br><br>";
        exit();
    }
}
//
//Deducted points
//
elseif($d_points_add)
{
    $d_points = intval($_POST['d_points']);
    $teamid = intval($_POST['teamid']);
    $seasonid = intval($_POST['seasonid']);
    
    if(is_numeric($d_points) && $d_points != '')
    {
        //
        //Adds
        //
        $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("tplls_deductedpoints")." SET
			seasonid = $seasonid,
			teamid = $teamid,
			points = $d_points");
    }
    
    header("Location: $HTTP_REFERER");
}
//
//Modify of deducted points
//
elseif($d_points_modify)
{
    $d_points = intval($_POST['d_points']);
    $id = intval($_POST['id']);
    
    if(is_numeric($d_points) && $d_points != '')
    {
        //
        //Delete deducted points if zero is written
        //
        if($d_points == 0)
        {
            $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("tplls_deductedpoints")."
				WHERE id = $id");
        }
        //
        //Modify if some other number
        //
        else
        {
            $xoopsDB->query("UPDATE ".$xoopsDB->prefix("tplls_deductedpoints")." SET
				points = $d_points
				WHERE id = $id");
        }
    }
    
    header("Location: $HTTP_REFERER");
}


?>
	
	<?php
	include('head.php');
	?>
	<table align="center" width="600">
		<tr>
		<td align="left" valign="top">
		<?php
		if(!isset($action))
		{
		?>
		<form method="post" action="<?php echo "$PHP_SELF"?>">
		<h3><?php echo _AM_ADDNEWTEAM;?></h3>
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_TEAMNAME;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="opponent">
			</td>
		</tr>
		</table>
		<input type="submit" name="add_submit" value="<?php echo _AM_ADDTEAM;?>">
		</form>
		<?php
		}
		elseif($action == 'modify')
		{
		    $opponentid = intval($_REQUEST['opponent']);
		    $get_opponent = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("tplls_opponents")." WHERE OpponentID = $opponentid LIMIT 1");
		    $data = $xoopsDB->fetchArray($get_opponent);
		?>

		<form method="post" action="<?php echo "$PHP_SELF"?>">
		<h3><?php echo _AM_TEAMMODIFYDELETE;?></h3>
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_TEAMNAME;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="opponent" value="<?php echo $data['OpponentName'] ?>">
			<input type="hidden" name="opponentid" value="<?php echo $data['OpponentID'] ?>">
			</td>
		</tr>

		<tr>
			<td align="left" valign="top">
			<?php echo _AM_TEAMISYOURS;?>
			</td>
			<td align="left" valign="top">
			<?php
			
			if($data['OpponentOwn'] == 1)
			echo"<input type=\"checkbox\" name=\"own\" value=\"1\" CHECKED>\n";
			else
			echo"<input type=\"checkbox\" name=\"own\" value=\"1\">\n";
			
			?>
			</td>
		</tr>

		</table>
		<input type="submit" name="modify_submit" value="<?php echo _AM_TEAMMODIFY;?>"> <input type="submit" name="delete_submit" value="<?php echo _AM_TEAMDELETE;?>">
		</form>

		<a href="<?php echo "$PHP_SELF" ?>"><?php echo _AM_ADDNEWTEAM;?></a>

		<h3><?php echo _AM_DEDPTS;?></h3>

		<?php
		
		//
		//Check if there are deducted points
		//
		
		echo"<b>$seasonname</b><br><br>";
		
		$get_deduct = $xoopsDB->query("SELECT points, id
		FROM ".$xoopsDB->prefix("tplls_deductedpoints")."
		WHERE seasonid = $seasonid AND teamid = $opponentid
		LIMIT 1
		");
		
		if($xoopsDB->getRowsNum($get_deduct) == 0)
		{
		    echo"
			<form method=\"POST\" action=\"$PHP_SELF\">"
		    ._AM_ADDDEDPTS.
		    "<input type=\"text\" size=\"2\" name=\"d_points\">
			<input type=\"hidden\" value=\"$opponentid\" name=\"teamid\">
		    <input type=\"hidden\" value=\"$seasonid\" name=\"seasonid\">
			<input type=\"submit\" value="._AM_ADEDPTS." name=\"d_points_add\">
			</form>
			";
		}
		else
		{
		    $data = $xoopsDB->fetchArray($get_deduct);
		    
		    echo"
			<form method=\"POST\" action=\"$PHP_SELF\">"
		    ._AM_MODDEDPTS.
		    "<input type=\"text\" size=\"2\" name=\"d_points\" value=\"$data[points]\">
			<input type=\"hidden\" value=\"$data[id]\" name=\"id\">
			<input type=\"submit\" value="._AM_MDEDPTS." name=\"d_points_modify\">
			</form>
			";
		}
		
		mysql_free_result($get_deduct);
		
		?>

		<?php
		mysql_free_result($get_opponent);
		}
		?>
		</td>

		<td align="left" valign="top">
		<?php
		$get_opponents = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("tplls_opponents")." ORDER BY OpponentName");
		
		if($xoopsDB->getRowsNum($get_opponents) < 1)
		{
		    echo "<b>". _AM_NOTEAMSAVAILABLE."</b><br><br>";
		}
		else
		{
		    echo "<b>". _AM_TEAMSAVAILABLE."</b><br><br>";
		    
		    while($data = $xoopsDB->fetchArray($get_opponents))
		    {
		        echo "<a href=\"$PHP_SELF?action=modify&amp;opponent=$data[OpponentID]\">$data[OpponentName]</a>";
		        
		        if($data['OpponentOwn'] == 1)
		        echo "&nbsp;"._AM_YT. "<br>\n";
		        else
		        echo"<br>\n";
		    }
		}
		
		?>

		<br><br>
		<?php echo _AM_YOURTEAM;?>
		</td>
		</tr>
	</table>
<?php
xoops_cp_footer();
?>