<?php

class Logger {
    private static $instance = null;
    private const LOG_LEVELS = ['DEBUG', 'INFO', 'WARNING', 'ERROR', 'CRITICAL'];
    private const LOG_DIR = __DIR__ . '/../logs';
    
    private function __construct() {
        $this->ensureLogDirectory();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Log a message with context
     */
    public function log($level, $message, array $context = []) {
        if (!in_array($level, self::LOG_LEVELS)) {
            throw new Exception('Invalid log level');
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logFile = self::LOG_DIR . '/' . date('Y-m-d') . '.log';
        
        // Format context data
        $contextStr = empty($context) ? '' : ' ' . json_encode($context);
        
        // Format log message
        $logMessage = "[{$timestamp}] [{$level}] {$message}{$contextStr}" . PHP_EOL;
        
        // Write to log file
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    public function debug($message, array $context = []) {
        $this->log('DEBUG', $message, $context);
    }
    
    public function info($message, array $context = []) {
        $this->log('INFO', $message, $context);
    }
    
    public function warning($message, array $context = []) {
        $this->log('WARNING', $message, $context);
    }
    
    public function error($message, array $context = []) {
        $this->log('ERROR', $message, $context);
    }
    
    public function critical($message, array $context = []) {
        $this->log('CRITICAL', $message, $context);
    }
    
    private function ensureLogDirectory() {
        if (!file_exists(self::LOG_DIR)) {
            mkdir(self::LOG_DIR, 0755, true);
        }
    }
}