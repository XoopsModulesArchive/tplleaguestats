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
//Includes preferences
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
//query to get last updated date and time
//
//Use site http://www.mysql.com/doc/en/Date_and_time_functions.html (DATE_FORMAT)
//to return date format that fits to your site
//

$updated_query = $xoopsDB->query("SELECT MAX(LeagueMatchCreated) AS last_updated FROM ".$xoopsDB->prefix("tplls_leaguematches"));
$ludata = $xoopsDB->fetchArray($updated_query);
$last_update = date('d.m.Y @ H:i', $ludata['last_updated']);
//mysql_free_result($updated_query);
//$xoopsDB->freeRecordSet($updated_query);

//
//If session variables are registered
//


//if(!session_is_registered('defaultseasonid') || !session_is_registered('defaultshow') || !session_is_registered('defaulttable'))
if ( !isset( $_SESSION['defaultseasonid'] ) || !isset( $_SESSION['defaultshow'] ) || !isset( $_SESSION['defaulttable'] ))
{
    $_SESSION['defaultseasonid'] = $d_season_id;
    $_SESSION['defaultshow'] = $show_all_or_one;
    $_SESSION['defaulttable'] = $show_table;
    $defaultseasonid = intval($_SESSION['defaultseasonid']);
    $defaultshow = $_SESSION['defaultshow'];
    $defaulttable = $_SESSION['defaulttable'];
}
else
{
    
    $defaultseasonid = intval($_SESSION['defaultseasonid']);
    $defaultshow = $_SESSION['defaultshow'];
    $defaulttable = $_SESSION['defaulttable'];
}

//
//Gets seasons and match types for dropdowns
//
$get_seasons = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("tplls_seasonnames")." WHERE SeasonPublish = '1' ORDER BY SeasonName");

//
//Sort by points, sort variable is not set
//
if (isset($_GET['sort']) || isset($_POST['sort'])) { //see if $sort is in GET or POST variables, POST overriding GET (Mithrandir)
if (isset($_POST['sort'])) {
    $sort = $_POST['sort'];
}
else {
    $sort = $_GET['sort'];
}
}
if(!isset($sort))
{
    $sort = 'pts';
}



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


<?php echo _LS_MOVETO;?><select name="moveto">
<option value="headtohead.php"><?php echo _LS_HEADTOHEAD;?></option>
<option value="season.php"><?php echo _LS_SEASONSTATS;?></option>
</select> <input type="submit" class="button" value=">>" name="submit6">

<br>
<?php echo _LS_CALENDAR;?>
<select name="change_show">
<?php

if($defaultshow == 1)
{
    echo"<option value=\"1\" SELECTED>"._LS_CALENDARALL."</option>
	<option value=\"2\">"._LS_CALENDAROWN." </option>
	<option value=\"3\">"._LS_CALENDARNONE."</option>";
}
elseif($defaultshow == 2)
{
    echo"<option value=\"1\">"._LS_CALENDARALL."</option>
	<option value=\"2\" SELECTED>"._LS_CALENDAROWN."</option>
	<option value=\"3\">"._LS_CALENDARNONE."</option>";
}
elseif($defaultshow == 3)
{
    echo"<option value=\"1\">"._LS_CALENDARALL."</option>
	<option value=\"2\">"._LS_CALENDAROWN."</option>
	<option value=\"3\" SELECTED>"._LS_CALENDARNONE."</option>";
}

//
//If all is chosen from season selector, set default to %
//
if($defaultseasonid == 0)
$defaultseasonid = '%';

