<?php require_once('init.php');

if(empty($_GET['search']))
  die(header('Location: index.php'));

$search = trim(stripslashes($_GET['search']));

if(preg_match('/^".*"$/',$search))
  $search = str_replace('"','',$search);
else
  $search = str_replace(' ','%',$search);


$query = "
SELECT 
  t.tasks_id as task,
  t.status as st,
  DATE_FORMAT(i.updated,'%m/%d') as updated,
  c.name as category,
  t.name as title,
  LEFT(i.body,250) as body
FROM tasks t
INNER JOIN categories c ON (t.categories_id1 = c.categories_id)
INNER JOIN tasks_info i ON (i.tasks_id = t.tasks_id)
WHERE
  t.name like '%$search%' OR
  i.body like '%$search%'
ORDER BY i.updated DESC
";

$res = mysql_query($query) or die(mysql_error());

?>
<!doctype html>
<head><meta http-equiv="Content-type" content="text/html;charset=UTF-8" /></head>

<h1>MPP-NV IT Search Results</h1>

<p>(<a href="index.php">See all tasks</a>)</p>

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


