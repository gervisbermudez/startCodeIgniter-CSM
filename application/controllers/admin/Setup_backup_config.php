<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Setup Backup Configuration
 * This controller sets up the automatic backup configuration in the database
 */
class Setup_backup_config extends CI_Controller
{
    public function index()
    {
        $this->load->database();
        
        $configs = [
            [
                'config_name' => 'AUTO_BACKUP_ENABLED',
                'config_label' => 'Habilitar Backups Automáticos',
                'config_value' => 'No',
                'config_type' => 'system',
                'config_description' => 'Activa o desactiva la creación automática de backups de base de datos.',
                'config_data' => json_encode([
                    'type_value' => 'boolean',
                    'validate_as' => 'boolean',
                    'handle_as' => 'switch',
                    'perm_values' => ['No', 'Si'],
                    'true' => 'Si',
                    'false' => 'No'
                ])
            ],
            [
                'config_name' => 'AUTO_BACKUP_FREQUENCY',
                'config_label' => 'Frecuencia de Backups',
                'config_value' => 'daily',
                'config_type' => 'system',
                'config_description' => 'Con qué frecuencia se crean los backups automáticos.',
                'config_data' => json_encode([
                    'type_value' => 'string',
                    'validate_as' => 'text',
                    'handle_as' => 'select',
                    'perm_values' => [
                        'hourly' => 'Cada hora',
                        'daily' => 'Diariamente',
                        'weekly' => 'Semanalmente',
                        'monthly' => 'Mensualmente'
                    ]
                ])
            ],
            [
                'config_name' => 'AUTO_BACKUP_RETENTION',
                'config_label' => 'Backups a Mantener',
                'config_value' => '7',
                'config_type' => 'system',
                'config_description' => 'Número de backups a mantener. Los más antiguos se eliminan automáticamente.',
                'config_data' => json_encode([
                    'type_value' => 'number',
                    'validate_as' => 'number',
                    'handle_as' => 'input',
                    'input_type' => 'number',
                    'min_value' => '1',
                    'max_value' => '30'
                ])
            ],
            [
                'config_name' => 'AUTO_BACKUP_TIME',
                'config_label' => 'Hora de Backup (24h)',
                'config_value' => '03:00',
                'config_type' => 'system',
                'config_description' => 'Hora del día para ejecutar backups automáticos (formato 24h: HH:MM).',
                'config_data' => json_encode([
                    'type_value' => 'string',
                    'validate_as' => 'text',
                    'handle_as' => 'input',
                    'input_type' => 'time'
                ])
            ],
            [
                'config_name' => 'LAST_AUTO_BACKUP',
                'config_label' => 'Último Backup Automático',
                'config_value' => '',
                'config_type' => 'system',
                'config_description' => 'Fecha y hora del último backup automático ejecutado.',
                'config_data' => json_encode([
                    'type_value' => 'string',
                    'validate_as' => 'text',
                    'handle_as' => 'input',
                    'input_type' => 'text',
                    'readonly' => true
                ])
            ]
        ];

        foreach ($configs as $config) {
            // Check if config already exists
            $existing = $this->db->get_where('site_config', ['config_name' => $config['config_name']])->row();
            
            if (!$existing) {
                $config['user_id'] = 1;
                $config['readonly'] = 0;
                $config['status'] = 1;
                $config['date_create'] = date('Y-m-d H:i:s');
                $config['date_update'] = date('Y-m-d H:i:s');
                
                $this->db->insert('site_config', $config);
                echo "✓ Configuración '{$config['config_name']}' creada<br>";
            } else {
                echo "- Configuración '{$config['config_name']}' ya existe<br>";
            }
        }
        
        echo "<br><strong>Setup completado!</strong><br>";
        echo "<a href='" . base_url('admin/configuration?section=system') . "'>Ver configuraciones</a>";
    }
}
