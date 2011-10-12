<?php require_once('init.php');

//$res = mysql_query("CALL task_report()") or die(mysql_error());

if(!isset($_POST['body'])) {
  $rescat = mysql_query("SELECT * FROM categories ORDER BY name ASC");

  $categories = "";
  while($c = mysql_fetch_object($rescat))
    $categories .= $c->name=="IT"?"<option value='".$c->name."' selected='true'>".$c->name."</option>":"<option value='".$c->name."'>".$c->name."</option>";
}
else {
  // perform s_create(category_name, task_name, task_body) and forward to taskprop.php or history.php
  foreach($_POST as $var => $val)
    ${"$var"} = $val;

  $sql = "CALL s_create('$category_name','$name','".wordwrap($body)."')";
  $id = mysql_result(mysql_query($sql),0);
  header("Location: history.php?id=$id");
}

?>
<!doctype html>
<head><meta http-equiv="Content-type" content="text/html;charset=UTF-8" /></head>

<h1>MPP-NV IT Create Task</h1>

<p>(<a href="index.php">See all tasks</a>)</p>

<form method="post" action="create.php">

<table>
  <tr>
    <th>Title</th>
    <td><input size='50' type="text" name="name" /></td>
  </tr>
  <tr>
    <th>Category</th>
    <td>
      <select name="category_name">
	<?=$categories?>
      </select>
    </td>
  </tr>
  <tr>
    <th>Body</th>
    <td>  
      <textarea name="body" rows="10" cols="48"></textarea>
    </td>
  </tr>
</table>

<br /><br />

<input type="submit" value="Create" />

</form>

