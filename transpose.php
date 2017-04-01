<?php
require_once("../smarty/setup.php");
require_once("chordize.class.php");
extract($_POST);
extract($_GET);

$keys = array("A","A#","Bb","B","C","C#","Db","D","D#","Eb","E","F","F#","Gb","G","G#","Ab");

if(!empty($command)) {
  $key_from = new Key($from_key);
  $key_to = new Key($to_key);
  $transpose = new Transpose($key_from,$key_to);
  $transposed = $transpose->get_transposed_scale();
  $mcholm->assign('transposed',$transposed);
  $mcholm->assign('from_key',$from_key);
  $mcholm->assign('to_key',$to_key);
} else {
  
}

$mcholm->assign('keys',$keys);

$mcholm->display("transpose.tpl");

?>
