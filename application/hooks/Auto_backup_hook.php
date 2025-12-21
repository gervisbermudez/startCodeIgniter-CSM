<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Auto Backup Hook
 * This hook runs on every admin page load to check if a backup is needed
 * It acts as a "pseudo-cron" for servers without cron job access
 */

class Auto_backup_hook
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * Check and execute automatic backup if needed
     * This runs silently in the background without affecting page load
     */
    public function check_and_backup()
    {
        // Only run for admin users
        if (!$this->is_admin_area()) {
            return;
        }

        // Only run occasionally to avoid performance impact
        if (!$this->should_check_now()) {
            return;
        }

        // Check if auto backup is enabled
        $enabled = config('AUTO_BACKUP_ENABLED');
        if ($enabled !== 'Si' && $enabled !== '1') {
            return;
        }

        // Get configuration
        $frequency = config('AUTO_BACKUP_FREQUENCY') ?: 'daily';
        $last_backup = config('LAST_AUTO_BACKUP');

        // Check if backup is needed
        if (!$this->is_backup_needed($last_backup, $frequency)) {
            return;
        }

        // Execute backup in background (non-blocking)
        $this->execute_background_backup();
    }

    /**
     * Check if we're in admin area
     */
    private function is_admin_area()
    {
        $uri = $this->CI->uri->segment(1);
        return $uri === 'admin';
    }

    /**
     * Determine if we should check for backup now
     * Uses random probability to avoid checking on every request
     */
    private function should_check_now()
    {
        // Check only 10% of the time to reduce overhead
        return (rand(1, 10) === 1);
    }

    /**
     * Determine if a backup is needed based on frequency
     */
    private function is_backup_needed($last_backup, $frequency)
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
     * Execute backup in background without blocking the request
     */
    private function execute_background_backup()
    {
        // For CLI environments, we can use exec
        if (function_exists('exec') && !$this->CI->input->is_cli_request()) {
            $command = 'php ' . FCPATH . 'index.php cron auto_backup > /dev/null 2>&1 &';
            @exec($command);
            return;
        }

        // Fallback: Execute inline but quickly
        $this->quick_backup();
    }

    /**
     * Quick inline backup (fallback method)
     */
    private function quick_backup()
    {
        try {
            // Load required libraries
            $this->CI->load->dbutil();
            $this->CI->load->helper('file');

            // Define backup directory
            $backup_dir = './backups/database/';

            // Ensure directory exists
            if (!file_exists($backup_dir)) {
                @mkdir($backup_dir, 0777, true);
                @chmod($backup_dir, 0777);
            }

            // Check if writable
            if (!is_writable($backup_dir)) {
                return;
            }

            // Create backup
            $backup = $this->CI->dbutil->backup();
            $filename = $backup_dir . 'auto_' . date('YmdHis') . '.gz';

            if (write_file($filename, $backup)) {
                // Update last backup time
                $this->CI->db->where('config_name', 'LAST_AUTO_BACKUP');
                $this->CI->db->update('site_config', [
                    'config_value' => date('Y-m-d H:i:s'),
                    'date_update' => date('Y-m-d H:i:s')
                ]);

                // Log the backup
                system_logger('config', 'Backup automático creado (pseudo-cron)', [
                    'filename' => basename($filename),
                    'size' => filesize($filename),
                    'method' => 'hook'
                ]);

                // Clean old backups
                $this->cleanup_old_backups();
            }
        } catch (Exception $e) {
            // Silently fail - don't break the page
            system_logger('error', 'Error en backup automático (hook)', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Clean up old backups
     */
    private function cleanup_old_backups()
    {
        $retention = (int) config('AUTO_BACKUP_RETENTION') ?: 7;
        $backup_dir = './backups/database/';
        $files = glob($backup_dir . 'auto_*.gz');

        if (count($files) <= $retention) {
            return;
        }

        // Sort by modification time (oldest first)
        usort($files, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });

        // Delete oldest files
        $to_delete = count($files) - $retention;
        for ($i = 0; $i < $to_delete; $i++) {
            @unlink($files[$i]);
        }
    }
}
