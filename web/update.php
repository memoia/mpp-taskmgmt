<?php require_once('init.php');

/*
if(!isset($db)) {
  $db = mysql_connect('192.168.1.201', 'web', 'luxembourgy', false, 65536) or die('Database Server Unavailable');
  mysql_set_charset('utf8', $db);
  //mysql_query("SET NAMES 'utf8'", $db);
  mysql_select_db('taskmgmt', $db) or die('Tasks Database Unavailable');
}
*/


if(isset($_GET['id'])) { ?>
<!doctype html>
<head><meta http-equiv="Content-type" content="text/html;charset=UTF-8" /></head>
<h2>Add History</h2>

<form method="post" action="update.php">
  <textarea name="body" rows=10 cols=50></textarea>
  <br />
  <input type="hidden" name="id" value="<?=$_GET['id']?>" />
  <input type="submit" value="Submit" />
</form>

<?php } elseif(isset($_POST['body']) && isset($_POST['id'])) {

  //$sql = "CALL s_update(".$_POST['id'].",'".mysql_real_escape_string($_POST['body'])."')";
  $sql = "CALL s_update(".$_POST['id'].",'".wordwrap($_POST['body'])."')";
  mysql_query($sql) or die($sql.mysql_error());
  header("Location: history.php?id=".$_POST['id']);

} ?>
