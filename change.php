<?php

include ('../../mainfile.php');

$HTTP_REFERER = $_SERVER['HTTP_REFERER'];

$submit = $_POST['submit'];
$submit2 = $_POST['submit2'];
$submit3 = $_POST['submit3'];
$submit4 = $_POST['submit4'];
$submit5 = $_POST['submit5'];
$submit6 = $_POST['submit6'];

if($submit)
{
	$season = intval($_POST['season']);

	//New value for session variable
	$_SESSION['defaultseasonid'] = $season;

	header("Location: $HTTP_REFERER");
}
elseif($submit2)
{
	$change = intval($_POST['change_show']);

	//New value for session variable
	$_SESSION['defaultshow'] = $change;

	header("Location: index.php?sort=pts");
}
elseif($submit3)
{
	$change = intval($_POST['change_table']);

	//New value for session variable
	$_SESSION['defaulttable'] = $change;

	header("Location: $HTTP_REFERER");
}
elseif($submit4)
{
	$change = intval($_POST['home_id']);

	//New value for session variable
	$_SESSION['defaulthomeid'] = $change;

	header("Location: $HTTP_REFERER");
}
elseif($submit5)
{
	$change = intval($_POST['away_id']);

	//New value for session variable
	$_SESSION['defaultawayid'] = $change;

	header("Location: $HTTP_REFERER");
}
elseif($submit6)
{
	$moveto = $_POST['moveto'];

	header("Location: $moveto");
}
else
{
header("Location: index.php?sort=pts");
}
exit();
?>