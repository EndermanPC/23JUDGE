<?php
$rootDirectory = 'submissions';
$logFile = 'submissions/Log.txt';

function cleanOldFiles($directory, $logFile) {
    $oneWeekAgo = time() - (7 * 24 * 60 * 60);

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'html') {
            $filename = $file->getBasename('.html');
            $dateString = substr($filename, 0, 19);
            $dateString = str_replace(' ', '-', $dateString);

            $dateTime = DateTime::createFromFormat('s-i-H-d-m-Y', $dateString);
            
            if ($dateTime !== false) {
                $fileTime = $dateTime->getTimestamp();

                if ($fileTime < $oneWeekAgo) {
                    unlink($file->getPathname()); 

                    $logContent = file_get_contents($logFile);
                    $logContent = preg_replace('/^.*' . preg_quote($filename, '/') . '.*$\n?/m', '', $logContent);
                    file_put_contents($logFile, $logContent);
                }
            }
        }
    }
}

cleanOldFiles($rootDirectory, $logFile);
?>