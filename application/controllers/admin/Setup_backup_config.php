<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * One-shot catalog sync. Prefer GET /api/v1/config (admin) which already
 * calls SiteConfigModel::sync_catalog(). This URL is kept for old bookmarks.
 */
class Setup_backup_config extends MY_Controller
{
    public function index()
    {
        if (!has_permisions('UPDATE_CONFIG')) {
            show_error(lang('not_have_permissions'), 403);
            return;
        }

        $this->load->model('Admin/SiteConfigModel');
        $model = new SiteConfigModel();
        $changed = $model->sync_catalog();
        echo 'Catalog sync complete. Rows inserted or updated: ' . (int) $changed;
        echo '<br><a href="' . base_url('admin/configuration?section=system') . '">Settings</a>';
    }
}
