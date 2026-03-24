<?php
@unlink("PocketMine-MP.phar");

$phar = new Phar("PocketMine-MP.phar");

$phar->startBuffering();
$phar->buildFromDirectory(__DIR__);
$phar->setStub($phar->createDefaultStub("src/PocketMine.php"));
$phar->stopBuffering();

echo "PocketmineMP.phar build successfully\n";

// js a .php so you can easily build the Pocketmine phar, on a cmd in you directory where the pocketmine is use php build.php and its all!
