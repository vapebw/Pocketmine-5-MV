<?php
@unlink("PocketMine-MP.phar");

$phar = new Phar("PocketMine-MP.phar");

$phar->startBuffering();
$phar->buildFromDirectory(__DIR__);
$phar->setStub($phar->createDefaultStub("src/PocketMine.php"));
$phar->stopBuffering();

echo "PHAR creado correctamente\n";