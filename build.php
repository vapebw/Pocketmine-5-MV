<?php

$outFileName = "PocketMine-MP.phar";
if (file_exists($outFileName)) {
    @unlink($outFileName);
}

$phar = new Phar($outFileName);
$phar->setSignatureAlgorithm(Phar::SHA1);
$phar->startBuffering();

$directory = __DIR__;
$excludedFolders = [".git", ".github", ".idea"];
$excludedFiles = [basename(__FILE__), $outFileName];

$iterator = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
$filter = new RecursiveCallbackFilterIterator($iterator, function (SplFileInfo $current, $key, $iterator) use ($excludedFolders, $excludedFiles, $directory) {
    $relativePath = ltrim(substr($current->getPathname(), strlen($directory)), DIRECTORY_SEPARATOR);

    foreach ($excludedFolders as $folder) {
        if (strpos($relativePath, $folder) === 0) {
            return false;
        }
    }

    if (in_array($current->getFilename(), $excludedFiles, true)) {
        return false;
    }

    return true;
});

$phar->buildFromIterator(new RecursiveIteratorIterator($filter), $directory);
$phar->setStub($phar->createDefaultStub("src/PocketMine.php"));
$phar->stopBuffering();

echo "PocketMine-MP.phar built successfully (excluded metadata)\n";

// js a .php so you can easily build the Pocketmine phar, on a cmd in you directory where the pocketmine is use php build.php and its all!
// also i recommend to use "php -d phar.readonly=0 build.php" to build the phar.