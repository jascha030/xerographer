<?php

declare(strict_types=1);

$autoloader = static function () {
    $locations = [
        dirname(__FILE__, 2) . '/vendor/autoload.php',
        dirname(__FILE__, 4) .  '/autoload.php',
        getenv('HOME') . '/.composer/vendor/autoload.php',
    ];

    foreach ($locations as $autoloaderPath) {
        if (is_file($autoloaderPath)) {
            return $autoloaderPath;
        }
    }

    $errorMsg = sprintf(
        'Couldn\'t find Composer\'s Autoloader file in any of the following paths: 
                %s, please make sure you run the %s or %s commands.',
        implode(', ', $locations),
        '<pre>composer install --prefer-source</pre>',
        '<pre>composer dump-autoload</pre>'
    );

    throw new \RuntimeException($errorMsg);
};

require_once $autoloader();
