<?php

include '../../../../src/Dlin/Getopt/getopt_inc.php';


error_reporting(E_ALL);
ini_set('display_errors', 1);
$option1 =
    array(
        'arg'=>'s',
        'alias' => 'size',
        'help'=>'Number of randomly generated item',
        'required'=>true,
        'pattern'=>'/\d+/',
        'patternMsg'=>'size must be a number',
       // 'requiredMsg'=>'size is required'
    );
$option2 =array(
        'arg'=>'t',
        'alias'=>'type',
        'help' => 'Type of items to be generated',
        'required'=>true,
        'prompt'=>true,
        'pattern'=>'/(int|string)/',
        'patternMsg'=>'type must be either "int" or "string"',
        'requiredMsg'=>'type is required'

);

$go = new Getopt();

$go->setUsage('php $0 -s [num] -t [int|string]');
$go->setOption($option1)->setOption($option2)->parse();


$size = intval($go->s);

$result = array();
for( $i = 0 ; $i < $size; $i++){
    $rand = rand(1,100000);
    if($go->t == 'string'){
        $rand = strval($rand).'s';
    }
    $result[] = $rand;
}

print_r($result);
