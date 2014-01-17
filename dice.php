<?php

$aDice[0] = array("T", "O", "E", "S", "S", "I");
$aDice[1] = array("A", "S", "P", "F", "F", "K");
$aDice[2] = array("N", "U", "I", "H", "M", "Qu");
$aDice[3] = array("O", "B", "J", "O", "A", "B");
$aDice[4] = array("L", "N", "H", "N", "R", "Z");
$aDice[5] = array("A", "H", "S", "P", "C", "O");
$aDice[6] = array("R", "Y", "V", "D", "E", "L");
$aDice[7] = array("I", "O", "T", "M", "U", "C");
$aDice[8] = array("L", "R", "E", "I", "X", "D");
$aDice[9] = array("T", "E", "R", "W", "H", "V");
$aDice[10] = array("T", "S", "T", "I", "Y", "D");
$aDice[11] = array("W", "N", "G", "E", "E", "H");
$aDice[12] = array("E", "R", "T", "T", "Y", "L");
$aDice[13] = array("O", "W", "T", "O", "A", "T");
$aDice[14] = array("A", "E", "A", "N", "E", "G");
$aDice[15] = array("E", "I", "U", "N", "E", "S");

for($i = 0; $i <= 15; $i++)
{
	$rand = mt_rand(0, count($aDice[$i])-1);
	echo $aDice[$i][$rand];
}

