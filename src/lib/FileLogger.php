<?php

namespace App\Library;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class FileLogger implements LoggerInterface
{
    public function emergency($message, array $context = []): void
    {
        // Use the level from LogLevel class
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        // Current date in 1970-12-01 23:59:59 format
        $dateFormatted = date('Y-m-d H:i:s');

        // Build the message with the current date, log level,
        // and the string from the arguments
        $contextString = json_encode($context);
        $message = sprintf(
            '[%s] %s: %s %s%s',
            $dateFormatted,
            $level,
            $message,
            $contextString,
            PHP_EOL // Line break
        );

        // Writing to the file
        $logDir = './logs';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $logFileName = date('Y-m-d').'.log';
        $logFile = $logDir.'/'.$logFileName;
        file_put_contents($logFile, $message, FILE_APPEND);
        // FILE_APPEND flag prevents flushing the file content on each call
        // and simply adds a new string to it
    }
}