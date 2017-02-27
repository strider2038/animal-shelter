#!/usr/bin/env php
<?php

require_once 'Shelter.php';
require_once 'Animal.php';
require_once 'Cat.php';
require_once 'Dog.php';
require_once 'Turtle.php';

if ($argc <= 1) {
    echo "php sh init - inits shelter" . PHP_EOL;
    echo "php sh list - show all animals in shelter" . PHP_EOL;
    echo "php sh place <type> <name> <birthdate> - creates and places new animal to shelter" . PHP_EOL;
    echo "php sh transfer [<type>] - transfers oldest animal of any or defined type from the shelter" . PHP_EOL;
    exit;
}

$shelter = new Shelter();
$shelter->load();

switch ($argv[1]) {
    
    case 'init':
        $names = ['Dolly', 'Molly', 'Neon', 'Mustang', 'Ferrari', 'Einstein'];
        $types = ['Cat', 'Dog', 'Turtle'];
        for ($i = 0; $i < 10; $i++) {
            $class = $types[rand(0, count($types)-1)];
            $animal = new $class(
                $names[rand(0, count($names)-1)],
                date('Y-m-d', strtotime('-' . rand(1, 10000) . ' days'))
            );
            $shelter->place($animal);
            unset($animal);
        }
        $shelter->save();
        break;
        
    case 'list':
        foreach ($shelter->getList() as $animal) {
            echo "{$animal->name} - {$animal->type} - {$animal->birthdate}" . PHP_EOL;
        }
        break;
        
    case 'place':
        if ($argc < 4) {
            echo 'not enough arguments' . PHP_EOL;
            break;
        }
        $className = ucfirst(strtolower($argv[2]));
        if (!class_exists($className)) {
            echo "unknown animal type {$className}" . PHP_EOL;
            break;
        }
        $shelter->place(new $className($argv[3], $argv[4]))->save();
        break;
        
    case 'transfer':
        $transfered = $shelter->transfer(!empty($argv[2]) ? $argv[2] : null);
        if ($transfered === null) {
            echo "no matching animals found" . PHP_EOL;
            break;
        }
        $shelter->save();
        echo "{$transfered->name} - {$transfered->type} - {$transfered->birthdate}" . PHP_EOL;
        break;
        
    default:
        echo "unknown command" . PHP_EOL;
}