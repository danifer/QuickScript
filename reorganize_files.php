<?php

$it = new RecursiveDirectoryIterator($argv[1], RecursiveDirectoryIterator::SKIP_DOTS);

$resultsArr = [ ];
$filesArr = [ ];

// Extract Invoice Numbers from .pdf Files
foreach(new RecursiveIteratorIterator($it) as $file) {

    preg_match('/ [0-9]+.pdf/', $file, $output);

    if (isset($output[0])) {
        $resultsArr[] = [
            'file' => $file->getPathname(),
            'invoiceNumber' => str_replace('.pdf', '', $output[0])
        ];
        
        // Add it to the filesArr so we ignore it later.
        $filesArr[] = $file->getPathname();
    }

}

foreach(new RecursiveIteratorIterator($it) as $file) {

    if (in_array($file->getPathname(), $filesArr)) {
        continue;
    }

    //print_r($file->getPathname()."\r\n");
}

print_r($resultsArr);

//_dark wolf construction (December 14 2018)Invoice 18298.pdf
