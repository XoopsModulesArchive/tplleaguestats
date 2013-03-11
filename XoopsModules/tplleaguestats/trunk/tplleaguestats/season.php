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

include ('../../mainfile.php');
include (XOOPS_ROOT_PATH.'/header.php');

//
//Preferences
//
$sql = "SELECT SeasonID FROM ".$xoopsDB->prefix("tplls_seasonnames")." WHERE SeasonDefault=1";
$season = $xoopsDB->query($sql);
$season = $xoopsDB->fetchArray($season);
$d_season_id = $season['SeasonID'];
$show_all_or_one = $xoopsModuleConfig['defaultshow'];
$show_table = $xoopsModuleConfig['defaulttable'];
$for_win = $xoopsModuleConfig['forwin'];
$for_draw = $xoopsModuleConfig['fordraw'];
$for_lose = $xoopsModuleConfig['forloss'];
$print_date = $xoopsModuleConfig['printdate'];
$top_bg = $xoopsModuleConfig['topoftable'];
$bg1 = $xoopsModuleConfig['bg1'];
$bg2 = $xoopsModuleConfig['bg2'];
$inside_c = $xoopsModuleConfig['inside'];
$border_c = $xoopsModuleConfig['bordercolour'];
$tb_width = $xoopsModuleConfig['tablewidth'];

//
//Check if there are session variables registered
//
//if(!session_is_registered('defaultseasonid'))
    if ( !isset( $_SESSION['defaultseasonid'] ) )
{
	$_SESSION['defaultseasonid'] = $d_seasonid;
}
$defaultseasonid = intval($_SESSION['defaultseasonid']);

//
//If All is chosen from season, lets set default value for %
//
if($defaultseasonid == 0)
	$defaultseasonid = '%';

//
//Gets seasons and match types for dropdowns
//
$get_seasons = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("tplls_seasonnames")." WHERE SeasonPublish = '1' ORDER BY SeasonName")
;


?>

<!-- All the data print begin -->
<form method="post" action="change.php">

<table align="center" width="<?php echo $tb_width ?>" cellspacing="0" cellpadding="0" border="0" bgcolor="<?= $border_c ?>">
<tr>
<td>
<table width="100%" cellspacing="1" cellpadding="5" border="0">
<tr>
<td bgcolor="<?= $inside_c ?>" align="center">
<?php


?>

<?php echo _LS_MOVETO;?><select name="moveto">
<option value="index.php"><?php echo _LS_TABLES;?></option>
<option value="headtohead.php"><?php echo _LS_HEADTOHEAD;?></option>
</select> <input type="submit" class="button"  value=">>" name="submit6">

</td>
</tr>
</table>
</td>
</tr>
</table>


