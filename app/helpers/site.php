<?php
function getSiteSettings() {
    $configFile = __DIR__ . '/../../config/site.json';
    $defaults = [
        'site_name' => 'Araska.id',
        'site_description' => 'Dokumen dan informasi terkini',
        'site_favicon' => '/favicon.ico'
    ];
    
    if (file_exists($configFile)) {
        $config = json_decode(file_get_contents($configFile), true) ?? [];
        return array_merge($defaults, $config);
    }
    
    return $defaults;
}
?>
