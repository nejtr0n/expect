<?php
/**
 * Created by PhpStorm.
 * User: a6y
 * Date: 18.09.15
 * Time: 10:57
 */
require_once 'vendor/autoload.php';

$tshell = new \Expect\Shell();
$cmd = new \Expect\Command("pwd\n");
$cmd->setNext(
    new \Expect\Command("ls -la\n")
);

$tshell->exec(
    $cmd
);
var_dump($tshell);