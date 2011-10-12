<?php require_once('init.php');

if(!(isset($_GET['id'])||isset($_POST['id']))) die('No task selected');

if(!isset($_POST['id'])) {
  $resprop = mysql_query("SELECT priority,DATE(deadline) AS deadline,status,categories_id1,name FROM tasks WHERE tasks_id=".$_GET['id']) or die(mysql_error());
  $rescat = mysql_query("SELECT * FROM categories ORDER BY name ASC");

  $t = mysql_fetch_object($resprop);

  $categories = "";
  while($c = mysql_fetch_object($rescat))
    $categories .= $c->categories_id==$t->categories_id1?"<option value='".$c->categories_id."' selected='true'>".$c->name."</option>":"<option value='".$c->categories_id."'>".$c->name."</option>";
}
else {
  foreach($_POST as $var => $val)
    ${"$var"} = $val;
  $sql = "UPDATE tasks SET priority='$priority',deadline='$deadline',status='$status',categories_id1='$categories_id1',name='$name' WHERE tasks_id=".$id;
  mysql_query($sql) or die(mysql_error());
  header("Location: history.php?id=$id");
}


?>
<!doctype html>
<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

<script language="javascript" src="calendar_db.js"></script>
<link rel="stylesheet" href="calendar.css" />
</head>

<h1>MPP-NV IT Task Metadata</h1>

<p>(<a href="index.php">See all tasks</a>)&nbsp;&nbsp;(<a href="history.php?id=<?=$_GET['id']?>">Task history</a>)</p>


<form name="taskprop" method="post">

<table>
  <tr>
    <th>Title</th>
    <td><input type="text" name="name" value="<?=$t->name?>" /></td>
  </tr>
  <tr>
    <th>Category</th>
    <td>
      <select name="categories_id1">
	<?=$categories?>
      </select>
    </td>
  </tr>
  <tr>
    <th>Status</th>
    <td>
      <input type="radio" name="status" value="0" <?=$t->status==0?"checked='true'":''?>> Closed<br />
      <input type="radio" name="status" value="1" <?=$t->status==1?"checked='true'":''?>> Open<br />
      <input type="radio" name="status" value="2" <?=$t->status==2?"checked='true'":''?>> Backburner
    </td>
  </tr>
  <tr>
    <th>Priority</th>
    <td>
      <select name="priority">
	<?php for($i=5; $i>=0; $i--):?>
	<option value="<?=$i?>" <?=$t->priority==$i?'selected="true"':''?>><?=$i?></option>
	<?php endfor; ?>
      </select>
    </td>
  </tr>
  <tr>
    <th>Deadline</th>
    <td>
      <input type="text" name="deadline" value="<?=$t->deadline?>" />

      <script language="javascript">
      new tcal ({
	'formname': 'taskprop',
	'controlname': 'deadline'
      });
      </script>
    </td>
  </tr>
</table>

<br /><br />
<input type="hidden" name="id" value="<?=$_GET['id']?>" />
<input type="submit" value="Update" />

</form>


