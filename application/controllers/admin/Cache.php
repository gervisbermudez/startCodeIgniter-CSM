<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cache Controller
 * Gestión de caché del sistema
 */
class Cache extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('cache');
        
        // Verificar que el usuario esté logueado
        if (!userdata('logged_in')) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'No autorizado']))
                ->_display();
            exit;
        }
    }
    
    /**
     * Limpiar caché de una página específica
     * POST /admin/cache/clear-page/{page_id}
     */
    public function clear_page($page_id = null)
    {
        try {
            if (!$page_id) {
                throw new Exception('ID de página no proporcionado');
            }
            
            // Verificar permisos
            if (!has_permisions('UPDATE_PAGE')) {
                throw new Exception('Sin permisos para limpiar caché');
            }
            
            // Limpiar caché de la página
            $cache_key = 'page_' . $page_id;
            delete_cache($cache_key);
            
            // También limpiar variantes comunes
            delete_cache('page_full_' . $page_id);
            delete_cache('page_content_' . $page_id);
            delete_cache('page_data_' . $page_id);
            
            // Log de la acción
            system_logger('cache', $page_id, 'clear_page_cache', 'Caché limpiado para página: ' . $page_id);
            
            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'Caché de la página limpiado correctamente',
                    'page_id' => $page_id
                ]));
                
        } catch (Exception $e) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }
    
    /**
     * Limpiar todo el caché del sistema
     * POST /admin/cache/clear-all
     */
    public function clear_all()
    {
        try {
            // Verificar permisos de administrador
            if (!has_permisions('UPDATE_CONFIG')) {
                throw new Exception('Sin permisos para limpiar todo el caché');
            }
            
            // Limpiar caché de CodeIgniter
            $this->load->driver('cache', ['adapter' => 'file']);
            $this->cache->clean();
            
            // Limpiar caché personalizado
            $cache_dir = APPPATH . 'cache/';
            
            if (is_dir($cache_dir)) {
                $files = glob($cache_dir . '*');
                $cleaned = 0;
                
                foreach ($files as $file) {
                    // No eliminar archivos importantes del sistema
                    if (is_file($file) && !in_array(basename($file), ['index.html', '.htaccess'])) {
                        if (strpos(basename($file), '.bladec') === false) {
                            @unlink($file);
                            $cleaned++;
                        }
                    }
                }
            }
            
            // Log de la acción
            system_logger('cache', 0, 'clear_all_cache', 'Todo el caché del sistema limpiado');
            
            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'Todo el caché ha sido limpiado',
                    'files_cleaned' => $cleaned ?? 0
                ]));
                
        } catch (Exception $e) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }
    
    /**
     * Limpiar caché de blog
     * POST /admin/cache/clear-blog/{blog_id}
     */
    public function clear_blog($blog_id = null)
    {
        try {
            if (!$blog_id) {
                throw new Exception('ID de blog no proporcionado');
            }
            
            if (!has_permisions('UPDATE_BLOG')) {
                throw new Exception('Sin permisos para limpiar caché');
            }
            
            $cache_key = 'blog_' . $blog_id;
            delete_cache($cache_key);
            delete_cache('blog_full_' . $blog_id);
            delete_cache('blog_content_' . $blog_id);
            
            // También limpiar listados de blog
            delete_cache('blog_list');
            delete_cache('blog_recent');
            
            system_logger('cache', $blog_id, 'clear_blog_cache', 'Caché limpiado para blog: ' . $blog_id);
            
            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'Caché del blog limpiado correctamente',
                    'blog_id' => $blog_id
                ]));
                
        } catch (Exception $e) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }
    
    /**
     * Obtener información del caché
     * GET /admin/cache/info
     */
    public function info()
    {
        try {
            $cache_dir = APPPATH . 'cache/';
            $info = [
                'cache_dir' => $cache_dir,
                'total_files' => 0,
                'total_size' => 0,
                'blade_cache_files' => 0
            ];
            
            if (is_dir($cache_dir)) {
                $files = glob($cache_dir . '*');
                
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $info['total_files']++;
                        $info['total_size'] += filesize($file);
                        
                        if (strpos(basename($file), '.bladec') !== false) {
                            $info['blade_cache_files']++;
                        }
                    }
                }
            }
            
            // Convertir tamaño a formato legible
            $info['total_size_formatted'] = $this->formatBytes($info['total_size']);
            
            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'data' => $info
                ]));
                
        } catch (Exception $e) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }
    
    /**
     * Formatear bytes a tamaño legible
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
