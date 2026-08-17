<?php
$file = 'd:/UI design/resources/views/partner/campaign-activity.blade.php';
$content = file_get_contents($file);

// Replace count with location names
$content = str_replace(
    '{{ $campaign->locations->whereIn(\'id\', $locationIds)->count() }} of your location(s)',
    '{{ $campaign->locations->whereIn(\'id\', $locationIds)->pluck(\'name\')->implode(\', \') }}',
    $content
);

// Replace invalid characters with -
$content = preg_replace('/[^\x20-\x7E\t\r\n]/', '-', $content);
$content = str_replace('--', '-', $content);

file_put_contents($file, $content);
echo "Fixed.\n";
