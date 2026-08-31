<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class NotificationsModel extends MY_Model
{
    public $table = 'notifications';
    public $primaryKey = 'notification_id';
    public $softDelete = true;

    public $hasOne = [
        'user' => ['user_id', 'Admin/UserModel', 'UserModel'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        return $collection;
    }

    /**
     * Inbox of one user. Status 0 is always excluded.
     * $status: 1 unread, 2 read, 'all' for 1+2.
     *
     * @param int $userId
     * @param int|string $status
     * @param int|null $limit
     * @return array
     */
    public function inbox($userId, $status = 1, $limit = 20)
    {
        $this->db->from($this->table);
        $this->db->where('user_id', (int) $userId);
        $this->db->where('status !=', 0);
        if ($status !== 'all' && $status !== null && $status !== '') {
            $this->db->where('status', (int) $status);
        }
        $this->db->order_by('date_create', 'DESC');
        if ($limit !== null && $limit !== false && (int) $limit > 0) {
            $this->db->limit((int) $limit);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return array();
    }

    /**
     * @param int $userId
     * @return int
     */
    public function unread_count($userId)
    {
        $this->db->from($this->table);
        $this->db->where('user_id', (int) $userId);
        $this->db->where('status', 1);
        return (int) $this->db->count_all_results();
    }

    /**
     * Mark one row as read (status 2). Does not delete.
     *
     * @param int $notificationId
     * @param int $userId
     * @return bool
     */
    public function mark_read($notificationId, $userId)
    {
        $found = $this->find($notificationId);
        if (!$found || (int) $this->user_id !== (int) $userId || (int) $this->status === 0) {
            return false;
        }
        $this->status = 2;
        return $this->save();
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function mark_all_read($userId)
    {
        $this->db->where('user_id', (int) $userId);
        $this->db->where('status', 1);
        return $this->db->update($this->table, array(
            'status' => 2,
            'date_update' => date('Y-m-d H:i:s'),
        ));
    }
}
