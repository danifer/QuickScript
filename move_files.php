<?php

$it = new RecursiveDirectoryIterator($argv[1], RecursiveDirectoryIterator::SKIP_DOTS);

$i=0;
foreach(new RecursiveIteratorIterator($it) as $file) {

    $new = '/target/'.$file->getBasename();

    copy($file->getPathname(), $new);
    $i++;
}
