<?php
namespace App\Utils;

/**
 * Logger Utility
 * Beheert logging functionaliteit
 */
class Logger {
    private $logPath;
    private $initialized = false;
    
    public function __construct($logFile = 'activity.log') {
        $this->logPath = __DIR__ . '/../../logs/' . $logFile;
        $this->initializeLogger();
    }
    
    /**
     * Initialiseer de logger en maak logs directory indien nodig
     */
    private function initializeLogger() {
        if ($this->initialized) {
            return;
        }

        $logDir = dirname($this->logPath);
        
        // Probeer directory aan te maken als die niet bestaat
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        
        // Check of we kunnen schrijven
        if (is_writable($logDir) || is_writable($this->logPath)) {
            $this->initialized = true;
        }
    }
    
    /**
     * Log een activiteit
     * @param string $action De uitgevoerde actie
     * @param string $user De gebruiker die de actie uitvoerde
     * @param string $details Extra details over de actie
     * @return bool Success status
     */
    public function logActivity($action, $user, $details = '') {
        if (!$this->initialized) {
            return false;
        }

        try {
            $timestamp = date('Y-m-d H:i:s');
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $logEntry = "$timestamp | $action | $user | $ip | $details\n";
            
            return @file_put_contents($this->logPath, $logEntry, FILE_APPEND) !== false;
        } catch (\Exception $e) {
            // Slik de error stil in
            return false;
        }
    }
    
    /**
     * Log een error
     * @param string $error De error message
     * @param string $user De betrokken gebruiker
     * @param array $context Extra context informatie
     * @return bool Success status
     */
    public function logError($error, $user, $context = []) {
        if (!$this->initialized) {
            return false;
        }

        try {
            $contextStr = !empty($context) ? json_encode($context) : '';
            return $this->logActivity('ERROR', $user, "$error | $contextStr");
        } catch (\Exception $e) {
            // Slik de error stil in
            return false;
        }
    }
} 