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
//if(!session_is_registered('season_name') || !session_is_registered('season_id'))
if ( !isset( $_SESSION['season_name'] ) || !isset( $_SESSION['season_id'] ) )
{
	echo "<form method=\"post\" action=\"leaguematches.php\">";
	echo '<b><?php echo _AM_CHOSEASON;?></b>';
	echo '<select name="season_select">';
	$get_seasons = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("tplls_seasonnames")." ORDER BY SeasonName");

	while($sdata = $xoopsDB->fetchArray($get_seasons))
	{
		echo "<option value=\"$sdata[SeasonID]____$sdata[SeasonName]\">$sdata[SeasonName]</option>\n";
	}
	echo "</select> <input type=\"submit\" name=\"submit\" value=" ._AM_SEASONGO. "></form>";


	mysql_free_result($get_seasons);
}
else
{
	$season_name = $_SESSION['season_name'];
	echo "<form method=\"post\" action=\"leaguematches.php\">";
	echo "<b> "._AM_SEASELECT."  $season_name</b><br><br>";
	echo _AM_SEASELDROP;
	echo '<select name="season_select">';

	$get_seasons = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("tplls_seasonnames")." ORDER BY SeasonName");

	while($sdata = $xoopsDB->fetchArray($get_seasons))
	{
		if($sdata['SeasonID'] == $seasonid)
			echo "<option value=\"$sdata[SeasonID]____$sdata[SeasonName]\" SELECTED>$sdata[SeasonName]</option>\n";
		else
			echo "<option value=\"$sdata[SeasonID]____$sdata[SeasonName]\">$sdata[SeasonName]</option>\n";
	}
	echo "</select> <input type=\"submit\" name=\"submit\" value=" ._AM_SEASONGO. "></form>";

	mysql_free_result($get_seasons);
}
?>

<hr width="100%">

</center>