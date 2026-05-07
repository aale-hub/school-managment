<?php

declare(strict_types=1);

namespace School\Infrastructure\Persistence\SQLite;

use PDO;

class Connection
{
    private static ?PDO $instance = null;

    public static function get(): PDO
    {
        if (self::$instance === null) {
            $path = __DIR__ . '/../../../../database/school.sqlite';
            $dir  = dirname($path);

            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            self::$instance = new PDO('sqlite:' . $path);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$instance->exec('PRAGMA foreign_keys = ON;');
        }

        return self::$instance;
    }
}
