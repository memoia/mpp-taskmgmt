<?php require_once('init.php');

if(!isset($_GET['id'])) die('No Task ID selected');

$res = mysql_query("CALL task_history(".$_GET['id'].")") or die(mysql_error());

?>
<!doctype html>
<head>
  <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
  <style type='text/css'>
    td.histbody textarea {
      width: 99%;
      height: 12em;
    }
    td.histbody input[type=submit] {
      float:right;
    }
  </style>
  <script type='text/javascript' src='jquery.js'></script>
  <script type='text/javascript'>
  var editing_history = false;

  function edit_history(e) {
    if(editing_history) return;

    editing_history = true;
    oldbody = $(this).html();
    $(this).html('<form method="post" action="change.php">'
		  + '<textarea name="body">' + oldbody + '</textarea>'
		  + '<input type="hidden" name="signature" value="'+$(this).attr('signature')+'" />'
		  + '<input type="hidden" name="id" value="<?=$_GET['id']?>" />'
		  + '<br /><input type="submit" value="Change" />'
		  + '</form>');
  }

  $(function() {
    $('td.histbody').bind('dblclick', edit_history);
  });
  </script>
</head>

<h1>MPP-NV IT Task History</h1>

<p>(<a href="index.php">See all tasks</a>)&nbsp;&nbsp;(<a href="taskprop.php?id=<?=$_GET['id']?>">Update task</a>)</p>

<table cellspacing='2' cellpadding='2' border='2' width='100%'>
  <?php
    echo "<tr>";
    for($i=0; $i<mysql_num_fields($res); $i++)
      echo "<th>".mysql_field_name($res, $i)."</th>";
    echo "</tr>";
    while($r = mysql_fetch_array($res)) {
      echo "<tr>";
      for($j=0; $j<count($r) && $r[$j]!=NULL; $j++) {
	if($j==0)
	  echo "<td class='datetime'><a name='".$r[$j]."'>".$r[$j]."</a></td>";
	else
	  echo "<td class='histbody' signature='".md5($r[$j])."'>".bracket_filter($r[$j])."</td>";
      }
      echo "</tr>";
    }
  ?>
</table>

<?php require("update.php"); ?>
