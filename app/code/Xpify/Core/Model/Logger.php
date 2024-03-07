<?php
declare(strict_types=1);

namespace Xpify\Core\Model;

use Zend_Log;

final class Logger
{
    protected static array $loggers = [];

    /**
     * Get logger with custom log file
     *
     * @param string $file
     * @return ?Zend_Log
     */
    public static function getLogger(string $file): ?Zend_Log
    {
        try {
            if (empty(Logger::$loggers[$file])) {
                $writer = new \Zend_Log_Writer_Stream(BP . "/var/log/$file");
                $logger = new Zend_Log();
                $logger->addWriter($writer);
                Logger::$loggers[$file] = $logger;
            }
            return Logger::$loggers[$file];
        } catch (\Throwable $e) {
            return null;
        }
    }

}
