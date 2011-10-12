<?php require_once('init.php');

$date = !empty($_GET['date'])?"'".$_GET['date']."'":"CURRENT_DATE";

$sql = "SELECT TIME(i.updated) AS time, i.tasks_id, t.name, SUBSTR(i.body,1,96) AS snippet ".
       "FROM tasks_info i INNER JOIN tasks t ON i.tasks_id=t.tasks_id ".
       "WHERE DATE(i.updated)=$date ORDER BY i.updated ASC";

$res = mysql_query($sql) or die(mysql_error());

?>
<!doctype html>
<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

<script language="javascript" src="calendar_db.js"></script>
<link rel="stylesheet" href="calendar.css" />
</head>

<form name="daily" method="get" style="position:absolute;top:0;right:10px;">
<input type="hidden" name="date" value="<?=$_GET['date']?>" />
<script language="javascript">
new tcal ({
  'formname': 'daily',
  'controlname': 'date'
});
</script>
<input type="submit" value="Go" />
</form>


<h1>MPP-NV IT Daily History</h1>

<p>(<a href="index.php">See all tasks</a>)</p>

<table cellspacing='2' cellpadding='2' border='2' width='100%'>
  <?php
    echo "<tr>";
    for($i=0; $i<mysql_num_fields($res); $i++) {
      $name = mysql_field_name($res, $i);
      if($name == "tasks_id")
	continue;
      echo "<th>".mysql_field_name($res, $i)."</th>";
    }
    echo "</tr>";
    while($r = mysql_fetch_array($res)) {
      echo "<tr>";
      for($j=0; $j<count($r) && $r[$j]!=NULL; $j++) {
	switch($j) {
	  case 1:   continue;
	  case 2:
	    echo "<td><a href='history.php?id=".$r[$j-1]."'>".$r[$j]."</a></td>";
	    break;
	  default:
	    echo "<td>".bracket_filter($r[$j])."</td>";
	}
      }
      echo "</tr>";
    }
  ?>
</table>


