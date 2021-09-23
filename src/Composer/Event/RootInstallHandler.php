<?php

namespace Jascha030\Xerox\Composer\Event;

use Composer\Script\Event;

final class RootInstallHandler
{
    private const SALTS_URL   = "https://api.wordpress.org/secret-key/1.1/salt";
    private const CONST_REGEX = "/define\('([A-Z_]*)',[ \t]*'(.*)'\);/";
    private const TEMPLATE    = "#{{SALTS}}#";

    public static function postInstall(Event $event): void
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $root      = dirname($vendorDir, 2);
        $env       = $root . '/.env.example';

        if (file_exists($env)) {
            self::generateWordpressSalts($env);
        }
    }

    private static function generateWordpressSalts(string $envPath): void
    {
        $resource = curl_init();
        curl_setopt($resource, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($resource, CURLOPT_URL, self::SALTS_URL);

        $data = curl_exec($resource);

        curl_close($resource);

        preg_match_all(self::CONST_REGEX, $data, $matches);

        if (isset($matches[1], $matches[2])) {
            $salts = array_combine($matches[1], $matches[2]);

            if (file_exists($envPath)) {
                $content = file_get_contents($envPath);

                ob_start();

                foreach ($salts as $key => $value) {
                    echo $key . "='{$value}'" . PHP_EOL;
                }

                file_put_contents($envPath,
                    str_replace(self::TEMPLATE,
                        ob_get_clean(),
                        $content));
            }
        }
    }
}