<table align="center" width="<?php echo $tb_width ?>" cellspacing="0" cellpadding="0" border="0" bgcolor="<?php echo $border_c ?>">
<tr>
<td>
	<table width="100%" cellspacing="1" cellpadding="5" border="0">
	<tr>
	<td bgcolor="<?php echo $inside_c ?>" align="center">

	<table width="100%" cellspacing="1" cellpadding="5" border="0" align="center">

	<tr>

	<td bgcolor="<?= $bg1 ?>" align="left" valign="middle" colspan="2" style="padding-left:2px;">
	<h3><?php echo _LS_SEASONSTATS;?></h3>
	<?php echo _LS_SEASONFILTER;?> <select name="season">
	<option value="0"><?php echo _LS_ALLSEASONS;?></option>
	<?php
	while($data = $xoopsDB->fetchArray($get_seasons))
	{
		if($data['SeasonID'] == $defaultseasonid)
		{
			echo "<option value=\"$data[SeasonID]\" SELECTED>$data[SeasonName]</option>\n";
			$draw_line = explode(",", $data['SeasonLine']);
		}
		else
			echo "<option value=\"$data[SeasonID]\">$data[SeasonName]</option>\n";
	}
	//$xoopsDB->freeRecordSet($get_seasons);

	?>
	</select> <input type="submit" class="button" value=">>" name="submit">
	</td>

	</tr>

	<?php

	//
	//query to get data from the matches
	//
	$query = $xoopsDB->query("SELECT
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM
	".$xoopsDB->prefix("tplls_leaguematches")." LM
	WHERE
	LM.LeagueMatchSeasonID LIKE '$defaultseasonid' AND
	LM.LeagueMatchHomeGoals IS NOT NULL AND
	LM.LeagueMatchAwayGoals IS NOT NULL
	")
	;

	//
	//Sets counter variables into zero
	//
	$home_wins = 0;

	$away_wins = 0;

	$draws = 0;

	$home_goals = 0;
	$away_goals = 0;

	$played = 0;
	$goals = 0;


	//
	//data check
	//
	while($data = $xoopsDB->fetchArray($query))
	{
		//
		//Home win
		//
		if($data['homegoals'] > $data['awaygoals'])
		{
			$home_wins++;
			$home_goals = $home_goals + $data['homegoals'];
			$away_goals = $away_goals + $data['awaygoals'];
		}
		//
		//Draw
		//
		elseif($data['homegoals'] == $data['awaygoals'])
		{
			$draws++;
			$home_goals = $home_goals + $data['homegoals'];
			$away_goals = $away_goals + $data['awaygoals'];
		}
		//
		//Away win
		//
		elseif($data['homegoals'] < $data['awaygoals'])
		{
			$away_wins++;
			$home_goals = $home_goals + $data['homegoals'];
			$away_goals = $away_goals + $data['awaygoals'];
		}
	}

	$played = $home_wins + $draws + $away_wins;

	$goals = $home_goals + $away_goals;

	//
	//Avoid divide by zero
	//
	if($xoopsDB->getRowsNum($query) < 1)
	{
		$home_win_percent = 0;
		$away_win_percent = 0;
		$draw_percent = 0;

		$home_goal_average = 0;
		$away_goal_average = 0;
		$goal_average = 0;

		$home_win_percent_ = number_format($home_win_percent, 2, '.', '');
		$away_win_percent_ = number_format($away_win_percent, 2, '.', '');
		$draw_percent_ = number_format($draw_percent, 2, '.', '');
		$home_goal_average_ = number_format($home_goal_average, 2, '.', '');
		$away_goal_average_ = number_format($away_goal_average, 2, '.', '');
		$goal_average_ = number_format($goal_average, 2, '.', '');
	}
	else
	{
		//
		//Calculates percents and averages
		//
		$home_win_percent = round(100*($home_wins/$played),2);
		$away_win_percent = round(100*($away_wins/$played),2);
		$draw_percent = round(100*($draws/$played),2);

		$home_goal_average = round(($home_goals/$played),2);
		$away_goal_average = round(($away_goals/$played),2);
		$goal_average = round(($goals/$played),2);

		$home_win_percent_ = number_format($home_win_percent, 2, '.', '');
		$away_win_percent_ = number_format($away_win_percent, 2, '.', '');
		$draw_percent_ = number_format($draw_percent, 2, '.', '');
		$home_goal_average_ = number_format($home_goal_average, 2, '.', '');
		$away_goal_average_ = number_format($away_goal_average, 2, '.', '');
		$goal_average_ = number_format($goal_average, 2, '.', '');
	}

    //$xoopsDB->freeRecordSet($query);
    
	?>

	<tr>

	<td align="left" valign="middle" width="40%" style="padding-left:5px;">
	<b><?php echo _LS_MATCHESPLAYED;?></b>
	</td>

	<td align="left" valign="middle" width="60%">
	<?= $played ?>
	</td>

	</tr>

	<tr>

		<td align="left" valign="middle" width="40%" style="padding-left:5px;">
		<b><?php echo _LS_HOMEWINS;?></b>
		</td>

		<td align="left" valign="middle" width="60%">
		<?= "$home_wins ($home_win_percent_ %)" ?>
		</td>

	</tr>

	<tr>

		<td align="left" valign="middle" width="40%" style="padding-left:5px;">
		<b><?php echo _LS_AWAYWINS;?></b>
		</td>

		<td align="left" valign="middle" width="60%">
		<?= "$away_wins ($away_win_percent_ %)" ?>
		</td>

	</tr>

	<tr>

		<td align="left" valign="middle" width="40%" style="padding-left:5px;">
		<b><?php echo _LS_DRAWS;?></b>
		</td>

		<td align="left" valign="middle" width="60%">
		<?= "$draws ($draw_percent_ %)" ?>
		</td>

	</tr>

	<tr>

		<td align="left" valign="middle" width="40%" style="padding-left:5px;">
		<b><?php echo _LS_TOTGOALS;?></b>
		</td>

		<td align="left" valign="middle" width="60%">
		<?= "$goals ("._LS_AVERAGE." $goal_average "._LS_PERMATCH.")" ?>
		</td>

	</tr>

	<tr>

		<td align="left" valign="middle" width="40%" style="padding-left:5px;">
		<b><?php echo _LS_HOMETEAMGOALS;?></b>
		</td>

		<td align="left" valign="middle" width="60%">
		<?= "$home_goals ("._LS_AVERAGE." $home_goal_average "._LS_PERMATCH.")" ?>
		</td>

	</tr>

	<tr>

		<td align="left" valign="middle" width="40%" style="padding-left:5px;">
		<b><?php echo _LS_AWAYTEAMGOALS;?></b>
		</td>

		<td align="left" valign="middle" width="60%">
		<?= "$away_goals ("._LS_AVERAGE." $away_goal_average "._LS_PERMATCH.")" ?>
		</td>

	</tr>

	</table>

	<table width="100%" cellspacing="1" cellpadding="5" border="0" align="center">

	<tr>

	<td bgcolor="<?= $top_bg ?>" align="center" valign="middle" colspan="3">
	<b><?php echo _LS_BIGHOMEWIN;?></b>
	</td>

	</tr>


	<?php

	//
	//How to print date?
	//
	if($print_date == 1)
	{
		$print_date = '%d.%m.%Y';
	}
	elseif($print_date == 2)
	{
		$print_date = '%m.%d.%Y';
	}
	elseif($print_date == 3)
	{
		$print_date = '%b %D %Y';
	}

	//
	//Max home win
	//
	$maxhomewin = $xoopsDB->query("SELECT
	MAX(LeagueMatchHomeGoals - LeagueMatchAwayGoals) AS ero
	FROM ".$xoopsDB->prefix("tplls_leaguematches")."
	WHERE
	LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	$temp_data = $xoopsDB->fetchArray($maxhomewin);
	$temp_number = $temp_data['ero'];

	//$xoopsDB->freeRecordSet($maxhomewin);

	//
	//query to get all final scores with maximum value from previous query
	//
	$maxhomewin = $xoopsDB->query("SELECT
	O.OpponentName AS hometeam,
	OP.OpponentName AS awayteam,
	DATE_FORMAT(LM.LeagueMatchDate, '$print_date') AS date,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM
	".$xoopsDB->prefix("tplls_leaguematches")." AS LM,
	".$xoopsDB->prefix("tplls_opponents")." O,
	".$xoopsDB->prefix("tplls_opponents")." OP
	WHERE
	O.OpponentID = LM.LeagueMatchHomeID AND
	OP.OpponentID = LM.LeagueMatchAwayID AND
	(LM.LeagueMatchHomeGoals - LM.LeagueMatchAwayGoals) = '$temp_number' AND
	LeagueMatchSeasonID LIKE '$defaultseasonid'
	ORDER BY LM.LeagueMatchDate
	")
	;

	//
	//Print max home wins
	//
	$i = 0;
	while($data = $xoopsDB->fetchArray($maxhomewin))
	{
		if($i = 0)
			$temp_color = $bg1;
		else
			$temp_color = $bg2;

		echo"
		<tr bgcolor=\"$temp_color\">
		<td align=\"right\" valign=\"middle\" width=\"30%\">
		$data[date]
		</td>

		<td align=\"center\" valign=\"middle\" width=\"40%\">
		$data[hometeam] - $data[awayteam]
		</td>

		<td align=\"left\" valign=\"middle\" width=\"30%\">
		$data[homegoals] - $data[awaygoals]
		</td>
		</tr>
		";

		$i++;
	}

	//$xoopsDB->freeRecordSet($maxhomewin);

	?>


	<tr>
	<td colspan="4">
	<br>
	</td>
	</tr>

	<tr>

	<td bgcolor="<?= $top_bg ?>" align="center" valign="middle" colspan="3">
	<b><?php echo _LS_BIGAWAYWIN;?></b>
	</td>

	</tr>


	<?php

	//
	//Max away win
	//
	$maxawaywin = $xoopsDB->query("SELECT
	MIN(LeagueMatchHomeGoals - LeagueMatchAwayGoals) AS ero
	FROM ".$xoopsDB->prefix("tplls_leaguematches")."
	WHERE
	LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	$temp_data = $xoopsDB->fetchArray($maxawaywin);
	$temp_number = $temp_data['ero'];

	//$xoopsDB->freeRecordSet($maxawaywin);

	//
	//query to get all max away wins
	//
	$maxawaywin = $xoopsDB->query("SELECT
	O.OpponentName AS hometeam,
	OP.OpponentName AS awayteam,
	DATE_FORMAT(LM.LeagueMatchDate, '$print_date') AS date,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM
	".$xoopsDB->prefix("tplls_leaguematches")." AS LM,
	".$xoopsDB->prefix("tplls_opponents")." O,
	".$xoopsDB->prefix("tplls_opponents")." OP
	WHERE
	O.OpponentID = LM.LeagueMatchHomeID AND
	OP.OpponentID = LM.LeagueMatchAwayID AND
	(LM.LeagueMatchHomeGoals - LM.LeagueMatchAwayGoals) = '$temp_number' AND
	LeagueMatchSeasonID LIKE '$defaultseasonid'
	ORDER BY LM.LeagueMatchDate
	")
	;

	//
	//Prints max away wins
	//
	$i = 0;
	while($data = $xoopsDB->fetchArray($maxawaywin))
	{
		if($i = 0)
			$temp_color = $bg1;
		else
			$temp_color = $bg2;

		echo"
		<tr bgcolor=\"$temp_color\">
		<td align=\"right\" valign=\"middle\" width=\"30%\">
		$data[date]
		</td>

		<td align=\"center\" valign=\"middle\" width=\"40%\">
		$data[hometeam] - $data[awayteam]
		</td>

		<td align=\"left\" valign=\"middle\" width=\"30%\">
		$data[homegoals] - $data[awaygoals]
		</td>
		</tr>
		";

		$i++;
	}

	//$xoopsDB->freeRecordSet($maxawaywin);

	?>


	<tr>
	<td colspan="4">
	<br>
	</td>
	</tr>

	<tr>

	<td bgcolor="<?= $top_bg ?>" align="center" valign="middle" colspan="3">
	<b><?php echo _LS_HIGHAGGSCORE;?></b>
	</td>

	</tr>


	<?php
	//
	//Most goals scored in one match
	//
	$maxgoals = $xoopsDB->query("SELECT
	MAX(LeagueMatchHomeGoals + LeagueMatchAwayGoals) AS summa
	FROM ".$xoopsDB->prefix("tplls_leaguematches")."
	WHERE
	LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	$temp_data = $xoopsDB->fetchArray($maxgoals);
	$temp_number = $temp_data['summa'];

	//$xoopsDB->freeRecordSet($maxgoals);

	//
	//query t get max values
	//
	$maxgoals = $xoopsDB->query("SELECT
	O.OpponentName AS hometeam,
	OP.OpponentName AS awayteam,
	DATE_FORMAT(LM.LeagueMatchDate, '$print_date') AS date,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM
	".$xoopsDB->prefix("tplls_leaguematches")." AS LM,
	".$xoopsDB->prefix("tplls_opponents")." O,
	".$xoopsDB->prefix("tplls_opponents")." OP
	WHERE
	O.OpponentID = LM.LeagueMatchHomeID AND
	OP.OpponentID = LM.LeagueMatchAwayID AND
	(LM.LeagueMatchHomeGoals + LM.LeagueMatchAwayGoals) = '$temp_number' AND
	LeagueMatchSeasonID LIKE '$defaultseasonid'
	ORDER BY LM.LeagueMatchDate
	")
	;

	//
	//Print max aggregate scores
	//
	$i = 0;
	while($data = $xoopsDB->fetchArray($maxgoals))
	{
		if($i = 0)
			$temp_color = $bg1;
		else
			$temp_color = $bg2;

		echo"
		<tr bgcolor=\"$temp_color\">
		<td align=\"right\" valign=\"middle\" width=\"30%\">
		$data[date]
		</td>

		<td align=\"center\" valign=\"middle\" width=\"40%\">
		$data[hometeam] - $data[awayteam]
		</td>

		<td align=\"left\" valign=\"middle\" width=\"30%\">
		$data[homegoals] - $data[awaygoals]
		</td>
		</tr>
		";

		$i++;
	}

	//$xoopsDB->freeRecordSet($maxgoals);

	?>


	</table>


	</td>
	</tr>
	</table><br>
</td>
</tr>
</table>


<?php
include('bottom.txt');
?>
</form>

<?php
include(XOOPS_ROOT_PATH.'/footer.php');
?>
