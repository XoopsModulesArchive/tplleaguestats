<?php

/*
This is a code that prints a minileaguetable such as
team1 9
team4 8
team9 8
team2 4

FEEL FREE TO MODIFY!!



/*
* Module: Tplleaguestats
* Author: Mithrandir/TPL Design
* Licence: GNU
*/
function b_minitable_show( ) {
    global $xoopsDB;
    $module_handler =& xoops_gethandler('module');
    $module =& $module_handler->getByDirname('tplleaguestats');
    //Get config for News module
    $config_handler =& xoops_gethandler('config');
    if ($module) {
        $moduleConfig =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));
    }
    
    //Season id
    //
    $sql = "SELECT SeasonID, SeasonName FROM ".$xoopsDB->prefix("tplls_seasonnames")." WHERE SeasonDefault=1";
    $seasonname = $xoopsDB->query($sql);
    $seasonname = $xoopsDB->fetchArray($seasonname);
    $season_id = $seasonname['SeasonID'];
    $seasonname = $seasonname['SeasonName'];    
    
    //
    //For win, draw and lost?
    //
    $for_win = $moduleConfig['forwin'];
    $for_draw = $moduleConfig['fordraw'];
    $for_lose = $moduleConfig['forloss'];
    
    //
    //Query to get teams from selected season
    //
    $get_teams = $xoopsDB->query("SELECT DISTINCT
                        O.OpponentName AS name,
                        O.OpponentID AS id
                        FROM ".$xoopsDB->prefix("tplls_opponents")." O, ".$xoopsDB->prefix("tplls_leaguematches")." LM
                        WHERE LM.LeagueMatchSeasonID = '$season_id' AND
                        (O.OpponentID = LM.LeagueMatchHomeID OR
                        O.OpponentID = LM.LeagueMatchAwayID)
                        ORDER BY name");
    
    //
    //Lets read teams into the table
    //
    $i = 0;
    while($data = $xoopsDB->fetchArray($get_teams))
    {
        $team[$data['id']]['name'] = $data['name'];
        $team[$data['id']]['homewins'] = 0;
        $team[$data['id']]['awaywins'] = 0;
        $team[$data['id']]['homeloss'] = 0;
        $team[$data['id']]['awayloss'] = 0;
        $team[$data['id']]['hometie'] = 0;
        $team[$data['id']]['awaytie'] = 0;
        $team[$data['id']]['homegoalsfor'] = 0;
        $team[$data['id']]['homegoalsagainst'] = 0;
        $team[$data['id']]['awaygoalsfor'] = 0;
        $team[$data['id']]['awaygoalsagainst'] = 0;
        $team[$data['id']]['matches'] = 0;
        
    }
    
    //Match data
    $query = $xoopsDB->query("SELECT
                                LM.LeagueMatchID AS mid, 
                                LM.LeagueMatchHomeID as homeid,
                                LM.LeagueMatchAwayID as awayid, 
                                LM.LeagueMatchHomeGoals as homegoals,
                                LM.LeagueMatchAwayGoals as awaygoals 
                                FROM
                                ".$xoopsDB->prefix("tplls_leaguematches")." LM
                            	WHERE
                                LM.LeagueMatchSeasonID = '$season_id' AND LM.LeagueMatchHomeGoals IS NOT NULL");
    while ($matchdata = $xoopsDB->fetchArray($query)) {
        $hometeam = $matchdata['homeid'];
        $awayteam = $matchdata['awayid'];
        
        $team[$hometeam]['matches'] = $team[$hometeam]['matches'] + 1;
        $team[$awayteam]['matches'] = $team[$awayteam]['matches'] + 1;
        
        $team[$hometeam]['homegoalsfor'] = $team[$hometeam]['homegoalsfor'] + $matchdata['homegoals'];
        $team[$awayteam]['awaygoalsagainst'] = $team[$awayteam]['awaygoalsagainst'] + $matchdata['homegoals'];
        $team[$awayteam]['awaygoalsfor'] = $team[$awayteam]['awaygoalsfor'] + $matchdata['awaygoals'];
        $team[$hometeam]['homegoalsagainst'] = $team[$hometeam]['homegoalsagainst'] + $matchdata['awaygoals'];
        
        $goaldiff = $matchdata['homegoals'] - $matchdata['awaygoals'];
        if ($goaldiff > 0) {
            $team[$hometeam]['homewins'] = $team[$hometeam]['homewins'] + 1;
            $team[$awayteam]['awayloss'] = $team[$awayteam]['awayloss'] + 1;
        }
        elseif ($goaldiff == 0) {
            $team[$hometeam]['hometie'] = $team[$hometeam]['hometie'] + 1;
            $team[$awayteam]['awaytie'] = $team[$awayteam]['awaytie'] + 1;
        }
        elseif ($goaldiff < 0) {
            $team[$hometeam]['homeloss'] = $team[$hometeam]['homeloss'] + 1;
            $team[$awayteam]['awaywins'] = $team[$awayteam]['awaywins'] + 1;
        }
    }
    $get_deduct = $xoopsDB->query("SELECT points, teamid FROM ".$xoopsDB->prefix("tplls_deductedpoints")." WHERE seasonid = '$season_id'");
    while ($d_points = $xoopsDB->fetchArray($get_deduct)) {
        $team[$d_points["teamid"]]['d_points'] = $d_points['points'];
    }
    foreach ($team as $teamid => $thisteam) {
        $temp_points = isset($thisteam['d_points']) ? $thisteam['d_points'] : 0;
        $points[$teamid] = ($thisteam['homewins'] * $for_win) + ($thisteam['awaywins'] * $for_win) + ($thisteam['hometie'] * $for_draw) + ($thisteam['awaytie'] * $for_draw) + $temp_points;
        $goalsfor[$teamid] = $thisteam['homegoalsfor'] + $thisteam['awaygoalsfor'];
        $goalsagainst[$teamid] = $thisteam['homegoalsagainst'] + $thisteam['awaygoalsagainst'];
    }
    array_multisort($points, SORT_NUMERIC, SORT_DESC, $goalsfor, SORT_NUMERIC, SORT_DESC, $goalsagainst, SORT_NUMERIC, SORT_DESC, $team, SORT_STRING, SORT_ASC);
    
    //
    //Print the table
    //
    $block['title'] = _BL_MINITABLE;
    $block['content'] = "<table width='100%' cellspacing='2' cellpadding='2' border='0'>
     <tr>
     <td width='50%' align='left'><u>"._BL_TEAM."</u></td>
     <td width='20%' align='center'><u>"._BL_POINTS."</u></td>
     <td width='30%' align='center'><u>"._BL_GOALS."</u></td> 
 </tr></table><marquee behavior='scroll' direction='up' width='100%' height='100' scrollamount='1' scrolldelay='60' onmouseover='this.stop()' onmouseout='this.start()'><table width='100%' cellspacing='2' cellpadding='2' border='0'>";
    foreach ($team as $teamid => $thisteam)
    {
        $block['content'] .= "<tr>
        <td width='50%' align='left'>".$thisteam['name']."</td>
        <td width='20%' align='center'>".$points[$teamid]."</td>
        <td width='30%' align='center'>".$goalsfor[$teamid]."-".$goalsagainst[$teamid]."</td>
        </tr>";
    }
    $block['content'] .= "</table><br><div align=\"center\"><a href=\"".XOOPS_URL."/modules/tplleaguestats/index.php\">"._BL_GOTOMAIN."</a></div></marquee>";
    return $block;
}
?>