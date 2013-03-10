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
    $season = array ($_POST['seasonid'], $_POST['seasonname']);
}
elseif (!isset($_SESSION['season_id'])) {
    $sql = "SELECT SeasonID, SeasonName FROM ".$xoopsDB->prefix("tplls_seasonnames")." WHERE SeasonDefault=1";
    $seasonname = $xoopsDB->query($sql);
    $seasonname = $xoopsDB->fetchArray($seasonname);
    $season = array($seasonname['SeasonID'], $seasonname['SeasonName']);
}
else {
    $season = array($_SESSION['season_id'], $_SESSION['season_name']);
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
$modifyall_submit = isset($_POST['modifyall_submit']) ? $_POST['modifyall_submit']: null;

xoops_cp_header();

$indexAdmin = new ModuleAdmin();
echo $indexAdmin->addNavigation('leaguematches.php');

//
//Exit check, if there are less than 2 teams in database
//
$query = $xoopsDB->query("SELECT OpponentID FROM ".$xoopsDB->prefix("tplls_opponents"));

if($xoopsDB->getRowsNum($query) < 2)
{
//    echo "<br><br>"._AM_ADDTWOTEAMS."<br><br>
//		<a href=\"opponents.php\">" ._AM_ADDTEAMS. "</a>";
//    exit();
    redirect_header("opponents.php",1,_AM_ADDTWOTEAMS);
}

if($add_submit)
{
    $year = intval($_POST['year']);
    $month = intval($_POST['month']);
    $day = intval($_POST['day']);
    $dateandtime = $year."-".$month."-".$day;
    
    //
    //Check the data of the submitted form
    //
    $i = 0;
    
    while($i < 15)
    {
        $home = $_POST['home'];	//hometeam id
        $away = $_POST['away'];	//awayteam id
        $home_goals = $_POST['home_goals'];
        $away_goals = $_POST['away_goals'];
        
        //
        //Set the default
        //
        $home_winner = -1;
        $home_loser = -1;
        $home_tie = -1;
        $away_winner = -1;
        $away_loser = -1;
        $away_tie = -1;
        
        //
        //If home and away are not the same
        //
        if($home[$i] != $away[$i])
        {
            $home[$i] = intval($home[$i]);
            $away[$i] = intval($away[$i]);
            $home_goals[$i] = $home_goals[$i] != null ? intval($home_goals[$i]) : null;
            $away_goals[$i] = $away_goals[$i] != null ? intval($away_goals[$i]) : null;
            //
            //Hometeam wins
            //
            if($home_goals[$i] > $away_goals[$i])
            {
                $home_winner = $home[$i];
                $away_loser = $away[$i];
            }
            
            //
            //Away win
            //
            elseif($home_goals[$i] < $away_goals[$i])
            {
                $away_winner = $away[$i];
                $home_loser = $home[$i];
            }
            
            //
            //Draw
            //
            elseif($home_goals[$i] == $away_goals[$i])
            {
                $home_tie = $home[$i];
                $away_tie = $away[$i];
            }
            
            //
            //query to check if homea or away team already exists in the current day
            //
            $query = $xoopsDB->query("SELECT LM.LeagueMatchID FROM
				".$xoopsDB->prefix("tplls_leaguematches")." LM
				WHERE
				(LM.LeagueMatchHomeID = '$home[$i]' OR
				LM.LeagueMatchAwayID = '$home[$i]' OR
				LM.LeagueMatchHomeID = '$away[$i]' OR
				LM.LeagueMatchAwayID = '$away[$i]') AND
				LM.LeagueMatchDate = '$dateandtime'
				")
            ;
            
            if($xoopsDB->getRowsNum($query) == 0)
            {
                if (($home_goals[$i] !== null) && ($home_goals[$i] !== null)) {
                    $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("tplls_leaguematches")." SET
						LeagueMatchSeasonID = '$seasonid',
						LeagueMatchDate = '$dateandtime',
						LeagueMatchHomeID = '$home[$i]',
						LeagueMatchAwayID = '$away[$i]',
						LeagueMatchHomeWinnerID = '$home_winner',
						LeagueMatchHomeLoserID = '$home_loser',
						LeagueMatchAwayWinnerID = '$away_winner',
						LeagueMatchAwayLoserID = '$away_loser',
						LeagueMatchHomeTieID = '$home_tie',
						LeagueMatchAwayTieID = '$away_tie',
						LeagueMatchHomeGoals = '$home_goals[$i]',
						LeagueMatchAwayGoals = '$away_goals[$i]',
                        LeagueMatchCreated = ".time()."
						");
                }
                else {
                    $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("tplls_leaguematches")." SET
						LeagueMatchSeasonID = '$seasonid',
						LeagueMatchDate = '$dateandtime',
						LeagueMatchHomeID = '$home[$i]',
						LeagueMatchAwayID = '$away[$i]',
						LeagueMatchHomeWinnerID = '-1',
						LeagueMatchHomeLoserID = '-1',
						LeagueMatchAwayWinnerID = '-1',
						LeagueMatchAwayLoserID = '-1',
						LeagueMatchHomeTieID = '-1',
						LeagueMatchAwayTieID = '-1',
                        LeagueMatchCreated = ".time()."
						");
                }

            }
                
        }
        $i++;
    }
}
elseif($modifyall_submit)
{
    $year = intval($_POST['year']);
    $month = intval($_POST['month']);
    $day = intval($_POST['day']);
    $dateandtime = $year."-".$month."-".$day;
    $qty = intval($_POST['qty']);
    
    //
    //Delete old data from selected date
    //
    $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("tplls_leaguematches")."
		WHERE LeagueMatchDate = '$dateandtime'
		");
    
    //
    //Check the submitted form
    //
    $i = 0;
    $home = $_POST['home'];	//hometeam id
    $away = $_POST['away'];	//awayteam id
    $home_goals = $_POST['home_goals'];
    $away_goals = $_POST['away_goals'];
    while($i < $qty)
    {
        $home[$i] = intval($home[$i]);
        $away[$i] = intval($away[$i]);
        $home_goals[$i] = $home_goals[$i] != null ? intval($home_goals[$i]) : null;
        $away_goals[$i] = $away_goals[$i] != null ? intval($away_goals[$i]) : null;
        //
        //Set default
        //
        $home_winner = -1;
        $home_loser = -1;
        $home_tie = -1;
        $away_winner = -1;
        $away_loser = -1;
        $away_tie = -1;
        
        //
        //Home wins
        //
        if($home_goals[$i] > $away_goals[$i])
        {
            $home_winner = $home[$i];
            $away_loser = $away[$i];
        }
        //
        //Away wins
        //
        elseif($home_goals[$i] < $away_goals[$i])
        {
            $away_winner = $away[$i];
            $home_loser = $home[$i];
        }
        //
        //Draw
        //
        elseif($home_goals[$i] == $away_goals[$i])
        {
            $home_tie = $home[$i];
            $away_tie = $away[$i];
        }
        if (($home_goals[$i] !== null) && ($away_goals[$i] !== null)) {
            $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("tplls_leaguematches")." SET
				LeagueMatchSeasonID = '$seasonid',
				LeagueMatchDate = '$dateandtime',
				LeagueMatchHomeID = '$home[$i]',
				LeagueMatchAwayID = '$away[$i]',
				LeagueMatchHomeWinnerID = '$home_winner',
				LeagueMatchHomeLoserID = '$home_loser',
				LeagueMatchAwayWinnerID = '$away_winner',
				LeagueMatchAwayLoserID = '$away_loser',
				LeagueMatchHomeTieID = '$home_tie',
				LeagueMatchAwayTieID = '$away_tie',
				LeagueMatchHomeGoals = '$home_goals[$i]',
				LeagueMatchAwayGoals = '$away_goals[$i]',
                LeagueMatchCreated = ".time()."
				");
        }
        else {
            $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("tplls_leaguematches")." SET
						LeagueMatchSeasonID = '$seasonid',
						LeagueMatchDate = '$dateandtime',
						LeagueMatchHomeID = '$home[$i]',
						LeagueMatchAwayID = '$away[$i]',
						LeagueMatchHomeWinnerID = '-1',
						LeagueMatchHomeLoserID = '-1',
						LeagueMatchAwayWinnerID = '-1',
						LeagueMatchAwayLoserID = '-1',
						LeagueMatchHomeTieID = '-1',
						LeagueMatchAwayTieID = '-1',
                        LeagueMatchCreated = ".time()."
						");
        }            
        $i++;
    }
}
elseif($modify_submit)
{
    $mid = intval($_POST['mid']);
    $homeid = intval($_POST['homeid']);
    $awayid = intval($_POST['awayid']);
    $year = intval($_POST['year']);
    $month = intval($_POST['month']);
    $day = intval($_POST['day']);
    $dateandtime = $year."-".$month."-".$day;
    
    $home = intval($_POST['home']);	//kotijoukkueen id
    $away = intval($_POST['away']);	//vierasjoukkueen id
    $home_goals = $_POST['home_goals'] != null ? intval($_POST['home_goals']) : null;
    $away_goals = $_POST['home_goals'] != null ? intval($_POST['away_goals']) : null;
    
    //
    //Set default
    //
    $home_winner = -1;
    $home_loser = -1;
    $home_tie = -1;
    $away_winner = -1;
    $away_loser = -1;
    $away_tie = -1;
    
    //
    //Check that home and away are not the same
    //
    if($home != $away)
    {
        //
        //Home wins
        //
        if($home_goals > $away_goals)
        {
            $home_winner = $home;
            $away_loser = $away;
        }
        
        //
        //Away wins
        //
        elseif($home_goals < $away_goals)
        {
            $away_winner = $away;
            $home_loser = $home;
        }
        
        //
        //Draw
        //
        elseif($home_goals == $away_goals)
        {
            $home_tie = $home;
            $away_tie = $away;
        }
        
        //
        //query to check if home or away team already exists in the current day
        //
        $query = $xoopsDB->query("SELECT LM.LeagueMatchID FROM
			".$xoopsDB->prefix("tplls_leaguematches")." LM
			WHERE
			(LM.LeagueMatchHomeID = '$home' OR
			LM.LeagueMatchAwayID = '$home' OR
			LM.LeagueMatchHomeID = '$homeid' OR
			LM.LeagueMatchAwayID = '$homeid' OR
			LM.LeagueMatchHomeID = '$away' OR
			LM.LeagueMatchAwayID = '$away' OR
			LM.LeagueMatchHomeID = '$awayid' OR
			LM.LeagueMatchAwayID = '$awayid') AND
			LM.LeagueMatchDate = '$dateandtime'
			");
        
        if($xoopsDB->getRowsNum($query) < 2)
        {
            if (($home_goals !== null) && ($away_goals !== null)) {
                $xoopsDB->query("UPDATE ".$xoopsDB->prefix("tplls_leaguematches")." SET
					LeagueMatchDate = '$dateandtime',
					LeagueMatchHomeID = '$home',
					LeagueMatchAwayID = '$away',
					LeagueMatchHomeWinnerID = '$home_winner',
					LeagueMatchHomeLoserID = '$home_loser',
					LeagueMatchAwayWinnerID = '$away_winner',
					LeagueMatchAwayLoserID = '$away_loser',
					LeagueMatchHomeTieID = '$home_tie',
					LeagueMatchAwayTieID = '$away_tie',
					LeagueMatchHomeGoals = '$home_goals',
					LeagueMatchAwayGoals = '$away_goals',
                    LeagueMatchCreated = ".time()."
					WHERE LeagueMatchID = '$mid'
					LIMIT 1
					");
            }
            else {
                $xoopsDB->query("UPDATE ".$xoopsDB->prefix("tplls_leaguematches")." SET
					LeagueMatchDate = '$dateandtime',
					LeagueMatchHomeID = '$home',
					LeagueMatchAwayID = '$away',
					LeagueMatchHomeWinnerID = '-1',
					LeagueMatchHomeLoserID = '-1',
					LeagueMatchAwayWinnerID = '-1',
					LeagueMatchAwayLoserID = '-1',
					LeagueMatchHomeTieID = '-1',
					LeagueMatchAwayTieID = '-1',
					LeagueMatchHomeGoals = NULL,
					LeagueMatchAwayGoals = NULL,
                    LeagueMatchCreated = ".time()."
					WHERE LeagueMatchID = '$mid'
					LIMIT 1
					");
            }                
        }        
    }
}
elseif($delete_submit)
{
    $mid = intval($_POST['mid']);
    $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("tplls_leaguematches")." WHERE LeagueMatchID = '$mid' LIMIT 1");
}

	?>

        <?php
	include('head.php');
	?>

	<table align="center" width="700">
		<tr>
		<td align="left" valign="top">
		<form method="post" action="<?php echo "$PHP_SELF"?>">
		<?php
		if(!isset($action))
		{
		?>
		<h3><?php echo _AM_ADDMATCH;?></h3>
		<?php echo _AM_ADDMATCHNOTE;?><br><br>

		<?php echo _AM_DATE;?>
		<select name="day">
		<?php
		//print the days
		for($i = 1 ; $i < 32 ; $i++)
		{
		    if($i<10)
		    {
		        $i = "0".$i;
		    }
		    if($i == "01")
		    echo "<option value=\"$i\" SELECTED>$i</option>\n";
		    else
		    echo "<option value=\"$i\">$i</option>\n";
		}
		?>
		</select>&nbsp;/&nbsp;

		<select name="month">
		<?php
		//print the months
		for($i = 1 ; $i < 13 ; $i++)
		{
		    if($i<10)
		    {
		        $i = "0".$i;
		    }
		    if($i == "01")
		    echo "<option value=\"$i\" SELECTED>$i</option>\n";
		    else
		    echo "<option value=\"$i\">$i</option>\n";
		}
		?>
		</select>&nbsp;/&nbsp;

		<select name="year">
		<?php
		//print the years
		for($i = 1950 ; $i < 2010 ; $i++)
		{
		    if($i<10)
		    {
		        $i = "0".$i;
		    }
		    if($i == "2003")
		    echo "<option value=\"$i\" SELECTED>$i</option>\n";
		    else
		    echo "<option value=\"$i\">$i</option>\n";
		}
		?>
		</select><br><br>
		<?php echo _AM_ADDMATCHNOTE2;?><br><br>

		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>

		<td align="left" valign="middle"><b><?php echo _AM_HOMETEAM;?></b></td>
		<td align="left" valign="middle"><b><?php echo _AM_AWAYTEAM;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_GOALSHOME;?></b></td>
		<td align="center" valign="middle"><b><b><?php echo _AM_GOALSAWAY;?></b></td>

		</tr>

		<?php
		
		//
		//query to get all the teams
		//
		$get_opponents = $xoopsDB->query("SELECT OpponentID AS id,
		OpponentName AS name
		FROM ".$xoopsDB->prefix("tplls_opponents")."
		ORDER BY OpponentName");
		
		//
		//Prints 15 forms
		//
		$i=0;
		
		while($i < 15)
		{
		    //
		    //query back to row 0 if not the first time in the loop
		    //
		    if($i>0)
		    mysql_data_seek($get_opponents, 0);
		    
		    echo'
			<tr>
			<td align="left" valign="middle">
			';
		    
		    echo"<select name=\"home[$i]\">";
		    
		    while($data = $xoopsDB->fetchArray($get_opponents))
		    {
		        echo"<option value=\"$data[id]\">$data[name]</option>\n";
		    }
		    
		    echo'
			</select>
			</td>
			<td align="left" valign="middle">
			';
		    
		    //
		    //Back to line 0in the query
		    //
		    mysql_data_seek($get_opponents, 0);
		    
		    echo"<select name=\"away[$i]\">";
		    
		    while($data = $xoopsDB->fetchArray($get_opponents))
		    {
		        echo"<option value=\"$data[id]\">$data[name]</option>\n";
		    }
		    
		    echo"
			</select>
			</td>
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"home_goals[$i]\" size=\"2\"></td>
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"away_goals[$i]\" size=\"2\"></td>

			</tr>
			";
		    
		    
		    
		    
		    $i++;
		}
		
		?>

		</table><br><br>
        <input type="hidden" name="seasonid" value="<?php echo $seasonid; ?>">
        <input type="hidden" name="seasonname" value="<?php echo $seasonname; ?>">
		<input type="submit" name="add_submit" value="<?php echo _AM_ADDMATCHES;?>">
		</form>
		<?php
		}
		elseif($action == 'modifyall')
		{
		    $date = $_REQUEST['date'];
		    
		$get_matches = $xoopsDB->query("SELECT DAYOFMONTH(LM.LeagueMatchDate) AS dayofmonth,
		MONTH(LM.LeagueMatchDate) AS month,
		YEAR(LM.LeagueMatchDate) AS year,
		LM.LeagueMatchHomeID AS homeid,
		LM.LeagueMatchAwayID AS awayid,
		LM.LeagueMatchHomeGoals AS homegoals,
		LM.LeagueMatchAwayGoals AS awaygoals
		FROM ".$xoopsDB->prefix("tplls_leaguematches")." LM
		WHERE LM.LeaguematchDate = '$date'
		")
		    ;
		    
		    //
		    //query to get date
		    //
		$get_match = $xoopsDB->query("SELECT DAYOFMONTH(LM.LeagueMatchDate) AS dayofmonth,
		MONTH(LM.LeagueMatchDate) AS month,
		YEAR(LM.LeagueMatchDate) AS year
		FROM ".$xoopsDB->prefix("tplls_leaguematches")." LM
		WHERE LM.LeaguematchDate = '$date'
		LIMIT 1
		")
		    ;
		    
		    $datedata = $xoopsDB->fetchArray($get_match);
		    
		    //$xoopsDB->freeRecordSet($get_match);
		    
		$get_opponents = $xoopsDB->query("SELECT OpponentID AS id,
		OpponentName AS name
		FROM ".$xoopsDB->prefix("tplls_opponents")."
		ORDER BY OpponentName
		")
		    ;
		    
		?>

		<form method="post" action="<?php echo "$PHP_SELF" ?>">
		<h3><?php echo _AM_MODMATCHES;?></h3>

		<table width="100%" cellspacing="3" cellpadding="3" border="0">

			<tr>
				<td align="left" valign="top">
				<?php echo _AM_DATETIME;?>
				</td>
				<td align="left" valign="top">

				<select name="day">
				<?php
				//Print the days
				for($i = 1 ; $i < 32 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$i;
				    }
				    if($datedata['dayofmonth'] == $i)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
				</select>&nbsp;/&nbsp;

				<select name="month">
				<?php
				//Print the months
				for($i = 1 ; $i < 13 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$i;
				    }
				    if($datedata['month'] == $i)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
				</select>&nbsp;/&nbsp;

				<select name="year">
				<?php
				//Print the years
				for($i = 1950 ; $i < 2010 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$i;
				    }
				    if($datedata['year'] == $i)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
			</select>
			</td>
		</tr>

		</table>

		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>

		<td align="left" valign="middle"><b><?php echo _AM_HOMETEAM;?></b></td>
		<td align="left" valign="middle"><b><?php echo _AM_AWAYTEAM;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_GOALSHOME;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_GOALSAWAY;?></b></td>

		</tr>

		<?php
		
		//
		//Lets get all the matches from selected date to the form
		//
		$i = 0;
		while($matchdata = $xoopsDB->fetchArray($get_matches))
		{
		    //
		    //Back to line 0 in the query if not the first loop
		    //
		    if($i>0)
		    mysql_data_seek($get_opponents, 0);
		    
		    echo'
			<tr>
			<td align="left" valign="middle">
			';
		    
		    echo"<select name=\"home[$i]\">";
		    
		    while($data = $xoopsDB->fetchArray($get_opponents))
		    {
		        if($matchdata['homeid'] == $data['id'])
		        echo"<option value=\"$data[id]\" SELECTED>$data[name]</option>\n";
		    }
		    
		    echo'
			</select>
			</td>
			<td align="left" valign="middle">
			';
		    
		    //
		    //Back to line 0 in the query
		    //
		    mysql_data_seek($get_opponents, 0);
		    
		    echo"<select name=\"away[$i]\">";
		    
		    while($data = $xoopsDB->fetchArray($get_opponents))
		    {
		        if($matchdata['awayid'] == $data['id'])
		        echo"<option value=\"$data[id]\" SELECTED>$data[name]</option>\n";
		    }
		    
		    echo"
			</select>
			</td>
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"home_goals[$i]\" size=\"2\" value=\"$matchdata[homegoals]\"></td>
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"away_goals[$i]\" size=\"2\" value=\"$matchdata[awaygoals]\"></td>

			</tr>
			";
		    
		    
		    
		    
		    $i++;
		}
	
		?>

		</table>

		<font color="red"><?php echo _AM_MODNOTICE1;?></font><br><br>
		<input type="hidden" name="qty" value="<?= $i ?>">
		<input type="hidden" name="seasonname" value="<?php echo $seasonname; ?>">
		<br><input type="submit" name="modifyall_submit" value="<?php echo _AM_MODINPUT;?>">
		</form>

		<?php
		}
		elseif($action == 'modify')
		{
		    $id = intval($_REQUEST['id']);
		    
		$get_match = $xoopsDB->query("SELECT DAYOFMONTH(LM.LeagueMatchDate) AS dayofmonth,
		MONTH(LM.LeagueMatchDate) AS month,
		YEAR(LM.LeagueMatchDate) AS year,
		LM.LeagueMatchHomeID AS homeid,
		LM.LeagueMatchAwayID AS awayid,
		LM.LeagueMatchHomeGoals AS homegoals,
		LM.LeagueMatchAwayGoals AS awaygoals
		FROM ".$xoopsDB->prefix("tplls_leaguematches")." LM
		WHERE LM.LeaguematchID = '$id'
		LIMIT 1
		")
		    ;
		    
		    $get_opponents = $xoopsDB->query("SELECT OpponentID AS id,
		OpponentName AS name
		FROM ".$xoopsDB->prefix("tplls_opponents")."
		ORDER BY OpponentName
		")
		    ;
		    
		    $matchdata = $xoopsDB->fetchArray($get_match);
		    
		    //$xoopsDB->freeRecordSet($get_match);
		    
		?>
		<form method="post" action="<?php echo "$PHP_SELF"?>">
		<h3><?php echo _AM_MODMATCH;?></h3>

		<table width="100%" cellspacing="3" cellpadding="3" border="0">

			<tr>
				<td align="left" valign="top">
				<?php echo _AM_DATETIME;?>
				</td>
				<td align="left" valign="top">

				<select name="day">
				<?php
				//Print the days
				for($i = 1 ; $i < 32 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$i;
				    }
				    if($matchdata['dayofmonth'] == $i)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
				</select>&nbsp;/&nbsp;

				<select name="month">
				<?php
				//Print the months
				for($i = 1 ; $i < 13 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$i;
				    }
				    if($matchdata['month'] == $i)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
				</select>&nbsp;/&nbsp;

				<select name="year">
				<?php
				//Print the years
//TODO: make this variable depending on actual year
				for($i = 1950 ; $i < 2015 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$i;
				    }
				    if($matchdata['year'] == $i)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
			</select>
			</td>
		</tr>

		</table>

		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>

		<td align="left" valign="middle"><b><?php echo _AM_HOMETEAM;?></b></td>
		<td align="left" valign="middle"><b><?php echo _AM_AWAYTEAM;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_GOALSHOME;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_GOALSAWAY;?></b></td>

		</tr>

		<tr>
		<td align="left" valign="middle">

		<select name="home">
		<?php
		
		while($data = $xoopsDB->fetchArray($get_opponents))
		{
		    if($matchdata['homeid'] == $data['id'])
		    echo"<option value=\"$data[id]\" SELECTED>$data[name]</option>\n";
		    else
		    echo"<option value=\"$data[id]\">$data[name]</option>\n";
		}
		
		?>
		</select>
		</td>
		<td align="left" valign="middle">

		<select name="away">
		<?php
		
		mysql_data_seek($get_opponents, 0);
		
		while($data = $xoopsDB->fetchArray($get_opponents))
		{
		    if($matchdata['awayid'] == $data['id'])
		    echo"<option value=\"$data[id]\" SELECTED>$data[name]</option>\n";
		    else
		    echo"<option value=\"$data[id]\">$data[name]</option>\n";
		}
		
		?>
		</select>
		</td>
		<td align="center" valign="middle"><input type="text" name="home_goals" size="2" value="<?= $matchdata['homegoals'] ?>"></td>
		<td align="center" valign="middle"><input type="text" name="away_goals" size="2" value="<?= $matchdata['awaygoals'] ?>"></td>

		</tr>

		</table>


		<input type="hidden" name="mid" value="<?= $id ?>">
		<input type="hidden" name="homeid" value="<?= $matchdata['awayid'] ?>">
		<input type="hidden" name="awayid" value="<?= $matchdata['homeid'] ?>">
		<br><input type="submit" name="modify_submit" value="<?php echo _AM_MODINPUT2;?>">
		<input type="hidden" name="seasonid" value="<?php echo $seasonid; ?>">
		<input type="hidden" name="seasonname" value="<?php echo $seasonname; ?>">
		<br><br><br><br><br>
		<input type="submit" name="delete_submit" value="<?php echo _AM_DELINPUT;?>">
		</form>

		<?php
		}
		?>
		</td>

		<td align="left" valign="top" width="250">

		<table width="250">
		<?php
		$get_matches = $xoopsDB->query("SELECT O.OpponentName AS hometeam,
		OP.OpponentName AS awayteam,
		LM.LeagueMatchHomeGoals AS goals_home,
		LM.LeagueMatchAwayGoals AS goals_away,
		LM.LeagueMatchID AS id,
		LM.LeagueMatchDate AS defaultdate,
		DATE_FORMAT(LM.LeagueMatchDate, '%b %D %Y') AS date
		FROM ".$xoopsDB->prefix("tplls_leaguematches")." LM, ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_opponents")." OP
		WHERE O.OpponentID = LM.LeagueMatchHomeID AND
		OP.OpponentID = LM.LeagueMatchAwayID AND
		LeagueMatchSeasonID = '$seasonid'
		ORDER BY LM.LeagueMatchDate DESC");
		
		if($xoopsDB->getRowsNum($get_matches) < 1)
		{
		    echo "<b>  "._AM_NOMATCHESYET." <br><u> $seasonname</u> </b>";
		}
		else
		{
		    echo "<b> "._AM_MATCHESYET." <br> <u>$seasonname</u></b><br><br>";
		    
		    $i = 0;
		    $temp = '';
		    
		    while($data = $xoopsDB->fetchArray($get_matches))
		    {
		        if($i == 0)
		        {
		            echo"
					<tr>
					<td align=\"left\" colspan=\"2\">
					<b><a href=\"$PHP_SELF?action=modifyall&amp;date=$data[defaultdate]\">$data[date]</a></b>
					</td>
					</tr>
					";
		        }
		        
		        if($data['date'] != "$temp" && $i > 0)
		        {
		            echo"
					<tr>
					<td align=\"left\" colspan=\"2\">
					<br><br>
					<b><a href=\"$PHP_SELF?action=modifyall&amp;date=$data[defaultdate]\">$data[date]</a></b>
					</td>
					</tr>
					";
		        }
		        
		        echo "
				<tr>
				<td align=\"left\" valign=\"top\" width=\"230\">
				<a href=\"$PHP_SELF?action=modify&amp;id=$data[id]\">$data[hometeam] - $data[awayteam]</a>
				</td>
				<td align=\"right\" valign=\"top\" width=\"50\">";
		        
		        if(!is_null($data['goals_home']))
		        echo"$data[goals_home]-$data[goals_away]";
		        else
		        echo'&nbsp;';
		        
		        
		        echo"
				</td>
				</tr>";
		        
		        $temp = "$data[date]";
		        
		        $i++;
		    }
		}
		?>
		</table>
		</td>
		</tr>
	</table>
<?php 
xoops_cp_footer();
?>