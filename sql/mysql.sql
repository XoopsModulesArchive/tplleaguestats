CREATE TABLE tplls_deductedpoints (
	  id int(11) NOT NULL auto_increment,
	  seasonid int(10) unsigned NOT NULL default '0',
	  teamid smallint(4) unsigned NOT NULL default '0',
	  points tinyint(3) NOT NULL default '0',
	  PRIMARY KEY  (id),
	  KEY seasonid (seasonid),
	  KEY opponentid (teamid)
	) ENGINE=MyISAM;
	
CREATE TABLE tplls_leaguematches (
	  LeagueMatchID int(10) unsigned NOT NULL auto_increment,
	  LeagueMatchSeasonID int(10) unsigned NOT NULL default '0',
	  LeagueMatchDate date NOT NULL default '0000-00-00',
	  LeagueMatchHomeID smallint(4) unsigned NOT NULL default '0',
	  LeagueMatchAwayID smallint(4) unsigned NOT NULL default '0',
	  LeagueMatchHomeWinnerID smallint(4) NOT NULL default '0',
	  LeagueMatchHomeLoserID smallint(4) NOT NULL default '0',
	  LeagueMatchAwayWinnerID smallint(4) NOT NULL default '0',
	  LeagueMatchAwayLoserID smallint(4) NOT NULL default '0',
	  LeagueMatchHomeTieID smallint(4) NOT NULL default '0',
	  LeagueMatchAwayTieID smallint(4) NOT NULL default '0',
	  LeagueMatchHomeGoals tinyint(2) default NULL,
	  LeagueMatchAwayGoals tinyint(2) default NULL,
	  LeagueMatchCreated int(12) NOT NULL,
	  PRIMARY KEY  (LeagueMatchID),
	  KEY LeagueMatchSeasonID (LeagueMatchSeasonID),
	  KEY LeagueMatchHomeID (LeagueMatchHomeID),
	  KEY LeagueMatchAwayID (LeagueMatchAwayID),
	  KEY LeagueMatchHomeWinnerID (LeagueMatchHomeWinnerID),
	  KEY LeagueMatchHomeLoserID (LeagueMatchHomeLoserID),
	  KEY LeagueMatchAwayWinnerID (LeagueMatchAwayWinnerID),
	  KEY LeagueMatchAwayLoserID (LeagueMatchAwayLoserID),
	  KEY LeagueMatchHomeTieID (LeagueMatchHomeTieID),
	  KEY LeagueMatchAwayTieID (LeagueMatchAwayTieID)
	) ENGINE=MyISAM;

CREATE TABLE tplls_opponents (
	  OpponentID smallint(4) unsigned NOT NULL auto_increment,
	  OpponentName varchar(128) NOT NULL default '',
	  OpponentOwn tinyint(1) unsigned NOT NULL default '0',
	  PRIMARY KEY  (OpponentID)
	) ENGINE=MyISAM;
	
CREATE TABLE tplls_seasonnames (
	  SeasonID int(10) unsigned NOT NULL auto_increment,
	  SeasonName varchar(64) NOT NULL default '',
	  SeasonPublish tinyint(1) unsigned NOT NULL default '1',
	  SeasonLine varchar(32) NOT NULL default '1',
	  SeasonDefault tinyint(1) unsigned NOT NULL default '0',
	  PRIMARY KEY  (SeasonID)
	) ENGINE=MyISAM;
	
INSERT INTO tplls_seasonnames (SeasonID, SeasonName, SeasonPublish, SeasonLine)
	VALUES ('1', 'testseason', '1', '1');