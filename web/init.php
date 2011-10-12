<?php	  // TASKMGMT IMM4MPPNV2009

$db = mysql_connect('hostname', 'username', 'password', false, 65536) or die('Database Server Unavailable');
mysql_set_charset('utf8', $db);
//mysql_query("SET NAMES 'utf8'", $db);
mysql_select_db('taskmgmt', $db) or die('Tasks Database Unavailable');




// IMM 2009-04-16 T.203
function bracket_filter($string) {
  // ticket reference
  $string = ereg_replace("\[T[\.]*([0-9]+)\]","<a href='history.php?id=\\1'>T.\\1</a>",$string);
  
  // url reference
  $string = ereg_replace("\[([a-z]+)://([^]]*),([^]]*)\]","<a href='\\1://\\2'>\\3</a>",$string);
  $string = ereg_replace("\[([a-z]+)://([^]]*)\]","<a href='\\1://\\2'>\\2</a>",$string);

  // history reference 
  if(isset($_GET['id']))
    $string = ereg_replace("\[([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2})\]","<a href='#\\1'>\\1</a>",$string);
  else
    $string = ereg_replace("\[([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2})\]","\\1",$string);

  return $string;
}



?>
