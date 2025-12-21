<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Cron Controller
 * Handles scheduled tasks like automatic database backups
 * 
 * Usage: php index.php cron auto_backup
 */
class Cron extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Only allow CLI execution
        if (!$this->input->is_cli_request()) {
            show_error('This script can only be accessed via the command line.');
        }
        
        $this->load->database();
        $this->load->helper('file');
    }

    /**
     * Automatic Database Backup
     * Creates a backup if conditions are met
     */
    public function auto_backup()
    {
        echo "=== Automatic Backup Process Started ===\n";
        echo "Time: " . date('Y-m-d H:i:s') . "\n\n";
        
        // Check if auto backup is enabled
        $enabled = config('AUTO_BACKUP_ENABLED');
        if ($enabled !== 'Si' && $enabled !== '1') {
            echo "❌ Auto backup is disabled\n";
            echo "Enable it in: Admin → Configuration → System\n";
            return;
        }
        
        echo "✓ Auto backup is enabled\n";
        
        // Get configuration
        $frequency = config('AUTO_BACKUP_FREQUENCY') ?: 'daily';
        $retention = (int) config('AUTO_BACKUP_RETENTION') ?: 7;
        $last_backup = config('LAST_AUTO_BACKUP');
        
        echo "Frequency: $frequency\n";
        echo "Retention: $retention backups\n";
        echo "Last backup: " . ($last_backup ?: 'Never') . "\n\n";
        
        // Check if backup is needed
        if (!$this->should_create_backup($last_backup, $frequency)) {
            echo "⏭️  Backup not needed yet\n";
            return;
        }
        
        echo "📦 Creating backup...\n";
        
        try {
            // Create backup directory if needed
            $backup_dir = './backups/database/';
            if (!file_exists($backup_dir)) {
                mkdir($backup_dir, 0777, true);
                chmod($backup_dir, 0777);
            }
            
            // Load DB utility
            $this->load->dbutil();
            $backup = $this->dbutil->backup();
            
            // Generate filename
            $filename = $backup_dir . 'auto_' . date('YmdHis') . '.gz';
            
            // Write backup file
            if (write_file($filename, $backup)) {
                $filesize = filesize($filename);
                echo "✓ Backup created successfully\n";
                echo "  File: " . basename($filename) . "\n";
                echo "  Size: " . $this->format_bytes($filesize) . "\n\n";
                
                // Update last backup time
                $this->update_config('LAST_AUTO_BACKUP', date('Y-m-d H:i:s'));
                
                // Log the backup
                system_logger('config', 'Backup automático creado', [
                    'filename' => basename($filename),
                    'size' => $filesize,
                    'frequency' => $frequency
                ]);
                
                // Clean old backups
                $this->cleanup_old_backups($retention);
                
            } else {
                echo "❌ Failed to write backup file\n";
                system_logger('error', 'Error al crear backup automático', [
                    'filename' => basename($filename)
                ]);
            }
            
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            system_logger('error', 'Error en backup automático', [
                'error' => $e->getMessage()
            ]);
        }
        
        echo "\n=== Backup Process Completed ===\n";
    }
    
    /**
     * Determine if a backup should be created based on frequency
     */
    private function should_create_backup($last_backup, $frequency)
    {
        if (empty($last_backup)) {
            return true; // First backup
        }
        
        $last_time = strtotime($last_backup);
        $now = time();
        $diff = $now - $last_time;
        
        switch ($frequency) {
            case 'hourly':
                return $diff >= 3600; // 1 hour
            case 'daily':
                return $diff >= 86400; // 24 hours
            case 'weekly':
                return $diff >= 604800; // 7 days
            case 'monthly':
                return $diff >= 2592000; // 30 days
            default:
                return $diff >= 86400; // Default to daily
        }
    }
    
    /**
     * Clean up old backups keeping only the specified number
     */
    private function cleanup_old_backups($retention)
    {
        echo "🧹 Cleaning old backups...\n";
        
        $backup_dir = './backups/database/';
        $files = glob($backup_dir . 'auto_*.gz');
        
        if (count($files) <= $retention) {
            echo "  No cleanup needed (" . count($files) . " backups)\n";
            return;
        }
        
        // Sort by modification time (oldest first)
        usort($files, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        
        // Delete oldest files
        $to_delete = count($files) - $retention;
        $deleted = 0;
        
        for ($i = 0; $i < $to_delete; $i++) {
            if (unlink($files[$i])) {
                echo "  ✓ Deleted: " . basename($files[$i]) . "\n";
                $deleted++;
            }
        }
        
        echo "  Removed $deleted old backup(s)\n";
        echo "  Kept $retention most recent backup(s)\n";
    }
    
    /**
     * Update a configuration value
     */
    private function update_config($name, $value)
    {
        $this->db->where('config_name', $name);
        $this->db->update('site_config', [
            'config_value' => $value,
            'date_update' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Format bytes to human readable
     */
    private function format_bytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    /**
     * Test method to verify cron is working
     */
    public function test()
    {
        echo "✓ Cron controller is working!\n";
        echo "Current time: " . date('Y-m-d H:i:s') . "\n";
        echo "PHP version: " . PHP_VERSION . "\n";
    }
}
