<?php

$array = [
    "appel" => "sap",
    "ice" => "tea",
    "coca" => "cola",
    "blik" => "bier",
];


function array_neighbor($arr, $key)
{
    krsort($arr);
    $keys = array_keys($arr);
    $keyIndexes = array_flip($keys);
   
    $return = array();
    if (isset($keys[$keyIndexes[$key]-1]))
        $return[] = $keys[$keyIndexes[$key]-1];
    if (isset($keys[$keyIndexes[$key]+1]))
        $return[] = $keys[$keyIndexes[$key]+1];

    return $return;
}
$var = array_neighbor($array, "coca");
var_dump($var);

?>