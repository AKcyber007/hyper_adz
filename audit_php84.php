<?php

$lockFile = __DIR__ . '/composer.lock';
if (!file_exists($lockFile)) {
    die("composer.lock not found.\n");
}

$data = json_decode(file_get_contents($lockFile), true);
$packages = array_merge($data['packages'] ?? [], $data['packages-dev'] ?? []);

$needs84 = [];

foreach ($packages as $pkg) {
    if (isset($pkg['require']['php'])) {
        $req = $pkg['require']['php'];
        // Check if the constraint explicitly demands PHP >= 8.4 or ^8.4 or ~8.4
        if (strpos($req, '8.4') !== false) {
            $needs84[] = [
                'name' => $pkg['name'],
                'version' => $pkg['version'],
                'php_constraint' => $req
            ];
        }
    }
}

echo "Dependencies explicitly mentioning PHP 8.4:\n";
if (empty($needs84)) {
    echo "None.\n";
} else {
    foreach ($needs84 as $n) {
        echo "- {$n['name']} ({$n['version']}) requires PHP {$n['php_constraint']}\n";
    }
}

$composerJson = json_decode(file_get_contents(__DIR__ . '/composer.json'), true);
echo "\nProject PHP Requirement:\n";
echo $composerJson['require']['php'] ?? 'Not specified';
echo "\n";
