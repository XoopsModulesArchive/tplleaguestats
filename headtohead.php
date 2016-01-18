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
if (!isset($_SESSION['defaulthomeid'])) {
    $sql = "SELECT OpponentID FROM ".$xoopsDB->prefix("tplls_opponents")." LIMIT 0,2";
    $teamresults = $xoopsDB->query($sql);
    $teams = $xoopsDB->fetchArray($teamresults);
    $_SESSION['defaulthomeid'] = $teams['OpponentID'];
    $teams = $xoopsDB->fetchArray($teamresults);
    $_SESSION['defaultawayid'] = $teams['OpponentID'];
}
$defaulthomeid = intval($_SESSION['defaulthomeid']);
$defaultawayid = intval($_SESSION['defaultawayid']);

//if(!session_is_registered || !session_is_registered('defaultseasonid'))
if ( !isset( $_SESSION ) || !isset( $_SESSION['defaultseasonid'] ) )

{
	$_SESSION['defaultseasonid'] = $d_season_id;
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

//
//query to get teams from choosed season
//
$get_teams = $xoopsDB->query("SELECT DISTINCT
O.OpponentName AS name,
O.OpponentID AS id
FROM ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_leaguematches")." LM
WHERE LM.LeagueMatchSeasonID LIKE '$defaultseasonid' AND
(O.OpponentID = LM.LeagueMatchHomeID OR
O.OpponentID = LM.LeagueMatchAwayID)
ORDER BY name
")
;

?>

<?php

//
//Width of the line
//
$templine_width = $tb_width-25;


//
//query to get team names
//
$get_names = $xoopsDB->query("SELECT O.OpponentName AS homename,
OP.OpponentName AS awayname
FROM ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_opponents")." OP
WHERE
O.OpponentID = ".intval($defaulthomeid)." AND
OP.OpponentID = ".intval($defaultawayid)."
LIMIT 1
")
;

$namedata = $xoopsDB->fetchArray($get_names);

//$xoopsDB->freeRecordSet($get_names);

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

<?php echo _LS_CHANGESEASON;?>
<select name="season">
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
</select>
<input type="submit" class="button" value=">>" name="submit">
&nbsp;&nbsp;&nbsp;
<?php echo _LS_MOVETO;?> <select name="moveto">
<option value="index.php"><?php echo _LS_TABLES;?></option>
<option value="season.php"><?php echo _LS_SEASONSTATS;?></option>
</select> <input type="submit" class="button" value=">>" name="submit6">
<br>
<?php echo _LS_HOMETEAM;?>
<select name="home_id">
<?php
while($data = $xoopsDB->fetchArray($get_teams))
{
	if($data['id'] == $defaulthomeid)
		echo"<option value=\"$data[id]\" SELECTED>$data[name]</option>\n";
	else
		echo"<option value=\"$data[id]\">$data[name]</option>\n";
}
?>
</select> <input type="submit" class="button" value=">>" name="submit4">
&nbsp;&nbsp;&nbsp;
<?php echo _LS_AWAYTEAM;?>
<select name="away_id">
<?php

if ( mysql_num_rows($get_teams) >=1 )  {
mysql_data_seek($get_teams, 0);

//mysql_data_seek($get_teams, 0);
while($data = $xoopsDB->fetchArray($get_teams))
{
	if($data['id'] == $defaultawayid)
		echo"<option value=\"$data[id]\" SELECTED>$data[name]</option>\n";
	else
		echo"<option value=\"$data[id]\">$data[name]</option>\n";
}
}
//$xoopsDB->freeRecordSet($get_teams);
?>
</select> <input type="submit" class="button" value=">>" name="submit5">

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
	//query to get hometeam data
	//
	$query = $xoopsDB->query("SELECT
	LM.LeagueMatchHomeID AS homeid,
	LM.LeagueMatchAwayID AS awayid,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM
	".$xoopsDB->prefix("tplls_leaguematches")." LM
	WHERE
	(LM.LeagueMatchHomeID = ".intval($defaulthomeid)." OR
	LM.LeagueMatchAwayID = ".intval($defaulthomeid).") AND
	LM.LeagueMatchSeasonID LIKE '$defaultseasonid' AND
	LM.LeagueMatchHomeGoals IS NOT NULL AND
	LM.LeagueMatchAwayGoals IS NOT NULL
	")
	;

	//
	//query to get away team data
	//
	$query2 = $xoopsDB->query("SELECT
	LM.LeagueMatchHomeID AS homeid,
	LM.LeagueMatchAwayID AS awayid,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM
	".$xoopsDB->prefix("tplls_leaguematches")." LM
	WHERE
	(LM.LeagueMatchHomeID = ".intval($defaultawayid)." OR
	LM.LeagueMatchAwayID = ".intval($defaultawayid).") AND
	LM.LeagueMatchSeasonID LIKE '$defaultseasonid' AND
	LM.LeagueMatchHomeGoals IS NOT NULL AND
	LM.LeagueMatchAwayGoals IS NOT NULL
	")
	;

	//
	//Table variables: hometeam values into index 0 and awayteam into index 1
	//
	$home_wins[0] = 0;
	$home_draws[0] = 0;
	$home_loses[0] = 0;
	$home_goals[0] = 0;
	$home_goalsagainst[0] = 0;
	$away_wins[0] = 0;
	$away_draws[0] = 0;
	$away_loses[0] = 0;
	$away_goals[0] = 0;
	$away_goalsagainst[0] = 0;
	$total_wins[0] = 0;
	$total_draws[0] = 0;
	$total_loses[0] = 0;
	$total_goals[0] = 0;
	$total_goalsagainst[0] = 0;

	$home_wins[1] = 0;
	$home_draws[1] = 0;
	$home_loses[1] = 0;
	$home_goals[1] = 0;
	$home_goalsagainst[1] = 0;
	$away_wins[1] = 0;
	$away_draws[1] = 0;
	$away_loses[1] = 0;
	$away_goals[1] = 0;
	$away_goalsagainst[1] = 0;
	$total_wins[1] = 0;
	$total_draws[1] = 0;
	$total_loses[1] = 0;
	$total_goals[1] = 0;
	$total_goalsagainst[1] = 0;


	//
	//Lets check hometeam
	//
	while($data = $xoopsDB->fetchArray($query))
	{
		//
		//Home or away game?
		//
		//
		//Home match
		//
		if($data['homeid'] == $defaulthomeid)
		{
			//
			//Win
			//
			if($data['homegoals'] > $data['awaygoals'])
			{
				$home_wins[0]++;
				$home_goals[0] = $home_goals[0] + $data['homegoals'];
				$home_goalsagainst[0] = $home_goalsagainst[0] + $data['awaygoals'];
			}
			//
			//Draw
			//
			elseif($data['homegoals'] == $data['awaygoals'])
			{
				$home_draws[0]++;
				$home_goals[0] = $home_goals[0] + $data['homegoals'];
				$home_goalsagainst[0] = $home_goalsagainst[0] + $data['awaygoals'];
			}
			//
			//Lost
			//
			elseif($data['homegoals'] < $data['awaygoals'])
			{
				$home_loses[0]++;
				$home_goals[0] = $home_goals[0] + $data['homegoals'];
				$home_goalsagainst[0] = $home_goalsagainst[0] + $data['awaygoals'];
			}
		}
		//
		//Away mathc
		//
		else
		{
			//
			//Win
			//
			if($data['awaygoals'] > $data['homegoals'])
			{
				$away_wins[0]++;
				$away_goals[0] = $away_goals[0] + $data['awaygoals'];
				$away_goalsagainst[0] = $away_goalsagainst[0] + $data['homegoals'];
			}
			//
			//Draw
			//
			elseif($data['awaygoals'] == $data['homegoals'])
			{
				$away_draws[0]++;
				$away_goals[0] = $away_goals[0] + $data['awaygoals'];
				$away_goalsagainst[0] = $away_goalsagainst[0] + $data['homegoals'];
			}
			//
			//Lost
			//
			elseif($data['awaygoals'] < $data['homegoals'])
			{
				$away_loses[0]++;
				$away_goals[0] = $away_goals[0] + $data['awaygoals'];
				$away_goalsagainst[0] = $away_goalsagainst[0] + $data['homegoals'];
			}
		}
	}


	//
	//Lets check away team
	//
	while($data = $xoopsDB->fetchArray($query2))
	{
		//
		//Home match
		//
		if($data['homeid'] == $defaultawayid)
		{
			//
			//Win
			//
			if($data['homegoals'] > $data['awaygoals'])
			{
				$home_wins[1]++;
				$home_goals[1] = $home_goals[1] + $data['homegoals'];
				$home_goalsagainst[1] = $home_goalsagainst[1] + $data['awaygoals'];
			}
			//
			//Draw
			//
			elseif($data['homegoals'] == $data['awaygoals'])
			{
				$home_draws[1]++;
				$home_goals[1] = $home_goals[1] + $data['homegoals'];
				$home_goalsagainst[1] = $home_goalsagainst[1] + $data['awaygoals'];
			}
			//
			//Lost
			//
			elseif($data['homegoals'] < $data['awaygoals'])
			{
				$home_loses[1]++;
				$home_goals[1] = $home_goals[1] + $data['homegoals'];
				$home_goalsagainst[1] = $home_goalsagainst[1] + $data['awaygoals'];
			}
		}
		//
		//Away match
		//
		else
		{
			//
			//Win
			//
			if($data['awaygoals'] > $data['homegoals'])
			{
				$away_wins[1]++;
				$away_goals[1] = $away_goals[1] + $data['awaygoals'];
				$away_goalsagainst[1] = $away_goalsagainst[1] + $data['homegoals'];
			}
			//
			//Draw
			//
			elseif($data['awaygoals'] == $data['homegoals'])
			{
				$away_draws[1]++;
				$away_goals[1] = $away_goals[1] + $data['awaygoals'];
				$away_goalsagainst[1] = $away_goalsagainst[1] + $data['homegoals'];
			}
			//
			//Lost
			//
			elseif($data['awaygoals'] < $data['homegoals'])
			{
				$away_loses[1]++;
				$away_goals[1] = $away_goals[1] + $data['awaygoals'];
				$away_goalsagainst[1] = $away_goalsagainst[1] + $data['homegoals'];
			}
		}
	}

	//
	//Calculates home team data
	//
	$home_played[0] = $home_wins[0] + $home_draws[0] + $home_loses[0];
	$away_played[0] = $away_wins[0] + $away_draws[0] + $away_loses[0];

	$total_wins[0] = $home_wins[0] + $away_wins[0];
	$total_draws[0] = $home_draws[0] + $away_draws[0];
	$total_loses[0] = $home_loses[0] + $away_loses[0];
	$total_goals[0] = $home_goals[0] + $away_goals[0];
	$total_goalsagainst[0] = $home_goalsagainst[0] + $away_goalsagainst[0];
	$total_played[0] = $total_wins[0] + $total_draws[0] + $total_loses[0];
	$total_points[0] = ($for_win*$total_wins[0]) + ($for_draw*$total_draws[0]) + ($for_lose*$total_loses[0]);

	$total_gd[0] = $total_goals[0] - $total_goalsagainst[0];
	$home_gd[0] = $home_goals[0] - $home_goalsagainst[0];
	$away_gd[0] = $away_goals[0] - $away_goalsagainst[0];

	//
	//Calculates away team data
	//
	$home_played[1] = $home_wins[1] + $home_draws[1] + $home_loses[1];
	$away_played[1] = $away_wins[1] + $away_draws[1] + $away_loses[1];

	$total_wins[1] = $home_wins[1] + $away_wins[1];
	$total_draws[1] = $home_draws[1] + $away_draws[1];
	$total_loses[1] = $home_loses[1] + $away_loses[1];
	$total_goals[1] = $home_goals[1] + $away_goals[1];
	$total_goalsagainst[1] = $home_goalsagainst[1] + $away_goalsagainst[1];
	$total_played[1] = $total_wins[1] + $total_draws[1] + $total_loses[1];
	$total_points[1] = ($for_win*$total_wins[1]) + ($for_draw*$total_draws[1]) + ($for_lose*$total_loses[1]);

	$total_gd[1] = $total_goals[1] - $total_goalsagainst[1];
	$home_gd[1] = $home_goals[1] - $home_goalsagainst[1];
	$away_gd[1] = $away_goals[1] - $away_goalsagainst[1];

	//$xoopsDB->freeRecordSet($query);
	//$xoopsDB->freeRecordSet($query2);

	//
	//query to get head-to-head data
	//
	$headtohead_query = $xoopsDB->query("SELECT
	O.OpponentName AS hometeam,
	OP.OpponentName AS awayteam,
	LM.LeagueMatchHomeID AS homeid,
	LM.LeagueMatchAwayID AS awayid,
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
	LM.LeagueMatchSeasonID LIKE '$defaultseasonid' AND
    LM.LeagueMatchHomeGoals IS NOT NULL AND
    LM.LeagueMatchAwayGoals IS NOT NULL AND
	((LM.LeagueMatchHomeID = ".intval($defaulthomeid)." AND LM.LeagueMatchAwayID = ".intval($defaultawayid).") OR
	(LM.LeagueMatchHomeID = ".intval($defaultawayid)." AND LM.LeagueMatchAwayID = ".intval($defaulthomeid)."))
	ORDER BY LM.LeagueMatchDate DESC
	")
	;


	//
	//Sets zero for head-to-head variables
	//Also checks the data in while-loop
	//

	$hth_home_wins = 0;
	$hth_home_draws = 0;
	$hth_home_loses = 0;
	$hth_home_goals = 0;
	$hth_home_goals_against = 0;

	$hth_away_wins = 0;
	$hth_away_draws = 0;
	$hth_away_loses = 0;
	$hth_away_goals = 0;
	$hth_away_goals_against = 0;

	$i = 0;
	while($data = $xoopsDB->fetchArray($headtohead_query))
	{
		//
		//Maximum five games into variables
		//
		if($i < 5)
		{
			$hth_matches_date[$i] = $data['date'];
			$hth_matches_home[$i] = $data['hometeam'];
			$hth_matches_away[$i] = $data['awayteam'];
			$hth_matches_score[$i] = $data['homegoals'] . " - " . $data['awaygoals'];

			$i++;
		}

		//
		//hometeams home match
		//
		if($data['homeid'] == $defaulthomeid)
		{
			if($data['homegoals'] > $data['awaygoals'])
			{
				$hth_home_wins++;
				$hth_away_loses++;
			}
			elseif($data['homegoals'] == $data['awaygoals'])
			{
				$hth_home_draws++;
				$hth_away_draws++;
			}
			elseif($data['homegoals'] < $data['awaygoals'])
			{
				$hth_home_loses++;
				$hth_away_wins++;
			}

			$hth_home_goals = $hth_home_goals + $data['homegoals'];
			$hth_home_goals_against = $hth_home_goals_against + $data['awaygoals'];
			$hth_away_goals = $hth_away_goals + $data['awaygoals'];
			$hth_away_goals_against = $hth_away_goals_against + $data['homegoals'];

		}
		elseif($data['homeid'] == $defaultawayid)
		{
			if($data['homegoals'] > $data['awaygoals'])
			{
				$hth_away_wins++;
				$hth_home_loses++;
			}
			elseif($data['homegoals'] == $data['awaygoals'])
			{
				$hth_away_draws++;
				$hth_home_draws++;
			}
			elseif($data['homegoals'] < $data['awaygoals'])
			{
				$hth_away_loses++;
				$hth_home_wins++;
			}

			$hth_away_goals = $hth_away_goals + $data['homegoals'];
			$hth_away_goals_against = $hth_away_goals_against + $data['awaygoals'];
			$hth_home_goals = $hth_home_goals + $data['awaygoals'];
			$hth_home_goals_against = $hth_home_goals_against + $data['homegoals'];
		}
	}

	//$xoopsDB->freeRecordSet($headtohead_query);


	?>

	<tr>
	<td align="center" valign="middle" width="35%">
	<b><u><?= $namedata['homename'] ?></u></b>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_VERSUS;?>
	</td>

	<td align="center" valign="middle" width="35%">
	<b><u><?= $namedata['awayname'] ?></u></b>
	</td>
	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $top_bg ?>">
	<b><?php echo _LS_HEADTOHEAD;?></b>
	</td>
	</tr>

	<tr>

	<td align="center" valign="middle">
	<b><?= "$hth_home_wins-$hth_home_draws-$hth_home_loses" ?></b>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_WDL;?>
	</td>

	<td align="center" valign="middle">
	<b><?= "$hth_away_wins-$hth_away_draws-$hth_away_loses" ?></b>
	</td>

	</tr>

	<tr>

	<td align="center" valign="middle">
	<b><?= "$hth_home_goals-$hth_home_goals_against" ?></b>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_GOALSDIFF;?>
	</td>

	<td align="center" valign="middle">
	<b><?= "$hth_away_goals-$hth_away_goals_against" ?></b>
	</td>

	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $top_bg ?>">
	<b><?php echo _LS_LATHEADTOHEAD;?></b>
	</td>
	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle">

	<?php

	for($j = 0 ; $j < $i ; $j++)
	{
		echo"$hth_matches_date[$j]: $hth_matches_home[$j] - $hth_matches_away[$j]&nbsp;&nbsp;&nbsp;$hth_matches_score[$j]<br>";
	}

	?>

	</td>
	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $top_bg ?>">
	<b><?php echo _LS_OVMATSTATS;?></b>         
	</td>
	</tr>

	<tr>

	<td align="center" valign="middle">
	<b><?= $total_points[0] ?></b>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_POINTSEARNED;?>
	</td>

	<td align="center" valign="middle">
	<b><?= $total_points[1] ?></b>
	</td>

	</tr>

	<tr>

	<td align="center" valign="middle">
	<b><?= $total_played[0] ?></b>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_OVMATPLAYED;?>
	</td>

	<td align="center" valign="middle">
	<b><?= $total_played[1] ?></b>
	</td>

	</tr>

	<tr>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($total_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($total_wins[0]/$total_played[0]));
	}

	echo"$total_wins[0]</b> ($temp %)";

	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_OVMATWON;?>
	</td>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($total_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($total_wins[1]/$total_played[1]));
	}

	echo"$total_wins[1]</b> ($temp %)";

	?>
	</b>
	</td>

	</tr>

	<tr>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($total_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($total_draws[0]/$total_played[0]));
	}

	echo"$total_draws[0]</b> ($temp %)";

	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_OVMATDRAWN;?>
	</td>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($total_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($total_draws[1]/$total_played[1]));
	}

	echo"$total_draws[1]</b> ($temp %)";

	?></b>
	</td>

	</tr>

	<tr>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($total_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($total_loses[0]/$total_played[0]));
	}

	echo"$total_loses[0]</b> ($temp %)";

	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_OVMATLOST;?>
	</td>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($total_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($total_loses[1]/$total_played[1]));
	}

	echo"$total_loses[1]</b> ($temp %)";

	?>
	</b>
	</td>

	</tr>

	<tr>
	<td align="center" valign="middle" colspan="3">
	<img src="images/line.gif" width="<?= $templine_width ?>" height="5" ALT=""><br>
	</td>
	</tr>



	<tr>

	<td align="center" valign="middle">
	<b><?= $home_played[0] ?></b>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_HOMEMATPLAYED;?>
	</td>

	<td align="center" valign="middle">
	<b><?= $home_played[1] ?></b>
	</td>

	</tr>

	<tr>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($home_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($home_wins[0]/$home_played[0]));
	}

	echo"$home_wins[0]</b> ($temp %)";

	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_HOMEMATWON;?>
	</td>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($home_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($home_wins[1]/$home_played[1]));
	}

	echo"$home_wins[1]</b> ($temp %)";

	?></b>
	</td>

	</tr>

	<tr>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($home_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($home_draws[0]/$home_played[0]));
	}

	echo"$home_draws[0]</b> ($temp %)";

	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_HOMEMATDRAWN;?>
	</td>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($home_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($home_draws[1]/$home_played[1]));
	}

	echo"$home_draws[1]</b> ($temp %)";

	?></b>
	</td>

	</tr>

	<tr>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($home_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($home_loses[0]/$home_played[0]));
	}

	echo"$home_loses[0]</b> ($temp %)";

	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_HOMEMATLOST;?>
	</td>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($home_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($home_loses[1]/$home_played[1]));
	}

	echo"$home_loses[1]</b> ($temp %)";

	?></b>
	</td>

	</tr>

	<tr>
	<td align="center" valign="middle" colspan="3">
	<img src="images/line.gif" width="<?= $templine_width ?>" height="5" ALT=""><br>
	</td>
	</tr>

	<tr>

	<td align="center" valign="middle">
	<b><?= $away_played[0] ?></b>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_AWAYMATPLAYED;?>
	</td>

	<td align="center" valign="middle">
	<b><?= $away_played[1] ?></b>
	</td>

	</tr>

	<tr>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($away_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($away_wins[0]/$away_played[0]));
	}

	echo"$away_wins[0]</b> ($temp %)";

	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_AWAYMATWON;?>
	</td>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($away_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($away_wins[1]/$away_played[1]));
	}

	echo"$away_wins[1]</b> ($temp %)";

	?></b>
	</td>

	</tr>

	<tr>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($away_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($away_draws[0]/$away_played[0]));
	}

	echo"$away_draws[0]</b> ($temp %)";

	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_AWAYMATDRAWN;?>
	</td>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($away_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($away_draws[1]/$away_played[1]));
	}

	echo"$away_draws[1]</b> ($temp %)";

	?></b>
	</td>

	</tr>

	<tr>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($away_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($away_loses[0]/$away_played[0]));
	}

	echo"$away_loses[0]</b> ($temp %)";

	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_AWAYMATLOST;?>
	</td>

	<td align="center" valign="middle">
	<b>
	<?php

	//
	//Avoid divide by zero
	//
	if($away_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($away_loses[1]/$away_played[1]));
	}

	echo"$away_loses[1]</b> ($temp %)";

	?></b>
	</td>

	</tr>


	<tr>
	<td align="center" valign="middle" colspan="3">
	<img src="images/line.gif" width="<?= $templine_width ?>" height="5" ALT=""><br>
	</td>
	</tr>


	<tr>

	<td align="center" valign="middle">
	<b><?php

	if($total_gd[0] >= 0)
		echo'+';

	echo"$total_gd[0]</b> ($total_goals[0] - $total_goalsagainst[0])";

	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_OVGOALSDIFF;?>
	</td>

	<td align="center" valign="middle">
	<b><?php

	if($total_gd[1] >= 0)
		echo'+';

	echo"$total_gd[1]</b> ($total_goals[1] - $total_goalsagainst[1])";

	?></b>
	</td>

	</tr>

	<tr>

	<td align="center" valign="middle">
	<b><?php

	if($home_gd[0] >= 0)
		echo'+';

	echo"$home_gd[0]</b> ($home_goals[0] - $home_goalsagainst[0])";

	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_GOALSDIFFHOME;?>
	</td>

	<td align="center" valign="middle">
	<b><?php

	if($home_gd[1] >= 0)
		echo'+';

	echo"$home_gd[1]</b> ($home_goals[1] - $home_goalsagainst[1])";

	?></b>
	</td>

	</tr>

	<tr>

	<td align="center" valign="middle">
	<b><?php

	if($away_gd[0] >= 0)
		echo'+';

	echo"$away_gd[0]</b> ($away_goals[0] - $away_goalsagainst[0])";

	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_GOALSDIFFAWAY;?>
	</td>

	<td align="center" valign="middle">
	<b><?php

	if($away_gd[1] >= 0)
		echo'+';

	echo"$away_gd[1]</b> ($away_goals[1] - $away_goalsagainst[1])";

	?></b>
	</td>

	</tr>

	<?php
	//
	//query to get biggest home win/lost/aggr for hometeam
	//
	$query = $xoopsDB->query("SELECT
	MAX(LeagueMatchHomeGoals - LeagueMatchAwayGoals) AS maxhomewin,
	MAX(LeagueMatchAwayGoals - LeagueMatchHomeGoals) AS maxhomelost,
	MAX(LeagueMatchHomeGoals + LeagueMatchAwayGoals) AS maxhomeaggregate
	FROM ".$xoopsDB->prefix("tplls_leaguematches")."
	WHERE LeagueMatchHomeID = ".intval($defaulthomeid)." AND
	LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	$maxhomedata_hometeam = $xoopsDB->fetchArray($query);

	//$xoopsDB->freeRecordSet($query);

	//
	//query to get biggest away win/lost/aggr for hometeam
	//
	$query = $xoopsDB->query("SELECT
	MAX(LeagueMatchAwayGoals - LeagueMatchHomeGoals) AS maxawaywin,
	MAX(LeagueMatchHomeGoals - LeagueMatchAwayGoals) AS maxawaylost,
	MAX(LeagueMatchHomeGoals + LeagueMatchAwayGoals) AS maxawayaggregate
	FROM ".$xoopsDB->prefix("tplls_leaguematches")."
	WHERE LeagueMatchAwayID = '$defaulthomeid' AND
	LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	$maxawaydata_hometeam = $xoopsDB->fetchArray($query);

	//$xoopsDB->freeRecordSet($query);

	//
	//query to get biggest home win/lost/aggr for awayteam
	//
	$query = $xoopsDB->query("SELECT
	MAX(LeagueMatchHomeGoals - LeagueMatchAwayGoals) AS maxhomewin,
	MAX(LeagueMatchAwayGoals - LeagueMatchHomeGoals) AS maxhomelost,
	MAX(LeagueMatchHomeGoals + LeagueMatchAwayGoals) AS maxhomeaggregate
	FROM ".$xoopsDB->prefix("tplls_leaguematches")."
	WHERE LeagueMatchHomeID = '$defaultawayid' AND
	LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	$maxhomedata_awayteam = $xoopsDB->fetchArray($query);

	//$xoopsDB->freeRecordSet($query);

	//
	//querF to get biggest away win/lost/aggr for awayteam
	//
	$query = $xoopsDB->query("SELECT
	MAX(LeagueMatchAwayGoals - LeagueMatchHomeGoals) AS maxawaywin,
	MAX(LeagueMatchHomeGoals - LeagueMatchAwayGoals) AS maxawaylost,
	MAX(LeagueMatchHomeGoals + LeagueMatchAwayGoals) AS maxawayaggregate
	FROM ".$xoopsDB->prefix("tplls_leaguematches")."
	WHERE LeagueMatchAwayID = '$defaultawayid' AND
	LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	$maxawaydata_awayteam = $xoopsDB->fetchArray($query);

	//$xoopsDB->freeRecordSet($query);

	//
	//Lets put max results into variables
	//
	$maxhomewin_home = $maxhomedata_hometeam['maxhomewin'];
	$maxhomelost_home = $maxhomedata_hometeam['maxhomelost'];
	$maxhomeaggregate_home = $maxhomedata_hometeam['maxhomeaggregate'];
	$maxawaywin_home = $maxawaydata_hometeam['maxawaywin'];
	$maxawaylost_home = $maxawaydata_hometeam['maxawaylost'];
	$maxawayaggregate_home = $maxawaydata_hometeam['maxawayaggregate'];

	$maxhomewin_away = $maxhomedata_awayteam['maxhomewin'];
	$maxhomelost_away = $maxhomedata_awayteam['maxhomelost'];
	$maxhomeaggregate_away = $maxhomedata_awayteam['maxhomeaggregate'];
	$maxawaywin_away = $maxawaydata_awayteam['maxawaywin'];
	$maxawaylost_away = $maxawaydata_awayteam['maxawaylost'];
	$maxawayaggregate_away = $maxawaydata_awayteam['maxawayaggregate'];

	?>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $top_bg ?>">
	<b><?php echo _LS_BIGHOMEWIN;?></b>
	</td>
	</tr>

	<tr>

	<td align="center" valign="top">
	<?php
	//
	//query to get all the biggest home wins: home
	//
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchAwayID AND
	LM.LeagueMatchHomeID = '$defaulthomeid' AND
	(LM.LeagueMatchHomeGoals - LM.LeagueMatchAwayGoals) = '$maxhomewin_home' AND
	(LM.LeagueMatchHomeGoals - LM.LeagueMatchAwayGoals) > 0 AND
	LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	//
	//If there are no home wins->print none
	//
	if($xoopsDB->getRowsNum($query) == 0)
	{
		echo _LS_NONE;
	}
	else
	{
		while($data = $xoopsDB->fetchArray($query))
		{
			echo"$data[homegoals] - $data[awaygoals] "._LS_VERSUS." $data[name]<br>\n";
		}
	}

	//$xoopsDB->freeRecordSet($query);

	?>
	</td>

	<td align="center" valign="middle">
	&nbsp;
	</td>

	<td align="center" valign="top">
	<?php
	//
	//query to get all the biggest home wins: away
	//
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchAwayID AND
	LM.LeagueMatchHomeID = '$defaultawayid' AND
	(LM.LeagueMatchHomeGoals - LM.LeagueMatchAwayGoals) = '$maxhomewin_away' AND
	(LM.LeagueMatchHomeGoals - LM.LeagueMatchAwayGoals) > 0 AND
	LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	//
	//If there are no home wins->print none
	//
	if($xoopsDB->getRowsNum($query) == 0)
	{
		echo _LS_NONE;
	}
	else
	{
		while($data = $xoopsDB->fetchArray($query))
		{
			echo"$data[homegoals] - $data[awaygoals] "._LS_VERSUS." $data[name]<br>\n";
		}
	}

	//$xoopsDB->freeRecordSet($query);

	?>
	</td>

	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $top_bg ?>">
	<b><?php echo _LS_BIGHOMELOST;?></b>
	</td>
	</tr>


	<tr>

	<td align="center" valign="top">
	<?php
	//
	//query to get all the biggest home losses: home
	//
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchAwayID AND
	LM.LeagueMatchHomeID = '$defaulthomeid' AND
	(LM.LeagueMatchAwayGoals - LM.LeagueMatchHomeGoals) = '$maxhomelost_home' AND
	(LM.LeagueMatchAwayGoals - LM.LeagueMatchHomeGoals) > 0 AND
	LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	//
	//If there are no home loses->print none
	//
	if($xoopsDB->getRowsNum($query) == 0)
	{
		echo _LS_NONE;
	}
	else
	{
		while($data = $xoopsDB->fetchArray($query))
		{
			echo"$data[homegoals] - $data[awaygoals] "._LS_VERSUS." $data[name]<br>\n";
		}
	}

	//$xoopsDB->freeRecordSet($query);

	?>
	</td>

	<td align="center" valign="middle">
	&nbsp;
	</td>

	<td align="center" valign="top">
	<?php
	//
	//query to get all the biggest home loses: away
	//
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchAwayID AND
	LM.LeagueMatchHomeID = '$defaultawayid' AND
	(LM.LeagueMatchAwayGoals - LM.LeagueMatchHomeGoals) = '$maxhomelost_away' AND
	(LM.LeagueMatchAwayGoals - LM.LeagueMatchHomeGoals) > 0 AND
	LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	//
	//If there are no home loses->print none
	//
	if($xoopsDB->getRowsNum($query) == 0)
	{
		echo _LS_NONE;
	}
	else
	{
		while($data = $xoopsDB->fetchArray($query))
		{
			echo"$data[homegoals] - $data[awaygoals] "._LS_VERSUS." $data[name]<br>\n";
		}
	}

	//$xoopsDB->freeRecordSet($query);

	?>
	</td>

	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $top_bg ?>">
	<b><?php echo _LS_HIGHAGGHOME;?></b>
	</td>
	</tr>

	<tr>

	<td align="center" valign="top">
	<?php
	//
	//query to get all the biggest home aggregate: home
	//
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchAwayID AND
	LM.LeagueMatchHomeID = '$defaulthomeid' AND
	(LM.LeagueMatchHomeGoals + LM.LeagueMatchAwayGoals) = '$maxhomeaggregate_home' AND
	LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	while($data = $xoopsDB->fetchArray($query))
	{
		echo"$data[homegoals] - $data[awaygoals] "._LS_VERSUS." $data[name]<br>\n";
	}

	//$xoopsDB->freeRecordSet($query);

	?>
	</td>

	<td align="center" valign="middle">
	&nbsp;
	</td>

	<td align="center" valign="top">
	<?php
	//
	//query to get all the biggest home aggregate: away
	//
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchAwayID AND
	LM.LeagueMatchHomeID = '$defaultawayid' AND
	(LM.LeagueMatchHomeGoals + LM.LeagueMatchAwayGoals) = '$maxhomeaggregate_away' AND
	LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	while($data = $xoopsDB->fetchArray($query))
	{
		echo"$data[homegoals] - $data[awaygoals] "._LS_VERSUS." $data[name]<br>\n";
	}

	//$xoopsDB->freeRecordSet($query);

	?>
	</td>

	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $top_bg ?>">
	<b><?php echo _LS_BIGAWAYWIN;?></b>
	</td>
	</tr>

	<tr>

	<td align="center" valign="top">
	<?php
	//
	//query to get all the biggest away wins: home
	//
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchHomeID AND
	LM.LeagueMatchAwayID = '$defaulthomeid' AND
	(LM.LeagueMatchAwayGoals - LM.LeagueMatchHomeGoals) = '$maxawaywin_home' AND
	(LM.LeagueMatchAwayGoals - LM.LeagueMatchHomeGoals) > 0 AND
	LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	//
	//If there are no away wins->print none
	//
	if($xoopsDB->getRowsNum($query) == 0)
	{
		echo _LS_NONE;
	}
	else
	{
		while($data = $xoopsDB->fetchArray($query))
		{
			echo"$data[awaygoals] - $data[homegoals] "._LS_VERSUS." $data[name]<br>\n";
		}
	}

	//$xoopsDB->freeRecordSet($query);

	?>
	</td>

	<td align="center" valign="middle">
	&nbsp;
	</td>

	<td align="center" valign="top">
	<?php
	//
	//query to get all the biggest away wins: away
	//
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchHomeID AND
	LM.LeagueMatchAwayID = '$defaultawayid' AND
	(LM.LeagueMatchAwayGoals - LM.LeagueMatchHomeGoals) = '$maxawaywin_away' AND
	(LM.LeagueMatchAwayGoals - LM.LeagueMatchHomeGoals) > 0 AND
	LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	//
	//If there are no away wins->print none
	//
	if($xoopsDB->getRowsNum($query) == 0)
	{
		echo _LS_NONE;
	}
	else
	{
		while($data = $xoopsDB->fetchArray($query))
		{
			echo"$data[awaygoals] - $data[homegoals] "._LS_VERSUS." $data[name]<br>\n";
		}
	}

	//$xoopsDB->freeRecordSet($query);

	?>
	</td>

	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $top_bg ?>">
	<b><?php echo _LS_BIGAWAYLOSS;?></b>
	</td>
	</tr>

	<tr>

	<td align="center" valign="top">
	<?php
	//
	//query to get all the biggest away loses: home
	//
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchHomeID AND
	LM.LeagueMatchAwayID = '$defaulthomeid' AND
	(LM.LeagueMatchHomeGoals - LM.LeagueMatchAwayGoals) = '$maxawaylost_home' AND
	(LM.LeagueMatchHomeGoals - LM.LeagueMatchAwayGoals) > 0 AND
	LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	//
	//If there are no away loses->print none
	//
	if($xoopsDB->getRowsNum($query) == 0)
	{
		echo _LS_NONE;
	}
	else
	{
		while($data = $xoopsDB->fetchArray($query))
		{
			echo"$data[awaygoals] - $data[homegoals] "._LS_VERSUS." $data[name]<br>\n";
		}
	}

	//$xoopsDB->freeRecordSet($query);

	?>
	</td>

	<td align="center" valign="middle">
	&nbsp;
	</td>

	<td align="center" valign="top">
	<?php
	//
	//query to get all the biggest away loses: away
	//
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchHomeID AND
	LM.LeagueMatchAwayID = '$defaultawayid' AND
	(LM.LeagueMatchHomeGoals - LM.LeagueMatchAwayGoals) = '$maxawaylost_away' AND
	(LM.LeagueMatchHomeGoals - LM.LeagueMatchAwayGoals) > 0 AND
	LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	//
	//If there are no away wins->print none
	//
	if($xoopsDB->getRowsNum($query) == 0)
	{
		echo _LS_NONE;
	}
	else
	{
		while($data = $xoopsDB->fetchArray($query))
		{
			echo"$data[awaygoals] - $data[homegoals] "._LS_VERSUS." $data[name]<br>\n";
		}
	}

	//$xoopsDB->freeRecordSet($query);

	?>
	</td>

	</tr>


	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $top_bg ?>">
	<b><?php echo _LS_HIGHAGGAWAY;?></b>
	</td>
	</tr>

	<tr>

	<td align="center" valign="top">
	<?php
	//
	//query to get all the biggest away aggregate: home
	//
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchHomeID AND
	LM.LeagueMatchAwayID = '$defaulthomeid' AND
	(LM.LeagueMatchAwayGoals + LM.LeagueMatchHomeGoals) = '$maxawayaggregate_home' AND
	LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	while($data = $xoopsDB->fetchArray($query))
	{
		echo"$data[awaygoals] - $data[homegoals] "._LS_VERSUS." $data[name]<br>\n";
	}

	//$xoopsDB->freeRecordSet($query);

	?>
	</td>

	<td align="center" valign="middle">
	&nbsp;
	</td>

	<td align="center" valign="top">
	<?php
	//
	//query to get all the biggest away aggregate: away
	//
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeGoals AS homegoals,
	LM.LeagueMatchAwayGoals AS awaygoals
	FROM ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchHomeID AND
	LM.LeagueMatchAwayID = '$defaultawayid' AND
	(LM.LeagueMatchAwayGoals + LM.LeagueMatchHomeGoals) = '$maxawayaggregate_away' AND
	LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
	")
	;

	while($data = $xoopsDB->fetchArray($query))
	{
		echo"$data[awaygoals] - $data[homegoals] "._LS_VERSUS." $data[name]<br>\n";
	}

	//$xoopsDB->freeRecordSet($query);

	?>
	</td>

	</tr>


	</table>

	</td>
	</tr>
	</table>
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
