<?php

/* Fonction permettant de trier les champs de la collection mongo */
function sortByKeyList($array,$seq){
    $ret=array();
    if(empty($array) || empty($seq)) return false;
    foreach($seq as $key){$ret[$key]=$array[$key];}
    return $ret;
}

function display_array ($val) {
       if (is_array($val)) {
               if (count($val) > 1) {
                       $val_display = "<li>".implode('</li><li>', $val)."</li>";
               } else {
                       $val_display = implode(" ",$val);
                       if (empty($val_display)) { $val_display = '-'; }
               }
       } else {
               $val_display = "<td>".$val."</td>";
               if (empty($val)) { $val = '-'; }
       }
       return $val_display;
}

?>
