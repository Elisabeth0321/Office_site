<?php
namespace App\Config;

class MailConfigLoader
{
    public static function load(): array
    {
        $config = parse_ini_file(__DIR__ . '/mail_config.ini');
        if (!$config) {
            throw new \RuntimeException("Unable to load mail_config.ini");
        }
        return $config;
    }
}
