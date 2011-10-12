<?php require_once('init.php');

$status = isset($_GET['status'])?$_GET['status']:1;

$res = mysql_query("CALL task_report($status)") or die(mysql_error());

?>
<!doctype html>
<head><meta http-equiv="Content-type" content="text/html;charset=UTF-8" /></head>

<form name="ticket" method="get" action="history.php" style="position:absolute;top:0;right:250px;">
T.<input type="text" name="id" size="3" />
<input type="submit" value="Load" />
</form>

<form name="search" method="get" action="search.php" style="position:absolute;top:0;right:10px;">
<input type="text" name="search" />
<input type="submit" value="Search" />
</form>


<h1>MPP-NV IT Task Summary</h1>

<p>(<a href="create.php">Create Task</a>)&nbsp;&nbsp;(<a href="daily.php">Daily History</a>)</p>

<table cellspacing='2' cellpadding='2' border='2' width='100%'>
  <?php
    echo "<tr>";
    for($i=0; $i<mysql_num_fields($res); $i++) {
      $fieldname = mysql_field_name($res, $i);
      switch($fieldname) {
	case "status":
	  $nextstatus = 0;
	  switch($status) {
	    case 0: $nextstatus=1; break;
	    case 1: $nextstatus=2; break;
	    case 2: $nextstatus=0; break;
	  }
	  //echo "<th><a href='?status=".($nextstatus)."'>".mysql_field_name($res, $i)."</a></th>";
	  echo "<th><a href='?status=".($nextstatus)."'>"."st"."</a></th>";
	  break;
	default:
	  echo "<th>".mysql_field_name($res, $i)."</th>";
      }
    }
    echo "</tr>";
    while($r = mysql_fetch_array($res)) {
      echo "<tr>";
      for($j=0; $j<count($r) && $r[$j]!=NULL; $j++) {
        echo "<td>";
	switch($j) {
	  case 0:
	    echo "<a href='taskprop.php?id=".$r[0]."'>".$r[$j]."</a>";
	    break;
	  case 4:
	    echo "<a href='history.php?id=".$r[0]."'>".$r[$j]."</a>";
	    break;
	  default:
	    echo bracket_filter($r[$j]);
	}
	echo "</td>";
      }
      echo "</tr>";
    }
  ?>



</table>


