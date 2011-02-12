<?php
//TinCan interpereter v1.0
$stack = array();
$vars = array();
$list = array_fill(0, 90, 0);
echo "Enter filename to interperet> ";
$filename = trim(fgets(STDIN));

//Check for file extension, if it does not exist, add it
if(!strpos($filename, '.can')){
	$filename.='.can';
}
//Read file into array
$instructionarray = file($filename, FILE_SKIP_EMPTY_LINES|FILE_IGNORE_NEW_LINES);

//Strip all non 40-length lines and lines not surrounded by hashes
foreach($instructionarray as $key => &$instruction){
	if(strlen($instruction) != 40){
		unset($instructionarray[$key]);
	}
	if((substr($instruction, 0, 1) != '#') or (substr($instruction, -1, 1) != '#')){
		unset($instructionarray[$key]);
	}
	$instruction = str_replace('#', '', $instruction);
	$instruction = str_replace(' ', '', $instruction);
}

//Check to make sure there are still instructions left to run
if(count($instructionarray) == 0){echo "\r\n\r\nERROR: You seem to be too stupid to include a single valid instruction."; die;}
$instructionarray = array_values($instructionarray);

//Execute program
$i = 0;
$j = 0;
while($i < count($instructionarray)){
	$line = $instructionarray[$i];
	$args = explode(',', $line);
	if(count($args) != 3){echo "\r\n\r\nERROR: Line $i has the wrong number of arguments."; die;}
	
	foreach($args as $key => $arg){
		$vars[$key] = $arg;
		if(!is_numeric($arg)){
			$ord = ord($arg);
			if($arg == '$'){
				$vars[$key] = $i;
			}
			elseif($arg == '@'){
				$vars[$key] = $j;
			}
			elseif($arg == '&'){
				$vars[$key] = $i+1;
			}
			elseif(($ord >= 65) and ($ord <= 90)){
				$vars[$key] = '%'.$ord;
			}
			else{echo "\r\n\r\nERROR: Argument $key on line $i is not numeric."; die;}
		}
	}
	
	if(substr($vars[0], 0, 1) == '%'){
		$vars[0] = $list[substr($vars[0], 1)];
	}
	if(substr($vars[1], 0, 1) == '%'){
		$store = substr($vars[1], 1);
		$vars[1] = $list[$store];
	}
	else{echo "\r\n\r\nERROR: Argument $key on line $i is not a storable location."; die;}
	if(substr($vars[2], 0, 1) == '%'){
		$vars[2] = $list[substr($vars[2], 1)];
	}
	$list[$store] = $vars[1] - $vars[0];
	if($list[$store] <= 0){
		if($vars[2] > -1){
			$i = $vars[2]-1;
		}
	}
	if($vars[2] == -1){
		array_push($stack, $list[$store]);
	}
	
	$i++;
	$j++;
}
foreach($stack as $value){
	echo chr($value);
}