?>
</select>
<input type="submit" class="button" value=">>" name="submit2">
&nbsp;&nbsp;&nbsp;
<?php echo _LS_MODETABLE;?>
<select name="change_table">
<?php
if($defaulttable == 1)
{
    echo"<option value=\"4\">"._LS_MODETABLESIMP."</option>
	<option value=\"1\" SELECTED>"._LS_MODETABLETRA."</option>
	<option value=\"2\">"._LS_MODETABLEMAT."</option>
	<option value=\"3\">"._LS_MODETABLEREC."</option>";
}
elseif($defaulttable == 2)
{
    echo"<option value=\"4\">"._LS_MODETABLESIMP."</option>
	<option value=\"1\" SELECTED>"._LS_MODETABLETRA."</option>
	<option value=\"2\">"._LS_MODETABLEMAT."</option>
	<option value=\"3\">"._LS_MODETABLEREC."</option>";
}
elseif($defaulttable == 3)
{
    echo"<option value=\"4\">"._LS_MODETABLESIMP."</option>
	<option value=\"1\" SELECTED>"._LS_MODETABLETRA."</option>
	<option value=\"2\">"._LS_MODETABLEMAT."</option>
	<option value=\"3\">"._LS_MODETABLEREC."</option>";
}
elseif($defaulttable == 4)
{
    echo"<option value=\"4\">"._LS_MODETABLESIMP."</option>
	<option value=\"1\" SELECTED>"._LS_MODETABLETRA."</option>
	<option value=\"2\">"._LS_MODETABLEMAT."</option>
	<option value=\"3\">"._LS_MODETABLEREC."</option>";
}
?>
</select> <input type="submit" class="button" value=">>" name="submit3">

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

		<!-- last updated table -->
		<table width="100%" cellspacing="1" cellpadding="2" border="0">
		<tr>
		<td align="left" valign="middle">
		&nbsp;
		</td>
		</tr>
		</table>

		<?php
		//
		//Tarkastetaan, mikä taulukko tulostetaan
		//
		if($defaulttable == 1 || $defaulttable == 3)
		{
		?>

		<table width="100%" cellspacing="1" cellpadding="2" border="0">

		<tr>

		<td align="center" valign="middle" colspan="3">
		&nbsp;
		</td>

		<td align="center" valign="middle" colspan="5" bgcolor="<?php echo $top_bg ?>">
		<b><i><?php echo _LS_COLOVERALL;?></i></b>
		</td>

		<td align="center" valign="middle" colspan="5" bgcolor="<?php echo $top_bg ?>">
		<b><i><?php echo _LS_COLHOME;?></i></b>
		</td>

		<td align="center" valign="middle" colspan="5" bgcolor="<?php echo $top_bg ?>">
		<b><i><?php echo _LS_COLAWAY;?></i></b>
		</td>

		<td align="center" valign="middle" colspan="2">
		&nbsp;
		</td>

		</tr>

		<tr>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		&nbsp;<b><?php echo _LS_POSSHORT;?></b>
		</td>

		<td align="left" valign="middle" bgcolor="<?php echo $top_bg ?>">
		&nbsp;<b><?php echo _LS_TEAM;?></b>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=pts"><?php echo _LS_PTSSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=tw"><?php echo _LS_WINSSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=td"><?php echo _LS_DRAWSSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=tl"><?php echo _LS_LOSESSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=tf"><?php echo _LS_GOALSSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=ta"><?php echo _LS_GOALSAGSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=hw"><?php echo _LS_WINSSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=hd"><?php echo _LS_DRAWSSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=hl"><?php echo _LS_LOSESSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=hf"><?php echo _LS_GOALSSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=ha"><?php echo _LS_GOALSAGSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=aw"><?php echo _LS_WINSSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=ad"><?php echo _LS_DRAWSSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=al"><?php echo _LS_LOSESSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=af"><?php echo _LS_GOALSSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=aa"><?php echo _LS_GOALSAGSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=d">+/-</a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=pld"><?php echo _LS_PLAYEDSHORT;?></a>
		</td>
		</tr>
		<?php
		}
		elseif($defaulttable == 2)
		{
		?>
		<table width="100%" cellspacing="1" cellpadding="2" border="0">

		<tr>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		&nbsp;<b><?php echo _LS_POSSHORT;?></b>
		</td>

		<td align="left" valign="middle" bgcolor="<?php echo $top_bg ?>">
		&nbsp;<b><?php echo _LS_TEAM;?></b>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=pts"><?php echo _LS_PTSSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=a_pts"><?php echo _LS_AVGPTS;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=w"><?php echo _LS_WINSPERC;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=d"><?php echo _LS_DRAWSPERC;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=l"><?php echo _LS_LOSSPERC;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=af"><?php echo _LS_AVGGSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=aa"><?php echo _LS_AVGGAGSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=agd"><?php echo _LS_AVGDIFF;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=pld"><?php echo _LS_PLAYEDSHORT;?></a>
		</td>
                

		</tr>

		<?php
		}
		elseif($defaulttable == 4)
		{
		?>
		<table width="100%" cellspacing="1" cellpadding="2" border="0">

		<tr>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		&nbsp;<b><?php echo _LS_POSSHORT;?></b>
		</td>

		<td align="left" valign="middle" bgcolor="<?php echo $top_bg ?>">
		&nbsp;<b><?php echo _LS_TEAM;?></b>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=pts"><?php echo _LS_PTSSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=tw"><?php echo _LS_WINSSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=td"><?php echo _LS_DRAWSSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=tl"><?php echo _LS_LOSESSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=tf"><?php echo _LS_GOALSSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=ta"><?php echo _LS_GOALSAGSHORT;?></a>
		</td>

		<td align="center" valign="middle" bgcolor="<?php echo $top_bg ?>">
		<a href="?sort=pld"><?php echo _LS_PLAYEDSHORT;?></a>
		</td>
                 
		</tr>
		<?php
		}
		?>

		<?php
		
		//
		//query to get teams from selected season
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
		
		//
		//Lets read teams into the table
		//
		$i = 0;
		while($data = $xoopsDB->fetchArray($get_teams))
		{
		    $team[$i] = $data['name'];
		    $teamid[$i] = $data['id'];
		    
		    //
		    //Which table style is chosen
		    //
		    if($defaulttable == 1 || $defaulttable == 2 || $defaulttable == 4)
		    {
		        //
		        //Home data
		        //
		        $query = $xoopsDB->query("SELECT
				COUNT(DISTINCT LM.LeagueMatchID) AS homewins
				FROM
				".$xoopsDB->prefix("tplls_leaguematches")." LM
				WHERE
				LM.LeagueMatchHomeWinnerID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
				")
		        ;
		        
		        //
		        //Home wins into the table
		        //
		        $mdata = $xoopsDB->fetchArray($query);
		        $homewins[$i] = $mdata['homewins'];
		        
		        //$xoopsDB->freeRecordSet($query);
		        
		        $query = $xoopsDB->query("SELECT
				COUNT(DISTINCT LM.LeagueMatchID) AS homedraws
				FROM
				".$xoopsDB->prefix("tplls_leaguematches")." LM
				WHERE
				LM.LeagueMatchHomeTieID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
				")
		        ;
		        
		        //
		        //Home draws into the table
		        //
		        $mdata = $xoopsDB->fetchArray($query);
		        $homedraws[$i] = $mdata['homedraws'];
		        
		        //$xoopsDB->freeRecordSet($query);
		        
		        $query = $xoopsDB->query("SELECT
				COUNT(DISTINCT LM.LeagueMatchID) AS homeloses
				FROM
				".$xoopsDB->prefix("tplls_leaguematches")." LM
				WHERE
				LM.LeagueMatchHomeLoserID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
				")
		        ;
		        
		        //
		        //Home loses into the table
		        //
		        $mdata = $xoopsDB->fetchArray($query);
		        $homeloses[$i] = $mdata['homeloses'];
		        
		        //$xoopsDB->freeRecordSet($query);
		        
		        
		        //
		        //Away data
		        //
		        $query = $xoopsDB->query("SELECT
				COUNT(DISTINCT LM.LeagueMatchID) AS awaywins
				FROM
				".$xoopsDB->prefix("tplls_leaguematches")." LM
				WHERE
				LM.LeagueMatchAwayWinnerID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
				")
		        ;
		        
		        //
		        //Away wins into the table
		        //
		        $mdata = $xoopsDB->fetchArray($query);
		        $awaywins[$i] = $mdata['awaywins'];
		        
		        //$xoopsDB->freeRecordSet($query);
		        
		        $query = $xoopsDB->query("SELECT
				COUNT(DISTINCT LM.LeagueMatchID) AS awaydraws
				FROM
				".$xoopsDB->prefix("tplls_leaguematches")." LM
				WHERE
				LM.LeagueMatchAwayTieID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
				")
		        ;
		        
		        //
		        //Away draws into the table
		        //
		        $mdata = $xoopsDB->fetchArray($query);
		        $awaydraws[$i] = $mdata['awaydraws'];
		        
		        //$xoopsDB->freeRecordSet($query);
		        
		        $query = $xoopsDB->query("SELECT
				COUNT(DISTINCT LM.LeagueMatchID) AS awayloses
				FROM
				".$xoopsDB->prefix("tplls_leaguematches")." LM
				WHERE
				LM.LeagueMatchAwayLoserID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
				")
		        ;
		        
		        //
		        //Away loses into the table
		        //
		        $mdata = $xoopsDB->fetchArray($query);
		        $awayloses[$i] = $mdata['awayloses'];
		        
		        //$xoopsDB->freeRecordSet($query);
		        
		        //
		        //query to get goals
		        //
		        
		        $query = $xoopsDB->query("SELECT
				SUM( LM.LeagueMatchHomeGoals) AS homegoals
				FROM
				".$xoopsDB->prefix("tplls_leaguematches")." LM
				WHERE
				LM.LeagueMatchHomeID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
				")
		        ;
		        
		        //
		        //Goals scored in hom
		        //
		        $mdata = $xoopsDB->fetchArray($query);
		        if(is_null($mdata['homegoals']))
		        $homegoals[$i] = 0;
		        else
		        $homegoals[$i] = $mdata['homegoals'];
		        
		        //$xoopsDB->freeRecordSet($query);
		        
		        $query = $xoopsDB->query("SELECT
				SUM( LM.LeagueMatchAwayGoals) AS homegoalsagainst
				FROM
				".$xoopsDB->prefix("tplls_leaguematches")." LM
				WHERE
				LM.LeagueMatchHomeID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
				")
		        ;
		        
		        //
		        //Goals against in home
		        //
		        $mdata = $xoopsDB->fetchArray($query);
		        if(is_null($mdata['homegoalsagainst']))
		        $homegoalsagainst[$i] = 0;
		        else
		        $homegoalsagainst[$i] = $mdata['homegoalsagainst'];
		        
		        //$xoopsDB->freeRecordSet($query);
		        
		        
		        $query = $xoopsDB->query("SELECT
				SUM( LM.LeagueMatchAwayGoals) AS awaygoals
				FROM
				".$xoopsDB->prefix("tplls_leaguematches")." LM
				WHERE
				LM.LeagueMatchAwayID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
				")
		        ;
		        
		        //
		        //Goals scored in away
		        //
		        $mdata = $xoopsDB->fetchArray($query);
		        if(is_null($mdata['awaygoals']))
		        $awaygoals[$i] = 0;
		        else
		        $awaygoals[$i] = $mdata['awaygoals'];
		        
		        //$xoopsDB->freeRecordSet($query);
		        
		        $query = $xoopsDB->query("SELECT
				SUM( LM.LeagueMatchHomeGoals) AS awaygoalsagainst
				FROM
				".$xoopsDB->prefix("tplls_leaguematches")." LM
				WHERE
				LM.LeagueMatchAwayID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
				")
		        ;
		        
		        //
		        //Goals against in away
		        //
		        $mdata = $xoopsDB->fetchArray($query);
		        if(is_null($mdata['awaygoalsagainst']))
		        $awaygoalsagainst[$i] = 0;
		        else
		        $awaygoalsagainst[$i] = $mdata['awaygoalsagainst'];
		        
		        //$xoopsDB->freeRecordSet($query);
		    }
		    //
		    //Recent form
		    //
		    elseif($defaulttable == 3)
		    {
		        //
		        //Counter are set to zero
		        //
		        $homewins[$i] = 0;
		        $homedraws[$i] = 0;
		        $homeloses[$i] = 0;
		        $awaywins[$i] = 0;
		        $awaydraws[$i] = 0;
		        $awayloses[$i] = 0;
		        $homegoals[$i] = 0;
		        $homegoalsagainst[$i] = 0;
		        $awaygoals[$i] = 0;
		        $awaygoalsagainst[$i] = 0;
		        
		        //
		        //query to get latest 6 matches
		        //
		        $query = $xoopsDB->query("SELECT
				LM.LeagueMatchHomeID AS homeid,
				LM.LeagueMatchAwayID AS awayid,
				LM.LeagueMatchHomewinnerID AS homewinner,
				LM.LeagueMatchHomeLoserID AS homeloser,
				LM.LeagueMatchAwayWinnerID AS awaywinner,
				LM.LeagueMatchAwayLoserID AS awayloser,
				LM.LeagueMatchHomeTieID AS hometie,
				LM.LeagueMatchAwayTieID AS awaytie,
				LM.LeagueMatchHomeGoals AS homegoals,
				LM.LeagueMatchAwayGoals AS awaygoals
				FROM
				".$xoopsDB->prefix("tplls_leaguematches")." LM
				WHERE
				(LM.LeagueMatchHomeWinnerID = '$teamid[$i]' OR
				LM.LeagueMatchHomeLoserID = '$teamid[$i]' OR
				LM.LeagueMatchAwayWinnerID = '$teamid[$i]' OR
				LM.LeagueMatchAwayLoserID = '$teamid[$i]' OR
				LM.LeagueMatchHomeTieID = '$teamid[$i]' OR
				LM.LeagueMatchAwayTieID = '$teamid[$i]') AND
				LM.LeagueMatchSeasonID LIKE '$defaultseasonid'
				ORDER BY LM.LeagueMatchDate DESC
				LIMIT 6
				")
		        ;
		        
		        //
		        //Lets use while to get correct numbers
		        //
		        while($row = $xoopsDB->fetchArray($query))
		        {
		            //
		            //If goals are null
		            //
		            if(is_null($row['homegoals']))
		            $row['homegoals'] = 0;
		            
		            if(is_null($row['awaygoals']))
		            $row['awaygoals'] = 0;
		            
		            
		            //
		            //Home win
		            //
		            if($row['homewinner'] == $teamid[$i])
		            {
		                $homewins[$i]++;
		            }
		            //
		            //Home lost
		            //
		            elseif($row['homeloser'] == $teamid[$i])
		            {
		                $homeloses[$i]++;
		            }
		            //
		            //Home draw
		            //
		            elseif($row['hometie'] == $teamid[$i])
		            {
		                $homedraws[$i]++;
		            }
		            //
		            //Away win
		            //
		            elseif($row['awaywinner'] == $teamid[$i])
		            {
		                $awaywins[$i]++;
		            }
		            //
		            //Away lost
		            //
		            elseif($row['awayloser'] == $teamid[$i])
		            {
		                $awayloses[$i]++;
		            }
		            //
		            //Away draw
		            //
		            elseif($row['awaytie'] == $teamid[$i])
		            {
		                $awaydraws[$i]++;
		            }
		            
		            
		            //
		            //Calculates goals and goals against
		            //
		            if($row['homeid'] == $teamid[$i])
		            {
		                $homegoals[$i] = $homegoals[$i] + $row['homegoals'];
		                $homegoalsagainst[$i] = $homegoalsagainst[$i] + $row['awaygoals'];
		            }
		            else
		            {
		                $awaygoals[$i] = $awaygoals[$i] + $row['awaygoals'];
		                $awaygoalsagainst[$i] = $awaygoalsagainst[$i] + $row['homegoals'];
		            }
		            
		            
		            
		        }
		        
		        //$xoopsDB->freeRecordSet($query);
		        
		    }
		    
		    //
		    //Check what table is used..
		    //
		    if($defaulttable == 1 || $defaulttable == 3 || $defaulttable == 4)
		    {
		        
		        //
		        //Calculates points and matches
		        //
		        
		        $wins[$i] = ($homewins[$i]+$awaywins[$i]);
		        $draws[$i] = ($homedraws[$i]+$awaydraws[$i]);
		        $loses[$i] = ($homeloses[$i]+$awayloses[$i]);
		        $goals_for[$i] = ($homegoals[$i] + $awaygoals[$i]);
		        $goals_against[$i] = ($homegoalsagainst[$i] + $awaygoalsagainst[$i]);
		        
		        //
		        //Lets make change in points if there are data in tplls_deductedpoints-table
		        //
		        if($defaulttable == 1 || $defaulttable == 4)
		        {
		            $get_deduct = $xoopsDB->query("SELECT points
					FROM ".$xoopsDB->prefix("tplls_deductedpoints")." 
					WHERE seasonid LIKE '$defaultseasonid' AND
					teamid = '$teamid[$i]'
					LIMIT 1
					")
		            ;
		            
		            $temp_points = 0;
		            
		            if($xoopsDB->getRowsNum($get_deduct) > 0)
		            {
		                while($d_points = $xoopsDB->fetchArray($get_deduct))
		                {
		                    $temp_points = $temp_points + $d_points['points'];
		                }
		            }
		            
		            //$xoopsDB->freeRecordSet($get_deduct);
		        }
		        else
		        {
		            $temp_points = 0;
		        }
		        
		        $points[$i] = $temp_points + (($homewins[$i]+$awaywins[$i])*$for_win) + (($homedraws[$i]+$awaydraws[$i])*$for_draw) + (($homeloses[$i]+$awayloses[$i])*$for_lose);
		        $pld[$i] = $homewins[$i]+$homedraws[$i]+$homeloses[$i]+$awaywins[$i]+$awaydraws[$i]+$awayloses[$i];
		        
		        //
		        //Calculates goal difference
		        //
		        $diff[$i] = ($homegoals[$i] + $awaygoals[$i]) - ($homegoalsagainst[$i] + $awaygoalsagainst[$i]);
		        
		    }
		    elseif($defaulttable == 2)
		    {
		        $wins[$i] = ($homewins[$i]+$awaywins[$i]);
		        $draws[$i] = ($homedraws[$i]+$awaydraws[$i]);
		        $loses[$i] = ($homeloses[$i]+$awayloses[$i]);
		        $goals_for[$i] = ($homegoals[$i] + $awaygoals[$i]);
		        $goals_against[$i] = ($homegoalsagainst[$i] + $awaygoalsagainst[$i]);
		        
		        //
		        //Lets make change in points if there are data in tplls_deductedpoints-table
		        //
		        $get_deduct = $xoopsDB->query("SELECT points
				FROM ".$xoopsDB->prefix("tplls_deductedpoints")."
				WHERE seasonid LIKE '$defaultseasonid' AND
				teamid = '$teamid[$i]'
				LIMIT 1
				")
		        ;
		        
		        $temp_points = 0;
		        
		        if($xoopsDB->getRowsNum($get_deduct) > 0)
		        {
		            while($d_points = $xoopsDB->fetchArray($get_deduct))
		            {
		                $temp_points = $temp_points + $d_points['points'];
		            }
		        }
		        
		        //$xoopsDB->freeRecordSet($get_deduct);
		        
		        $points[$i] = $temp_points + (($homewins[$i]+$awaywins[$i])*$for_win) + (($homedraws[$i]+$awaydraws[$i])*$for_draw) + (($homeloses[$i]+$awayloses[$i])*$for_lose);
		        $pld[$i] = $homewins[$i]+$homedraws[$i]+$homeloses[$i]+$awaywins[$i]+$awaydraws[$i]+$awayloses[$i];
		        
		        //
		        //To avoid divide by zero
		        //
		        if($pld[$i] != 0)
		        {
		            $win_pros[$i] = round(100*($wins[$i]/$pld[$i]), 2);
		            $draw_pros[$i] = round(100*($draws[$i]/$pld[$i]), 2);
		            $loss_pros[$i] = round(100*($loses[$i]/$pld[$i]), 2);
		            
		            $av_points[$i] = round($points[$i]/$pld[$i], 2);
		            
		            $av_for[$i] = round($goals_for[$i]/$pld[$i], 2);
		            $av_against[$i] = round($goals_against[$i]/$pld[$i], 2);
		        }
		        else
		        {
		            $win_pros[$i] = 0;
		            $draw_pros[$i] = 0;
		            $loss_pros[$i] = 0;
		            
		            $av_points[$i] = 0;
		            
		            $av_for[$i] = 0;
		            $av_against[$i] = 0;
		        }
		        
		        $av_diff[$i] = $av_for[$i] - $av_against[$i];
		        
		    }
		    
		    $i++;
		}
		
		$qty = $xoopsDB->getRowsNum($get_teams);
		
		//$xoopsDB->freeRecordSet($get_teams);
		
		
		//
		//Which table?
		//
		if($defaulttable == 1 || $defaulttable == 3 || $defaulttable == 4)
		{
		    
		    
		    //
		    //What sort type is chosen?
		    //
            switch ($sort) {
                case 'pts':
                    if (isset($points)) {
                        array_multisort($points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homegoals, $homegoalsagainst, $awaygoals, $awaygoalsagainst);
                    }
                    break;

                case
                    'd':
                    if (isset($diff)) {
                        array_multisort($diff, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homegoals, $homegoalsagainst, $awaygoals, $awaygoalsagainst);
                    }
                    break;

                case 'pld':
                    if (isset($pld)) {
                        array_multisort($pld, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homegoals, $homegoalsagainst, $awaygoals, $awaygoalsagainst);
                    }
                    break;

                case 'tw':
                    if (isset($wins)) {
                        array_multisort($wins, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homedraws, $homeloses, $homewins, $awaywins, $awaydraws, $awayloses, $homegoals, $homegoalsagainst, $awaygoals, $awaygoalsagainst);
                    }
                    break;

                case 'td':
                    if (isset($draws)) {
                        array_multisort($draws, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homeloses, $awaywins, $homedraws, $awaydraws, $awayloses, $homegoals, $homegoalsagainst, $awaygoals, $awaygoalsagainst);
                    }
                    break;

                case
                    'tl':
                    if (isset($loses)) {
                        array_multisort($loses, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $awaywins, $awaydraws, $homeloses, $awayloses, $homegoals, $homegoalsagainst, $awaygoals, $awaygoalsagainst);
                    }
                    break;

                case 'tf':
                    if (isset($goals_for)) {
                        array_multisort($goals_for, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homegoals, $homegoalsagainst, $awaygoals, $awaygoalsagainst);
                    }
                    break;

                case 'ta':
                    if (isset($goals_against)) {
                        array_multisort($goals_against, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homegoals, $homegoalsagainst, $awaygoals, $awaygoalsagainst);
                    }
                    break;

                case 'hw':
                    if (isset($homewins)) {
                        array_multisort($homewins, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homegoals, $homegoalsagainst, $awaygoals, $awaygoalsagainst);
                    }
                    break;

                case 'hd':
                    if (isset($homedraws)) {
                        array_multisort($homedraws, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homeloses, $awaywins, $awaydraws, $awayloses, $homegoals, $homegoalsagainst, $awaygoals, $awaygoalsagainst);
                    }
                    break;

                case 'hl':
                    if (isset($homeloses)) {
                        array_multisort($homeloses, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $awaywins, $awaydraws, $awayloses, $homegoals, $homegoalsagainst, $awaygoals, $awaygoalsagainst);
                    }
                    break;

                case 'hf':
                    if (isset($homegoals)) {
                        array_multisort($homegoals, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homegoalsagainst, $awaygoals, $awaygoalsagainst);
                    }
                    break;

                case 'ha':
                    if (isset($homegoalsagainst)) {
                        array_multisort($homegoalsagainst, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homegoals, $awaygoals, $awaygoalsagainst);
                    }
                    break;

                case 'aw':
                    if (isset($awaywins)) {
                        array_multisort($awaywins, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaydraws, $awayloses, $homegoals, $homegoalsagainst, $awaygoals, $awaygoalsagainst);
                    }
                    break;

                case 'ad':
                    if (isset($awaydraws)) {
                        array_multisort($awaydraws, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awayloses, $homegoals, $homegoalsagainst, $awaygoals, $awaygoalsagainst);
                    }
                    break;

                case 'al':
                    if (isset($awayloses)) {
                        array_multisort($awayloses, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $homegoals, $homegoalsagainst, $awaygoals, $awaygoalsagainst);
                    }
                    break;

                case 'af':
                    if (isset($awaygoals)) {
                        array_multisort($awaygoals, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homegoals, $homegoalsagainst, $awaygoalsagainst);
                    }
                    break;

                case 'aa':
                    if (isset($awaygoalsagainst)) {
                        array_multisort($awaygoalsagainst, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homegoals, $homegoalsagainst, $awaygoals);
                    }
                    break;

                default:
                    if (isset($points)) {
                        array_multisort($points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $goals_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $goals_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homegoals, $homegoalsagainst, $awaygoals, $awaygoalsagainst);
                    }
                    break;
            }

            if($defaulttable == 1 || $defaulttable == 3)
		    {
		        
		        //
		        //Lets print data
		        //
		        $j=1;
		        $i=0;
		        while($i< $qty)
		        {
		            if(isset($draw_line))
		            {
		                //
		                //Tarkistetaan, piirretäänkö erotusviiva
		                //
		                for($k = 0 ; $k < sizeof($draw_line) ; $k++)
		                {
		                    if($draw_line[$k] == $i)
		                    {
		                        $templine_width = $tb_width-20;
		                        echo"
								<tr>
								<td height=\"5\" colspan=\"20\" align=\"center\" valign=\"middle\">
								<img src=\"images/line.gif\" width=\"$templine_width\" height=\"5\" ALT=\"\"><br>
								</td>
								</tr>
								";
		                    }
		                }
		            }
		            
		            
		            echo"
					<tr>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">
					&nbsp;<b>$j</b>
					</td>

					<td align=\"left\" valign=\"middle\" bgcolor=\"$bg1\">
					&nbsp;<b>$team[$i]</b>
					</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg2\">";
		            if($sort == 'pts')
		            echo'<b>';
		            
		            echo"$points[$i]";
		            
		            if($sort == 'pts')
		            echo'</b>';
		            echo"</td>


					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'tw')
		            echo'<b>';
		            
		            echo"$wins[$i]";
		            
		            if($sort == 'tw')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'td')
		            echo'<b>';
		            
		            echo"$draws[$i]";
		            
		            if($sort == 'td')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'tl')
		            echo'<b>';
		            
		            echo"$loses[$i]";
		            
		            if($sort == 'tl')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'tf')
		            echo'<b>';
		            
		            echo"$goals_for[$i]";
		            
		            if($sort == 'tf')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'ta')
		            echo'<b>';
		            
		            echo"$goals_against[$i]";
		            
		            if($sort == 'ta')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'hw')
		            echo'<b>';
		            
		            echo"$homewins[$i]";
		            
		            if($sort == 'hw')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'hd')
		            echo'<b>';
		            
		            echo"$homedraws[$i]";
		            
		            if($sort == 'hd')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'hl')
		            echo'<b>';
		            
		            echo"$homeloses[$i]";
		            
		            if($sort == 'hl')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'hf')
		            echo'<b>';
		            
		            echo"$homegoals[$i]";
		            
		            if($sort == 'hf')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'ha')
		            echo'<b>';
		            
		            echo"$homegoalsagainst[$i]";
		            
		            if($sort == 'ha')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'aw')
		            echo'<b>';
		            
		            echo"$awaywins[$i]";
		            
		            if($sort == 'aw')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'ad')
		            echo'<b>';
		            
		            echo"$awaydraws[$i]";
		            
		            if($sort == 'ad')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'al')
		            echo'<b>';
		            
		            echo"$awayloses[$i]";
		            
		            if($sort == 'al')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'af')
		            echo'<b>';
		            
		            echo"$awaygoals[$i]";
		            
		            if($sort == 'af')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'aa')
		            echo'<b>';
		            
		            echo"$awaygoalsagainst[$i]";
		            
		            if($sort == 'aa')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            if($sort == 'd')
		            echo'<b>';
		            
		            if($diff[$i] > 0)
		            echo'+';
		            
		            echo"$diff[$i]";
		            
		            if($sort == 'd')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg2\">";
		            
		            if($sort == 'pld')
		            echo'<b>';
		            
		            echo"$pld[$i]";
		            
		            if($sort == 'pld')
		            echo'</b>';
		            
		            echo"</td>
                                       
					</tr>
					";
		            
		            $i++;
		            $j++;
		        }
		    }
		    //
		    //Simple table print
		    //
		    elseif($defaulttable == 4)
		    {
		        //
		        //Lets print data
		        //
		        $j=1;
		        $i=0;
		        while($i< $qty)
		        {
		            if(isset($draw_line))
		            {
		                //
		                //Tarkistetaan, piirretäänkö erotusviiva
		                //
		                for($k = 0 ; $k < sizeof($draw_line) ; $k++)
		                {
		                    if($draw_line[$k] == $i)
		                    {
		                        $templine_width = $tb_width-20;
		                        echo"
								<tr>
								<td height=\"5\" colspan=\"20\" align=\"center\" valign=\"middle\">
								<img src=\"images/line.gif\" width=\"$templine_width\" height=\"5\" ALT=\"\"><br>
								</td>
								</tr>
								";
		                    }
		                }
		            }
		            
		            
		            echo"
					<tr>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">
					&nbsp;<b>$j</b>
					</td>

					<td align=\"left\" valign=\"middle\" bgcolor=\"$bg1\">
					&nbsp;<b>$team[$i]</b>
					</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg2\">";
		            if($sort == 'pts')
		            echo'<b>';
		            
		            echo"$points[$i]";
		            
		            if($sort == 'pts')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'tw')
		            echo'<b>';
		            
		            echo"$wins[$i]";
		            
		            if($sort == 'tw')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'td')
		            echo'<b>';
		            
		            echo"$draws[$i]";
		            
		            if($sort == 'td')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'tl')
		            echo'<b>';
		            
		            echo"$loses[$i]";
		            
		            if($sort == 'tl')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'tf')
		            echo'<b>';
		            
		            echo"$goals_for[$i]";
		            
		            if($sort == 'tf')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		            
		            if($sort == 'ta')
		            echo'<b>';
		            
		            echo"$goals_against[$i]";
		            
		            if($sort == 'ta')
		            echo'</b>';
		            
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$bg2\">";
		            
		            if($sort == 'pld')
		            echo'<b>';
		            
		            echo"$pld[$i]";
		            
		            if($sort == 'pld')
		            echo'</b>';
		            
		            echo"</td>
                                        
					</tr>
					";
		            
		            $i++;
		            $j++;
		        }
		    }
		}
		elseif($defaulttable == 2)
		{
		    
		    //
		    //What sort type is chosen?
		    //
		    switch($sort)
		    {
                case 'pts':
                    if (isset($points)) {
                        array_multisort($points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $pld, $av_points, $win_pros, $draw_pros, $loss_pros, $team);
                    }
                    break;

                case 'a_pts':
                    if (isset($av_points)) {
                        array_multisort($av_points, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $pld, $win_pros, $draw_pros, $loss_pros, $team);
                    }
                    break;

                case 'w':
                    if (isset($av_pros)) {
                        array_multisort($win_pros, SORT_DESC, SORT_NUMERIC, $av_points, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $pld, $draw_pros, $loss_pros, $team);
                    }
                    break;

                case 'd':
                    if (isset($draw_pros)) {
                        array_multisort($draw_pros, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $pld, $av_points, $win_pros, $loss_pros, $team);
                    }
                    break;

                case 'l':
                    if (isset($loss_pros)) {
                        array_multisort($loss_pros, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $pld, $av_points, $win_pros, $draw_pros, $team);
                    }
                    break;

                case 'af':
                    if (isset($av_for)) {
                        array_multisort($av_for, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $pld, $av_points, $win_pros, $draw_pros, $loss_pros, $team);
                    }
                    break;
                case 'aa':
                    if (isset($av_against)) {
                        array_multisort($av_against, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $pld, $av_points, $win_pros, $draw_pros, $loss_pros, $team);
                    }
                    break;

                case 'agd':
                    if (isset($av_diff)) {
                        array_multisort($av_diff, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $pld, $av_points, $win_pros, $draw_pros, $loss_pros, $team);
                    }
                    break;

                case 'pld':
                    if (isset($pld)) {
                        array_multisort($pld, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $av_points, $win_pros, $draw_pros, $loss_pros, $team);
                    }
                    break;
		        
		    }
		    
		    //
		    //Print data
		    //
		    $j=1;
		    $i=0;
		    while($i< $qty)
		    {
		        //
		        //Tehdään numberformatointi
		        //
		        $av_points[$i] = number_format($av_points[$i], 2, '.', '');
		        $av_for[$i] = number_format($av_for[$i], 2, '.', '');
		        $av_against[$i] = number_format($av_against[$i], 2, '.', '');
		        $av_temp = number_format($av_diff[$i], 2, '.', '');
		        $win_pros[$i] = number_format($win_pros[$i], 2, '.', '');
		        $draw_pros[$i] = number_format($draw_pros[$i], 2, '.', '');
		        $loss_pros[$i] = number_format($loss_pros[$i], 2, '.', '');
		        
		        echo"
				<tr>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">
				&nbsp;<b>$j</b>
				</td>

				<td align=\"left\" valign=\"middle\" bgcolor=\"$bg1\">
				&nbsp;<b>$team[$i]</b>
				</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$bg2\">";
		        
		        if($sort == 'pts')
		        echo'<b>';
		        
		        echo"$points[$i]";
		        
		        if($sort == 'pts')
		        echo'</b>';
		        
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		        
		        if($sort == 'a_pts')
		        echo'<b>';
		        
		        echo"$av_points[$i]";
		        
		        if($sort == 'a_pts')
		        echo'</b>';
		        
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		        
		        if($sort == 'w')
		        echo'<b>';
		        
		        echo"$win_pros[$i]";
		        
		        if($sort == 'w')
		        echo'</b>';
		        
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		        
		        if($sort == 'd')
		        echo'<b>';
		        
		        echo"$draw_pros[$i]";
		        
		        if($sort == 'd')
		        echo'</b>';
		        
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		        
		        if($sort == 'l')
		        echo'<b>';
		        
		        echo"$loss_pros[$i]";
		        
		        if($sort == 'l')
		        echo'</b>';
		        
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		        
		        if($sort == 'af')
		        echo'<b>';
		        
		        echo"$av_for[$i]";
		        
		        if($sort == 'af')
		        echo'</b>';
		        
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		        
		        if($sort == 'aa')
		        echo'<b>';
		        
		        echo"$av_against[$i]";
		        
		        if($sort == 'aa')
		        echo'</b>';
		        
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$bg1\">";
		        
		        if($sort == 'agd')
		        echo'<b>';
		        
		        if($av_diff[$i] >= 0)
		        echo'+';
		        
		        echo"$av_temp";
		        
		        if($sort == 'agd')
		        echo'</b>';
		        
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$bg2\">";
		        
		        if($sort == 'pld')
		        echo'<b>';
		        
		        echo"$pld[$i]";
		        
		        if($sort == 'pld')
		        echo'</b>';
		        
		        echo"</td>
                                
				</tr>
				";
		        
		        $i++;
		        $j++;
		    }
		    
		}
		
		?>

		</table>


		<?php
		//
		//Check if match calendar want to be shown
		//
		if($defaultshow != 3)
		{
		?>


		<!-- Sitten ottelukalenteri -->
               <div height="15" align="left" valign="top"> 
               <h6><?php echo _LS_LASTUPDT;?><?= "$last_update" ?></h6>
               </div>

                <?php
                include('notes.txt');
		?>	

                <div align="center" valign="top">
		<h3><?php echo _LS_CALENDARFIXED;?></h3>
		</div>

                <div  align="center" width="100%">
                <table width="100%"><tr>
                <td align="center" width="50%" bgcolor="#E6E6FF"><?php echo _LS_MATPLD;?></td>
                <td align="center" width="50%" bgcolor="#E6E6FF"><?php echo _LS_MATUPC;?></td>
                </tr></table>
                </div>
            
    <table width="100%">
       <tr>
         <td width="50%"> 
                <table border="0" width="100%" cellspacing="2" cellpadding="2">
		

		<?php
		
		//
		//How to print date
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
		//Check which matche want to be printed
		//
		//All
		//
		if($defaultshow == 1)
		{
		    
		    $get_matches = $xoopsDB->query("SELECT O.OpponentName AS hometeam,
			OP.OpponentName AS awayteam,
			LM.LeagueMatchHomeGoals AS goals_home,
			LM.LeagueMatchAwayGoals AS goals_away,
			LM.LeagueMatchID AS id,
			DATE_FORMAT(LM.LeagueMatchDate, '$print_date') AS date
			FROM ".$xoopsDB->prefix("tplls_leaguematches")." LM, ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_opponents")." OP
			WHERE O.OpponentID = LM.LeagueMatchHomeID AND
			OP.OpponentID = LM.LeagueMatchAwayID AND
                        LM.LeagueMatchDate < CURDATE()  AND
			LeagueMatchSeasonID LIKE '$defaultseasonid'
			ORDER BY LM.LeagueMatchDate DESC")
		    ;
		}
		//
		//Own only
		//
		else
		{
		    $get_matches = $xoopsDB->query("SELECT O.OpponentName AS hometeam,
			OP.OpponentName AS awayteam,
			LM.LeagueMatchHomeGoals AS goals_home,
			LM.LeagueMatchAwayGoals AS goals_away,
			LM.LeagueMatchID AS id,
			DATE_FORMAT(LM.LeagueMatchDate, '$print_date') AS date
			FROM ".$xoopsDB->prefix("tplls_leaguematches")." LM, ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_opponents")." OP
			WHERE O.OpponentID = LM.LeagueMatchHomeID AND
			OP.OpponentID = LM.LeagueMatchAwayID AND
                        LM.LeagueMatchDate < CURDATE()  AND
			LeagueMatchSeasonID LIKE '$defaultseasonid' AND
			(O.OpponentOwn = '1' OR OP.OpponentOwn = '1')
			ORDER BY LM.LeagueMatchDate DESC")
		    ;
		}
		
		if($xoopsDB->getRowsNum($get_matches) < 1)
		{
		    echo "&nbsp;<b>"._LS_NOMATCHES."</b>";
		}
		else
		{
		    
		    $i = 0;
		    $temp = '';
		    
		    while($data = $xoopsDB->fetchArray($get_matches))
		    {
		        if($i == 0)
		        {
		            echo"
					<tr>
					<td style=\"padding-left:5px;\" align=\"left\" colspan=\"2\">
					<u><b>$data[date]</b></u>
					</td>
					</tr>
					";
		        }
		        
		        if($data['date'] != "$temp" && $i > 0)
		        {
		            echo"
					<tr>
					<td style=\"padding-left:5px;\" align=\"left\" colspan=\"2\">
					<br>
					<u><b>$data[date]</b></u>
					</td>
					</tr>
					";
		        }
		        
		        echo "
				<tr>
				<td style=\"padding-left:5px;\" align=\"left\" valign=\"top\">
				$data[hometeam] - $data[awayteam]
				</td>
				<td align=\"left\" valign=\"top\">";
		        
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
		
		//$xoopsDB->freeRecordSet($get_matches);
		
		?>

		</table>
              </td>
              <td width="50%">
                   <table border="0" width="100%" cellspacing="2" cellpadding="2">
		

		<?php
		
		//
		//How to print date
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
		//Check which matche want to be printed
		//
		//All
		//
		if($defaultshow == 1)
		{
		    $get_matches = $xoopsDB->query("SELECT O.OpponentName AS hometeam,
			OP.OpponentName AS awayteam,
			LM.LeagueMatchHomeGoals AS goals_home,
			LM.LeagueMatchAwayGoals AS goals_away,
			LM.LeagueMatchID AS id,
			DATE_FORMAT(LM.LeagueMatchDate, '$print_date') AS date
			FROM ".$xoopsDB->prefix("tplls_leaguematches")." LM, ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_opponents")." OP
			WHERE O.OpponentID = LM.LeagueMatchHomeID AND
			OP.OpponentID = LM.LeagueMatchAwayID AND
                        LM.LeagueMatchDate > CURDATE()  AND
			LeagueMatchSeasonID LIKE '$defaultseasonid'
			ORDER BY LM.LeagueMatchDate ASC")
		    ;
		}
		//
		//Own only
		//
		else
		{
		    $get_matches = $xoopsDB->query("SELECT O.OpponentName AS hometeam,
			OP.OpponentName AS awayteam,
			LM.LeagueMatchHomeGoals AS goals_home,
			LM.LeagueMatchAwayGoals AS goals_away,
			LM.LeagueMatchID AS id,
			DATE_FORMAT(LM.LeagueMatchDate, '$print_date') AS date
			FROM ".$xoopsDB->prefix("tplls_leaguematches")." LM, ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_opponents")." OP
			WHERE O.OpponentID = LM.LeagueMatchHomeID AND
			OP.OpponentID = LM.LeagueMatchAwayID AND
                        LM.LeagueMatchDate > CURDATE()  AND
			LeagueMatchSeasonID LIKE '$defaultseasonid' AND
			(O.OpponentOwn = '1' OR OP.OpponentOwn = '1')
			ORDER BY LM.LeagueMatchDate ASC")
		    ;
		}
		
		if($xoopsDB->getRowsNum($get_matches) < 1)
		{
		    echo "&nbsp;<b>"._LS_NOMATCHES."</b>";
		}
		else
		{
		    
		    $i = 0;
		    $temp = '';
		    
		    while($data = $xoopsDB->fetchArray($get_matches))
		    {
		        if($i == 0)
		        {
		            echo"
					<tr>
					<td style=\"padding-left:5px;\" align=\"left\" colspan=\"2\">
					<u><b>$data[date]</b></u>
					</td>
					</tr>
					";
		        }
		        
		        if($data['date'] != "$temp" && $i > 0)
		        {
		            echo"
					<tr>
					<td style=\"padding-left:5px;\" align=\"left\" colspan=\"2\">
					<br>
					<u><b>$data[date]</b></u>
					</td>
					</tr>
					";
		        }
		        
		        echo "
				<tr>
				<td style=\"padding-left:5px;\" align=\"left\" valign=\"top\">
				$data[hometeam] - $data[awayteam]
				</td>
				<td align=\"left\" valign=\"top\">";
		        
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
		
		//$xoopsDB->freeRecordSet($get_matches);
		
		?>

		</table>
             </td>
           </tr>
       </table><br><br>

		<?php
		}
		?>

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