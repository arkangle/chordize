<?php
class Key {
  var $_notes = array("A","A#|Bb","B","C","C#|Db","D","D#|Eb","E","F","F#|Gb","G","G#|Ab");
  var $_sharps = array("G","D","A","E","B","F#","C#","G#","D#","A#");
  var $_scales = array(0,2,4,5,7,9,11);
  function Key($key) {
    $this->isSharp = $this->isFlat = false;
    $this->key = $key;
    $this->_count_notes = count($this->_notes);
    $this->_set_type();
    $this->_set_key_index();
    $this->_set_scale();
  }
  function _set_type() {
    if(in_array($this->key,$this->_sharps)) {
      $this->isSharp = true;
    } elseif($this->key != "C") {
      $this->isFlat = true;
    }
  }
  function _set_key_index() {
    foreach($this->_notes as $i => $note) {
      if($this->_is_match($note,$this->key)) {
        $this->key_index = $i;
        break;
      }
    }
  }
  function _set_scale() {
    $scale = array_slice($this->_notes,$this->key_index);
    if($this->key_index > 0) {
      $scale = array_merge($scale,array_slice($this->_notes,0,$this->key_index));
    }
    $this->scale = $scale;
  }
  function _is_match($both_notes,$note) {
    $n = split("\|",$both_notes);
    return in_array($note,$n);
  }
  function _which_note($note) {
    $n = split("\|",$note);
    if(count($n) == 1 || $this->isSharp) {
      $return = $n[0];
    } else {
      $return = $n[1];
    }
    return $return;
  }
  function get_note($index) {
    $note = $this->scale[$index];
    return $this->_which_note($note);
  }
  function get_index($note) {
    $return = false;
    for($i = 0;$i<$this->_count_notes;$i++) {
      if($this->_is_match($this->scale[$i],$note)) {
        $return = $i;
        break;
      }
    }
    return $return;
  }
  function get_scale() {
    $return = array();
    foreach($this->_scales as $index) {
      $return[] = $this->get_note($index);
    }
    return $return;
  }
  function get_full_scale() {
    return $this->scale;
  }
}

class Transpose {
  function Transpose($from_Key,$to_Key) {
    $this->from_key = $from_Key;
    $this->to_key = $to_Key;
    $this->_set_transposed_scale();
  }
  function _set_transposed_scale() {
    $from_scale = $this->from_key->get_full_scale();
    $transposed = array();
    foreach($from_scale as $i => $note) {
      $to_note = $this->to_key->get_note($i);
      $n = split("\|",$note);
      foreach($n as $from_note) {
        $transposed[$from_note] = $to_note;
      }
    }
    $this->transposed = $transposed;
  }
  function get_transposed_scale() {
    return $this->transposed;
  }
  function get_transposed($from_note) {
    return $this->transposed[$from_note];
  }
}

class Chordize {
  var $_suffix = array("sus","m","M","dim","\\+","");
  function Chordize($Transpose) {
    $this->transpose_scale = $Transpose->get_transposed_scale();
  }
  function isChords($line) {
    $line .= " ";
    $split = preg_split(".[\s/]+.",$line,-1,PREG_SPLIT_NO_EMPTY);
    if(count($split) == 0) {
      $return = false;
    } else {
      $return = true;
      $suffix = join("|",$this->_suffix);
      foreach($split as $word) {
        if(preg_match("/^[ABCDEFG]{1}[#b]{0,1}[0-9]*($suffix)[0-9]*$/",$word) == 0) {
          $return = false;
          break;
        }
      }
    }
    return $return;
  }
  function transposeChords($line) {
    $line .= " ";
    $suffix = join("|",$this->_suffix);
    $return = preg_replace_callback("@([ABCDEFG]{1}[#b]{0,1})([0-9]*)($suffix)([0-9]*)(.{1})@",
                                    array('self','transpose_callback'),
                                    $line);
    return $return;
  }
  function transpose_callback($matches) {
    $orig = $matches[1];
    $trans = $this->transpose_scale[$orig];
    $diff = strlen($orig) - strlen($trans);
    $return = $trans . $matches[2]  . $matches[3]  . $matches[4];

    if($matches[5] == "\n" || $matches[5] == "\r") {
      $return .= $matches[5];
    }elseif($diff == 1) {
      $return .= $matches[5] . " ";
    } elseif($diff == 0) {
      $return .= $matches[5];  
    }
    return $return;
  }
}
?>
