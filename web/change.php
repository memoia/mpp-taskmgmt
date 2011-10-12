<?php require_once('init.php');

if(!isset($_POST['id']) || !isset($_POST['signature']))
  die(header('Location: index.php'));


$id	  = $_POST['id'];
$body	  = wordwrap($_POST['body']);
$signature= $_POST['signature'];

if(empty($body))
  $sql = "DELETE FROM tasks_info WHERE tasks_id='".$id."' AND MD5(body)='".$signature."' LIMIT 1;";
else
  $sql = "UPDATE tasks_info SET updated=updated, body='".$body."' WHERE tasks_id='".$id."' AND MD5(body)='".$signature."'";


//mysql_real_escape_string($_POST['body'])."')";

mysql_query($sql) or die($sql.mysql_error());
header("Location: history.php?id=".$id);





