<?php
require_once("../smarty/setup.php");
require_once("chordize.class.php");
extract($_POST);
extract($_GET);

$keys = array("A","A#","Bb","B","C","C#","Db","D","D#","Eb","E","F","F#","Gb","G","G#","Ab");

if(!empty($command)) {
  $key_from = new Key($from_key);
  $key_to = new Key($to_key);
  $from_scale = $key_from->get_scale();
  $to_scale = $key_to->get_scale();
  $mcholm->assign('from_scale',$from_scale);
  $mcholm->assign('to_scale',$to_scale);
  $mcholm->assign('from_key',$from_key);
  $mcholm->assign('to_key',$to_key);
} else {
  
}

$mcholm->assign('keys',$keys);
$mcholm->display("scales.tpl");

?>
