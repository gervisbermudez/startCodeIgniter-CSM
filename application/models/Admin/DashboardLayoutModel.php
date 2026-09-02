<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class DashboardLayoutModel extends MY_Model
{
    public $table = 'dashboard_layout';
    public $primaryKey = 'dashboard_layout_id';
    public $softDelete = false;
    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param int $user_id
     * @return array|null
     */
    public function find_for_user($user_id)
    {
        $user_id = (int) $user_id;
        if ($user_id < 1 || !$this->db->table_exists($this->table)) {
            return null;
        }
        $row = $this->db->where('user_id', $user_id)
            ->where('status', 1)
            ->limit(1)
            ->get($this->table)
            ->row_array();
        return $row ? $row : null;
    }

    /**
     * @param int $usergroup_id
     * @return array|null
     */
    public function find_for_group($usergroup_id)
    {
        $usergroup_id = (int) $usergroup_id;
        if ($usergroup_id < 1 || !$this->db->table_exists($this->table)) {
            return null;
        }
        $row = $this->db->where('usergroup_id', $usergroup_id)
            ->where('user_id IS NULL', null, false)
            ->where('status', 1)
            ->limit(1)
            ->get($this->table)
            ->row_array();
        return $row ? $row : null;
    }

    /**
     * @param int $user_id
     * @param array $layout
     * @return bool
     */
    public function save_for_user($user_id, $layout)
    {
        $user_id = (int) $user_id;
        if ($user_id < 1 || !$this->db->table_exists($this->table)) {
            return false;
        }
        $json = json_encode($layout);
        $now = date('Y-m-d H:i:s');
        $existing = $this->find_for_user($user_id);
        if ($existing) {
            $this->db->where('dashboard_layout_id', (int) $existing['dashboard_layout_id']);
            return $this->db->update($this->table, array(
                'layout_json' => $json,
                'status' => 1,
                'date_update' => $now,
            ));
        }
        return $this->db->insert($this->table, array(
            'user_id' => $user_id,
            'usergroup_id' => null,
            'layout_json' => $json,
            'status' => 1,
            'date_create' => $now,
            'date_update' => $now,
        ));
    }

    /**
     * @param int $usergroup_id
     * @param array $layout
     * @return bool
     */
    public function save_for_group($usergroup_id, $layout)
    {
        $usergroup_id = (int) $usergroup_id;
        if ($usergroup_id < 1 || !$this->db->table_exists($this->table)) {
            return false;
        }
        $json = json_encode($layout);
        $now = date('Y-m-d H:i:s');
        $existing = $this->find_for_group($usergroup_id);
        if ($existing) {
            $this->db->where('dashboard_layout_id', (int) $existing['dashboard_layout_id']);
            return $this->db->update($this->table, array(
                'layout_json' => $json,
                'status' => 1,
                'date_update' => $now,
            ));
        }
        return $this->db->insert($this->table, array(
            'user_id' => null,
            'usergroup_id' => $usergroup_id,
            'layout_json' => $json,
            'status' => 1,
            'date_create' => $now,
            'date_update' => $now,
        ));
    }

    /**
     * @param int $user_id
     * @return bool
     */
    public function delete_for_user($user_id)
    {
        $user_id = (int) $user_id;
        if ($user_id < 1 || !$this->db->table_exists($this->table)) {
            return false;
        }
        $this->db->where('user_id', $user_id);
        return $this->db->delete($this->table);
    }
}
