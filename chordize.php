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
  $chords = new Chordize($transpose);
  $original = stripslashes($original);
  $lines = split("\n",$original);
  
  $data = array();
  foreach($lines as $line) {
    if($chords->isChords($line)) {
      $line = $chords->transposeChords($line);
    }
    $data[] = $line;
  }
  if($command == 'ajax') {
    foreach($data as $line) {
      echo $line . "\n";
    }
    exit;
  } else {
    $mcholm->assign('from_key',$from_key);
    $mcholm->assign('to_key',$to_key);
    $mcholm->assign('original',$original);
    $mcholm->assign('data',$data);
  }
} else {
  $mcholm->assign('from_key',"G");
}

$mcholm->assign('keys',$keys);
$mcholm->display("chordize.tpl");
?>
