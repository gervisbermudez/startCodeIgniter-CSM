<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Tightenco\Collect\Support\Collection;

class Page extends MY_model
{
    public $primaryKey = 'page_id';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return all records found on a table or false if nothing is found
     * @return Collection
     */
    public function all()
    {
        $sql = 'SELECT p.*, u.username, ug.name, ug.`level` FROM page p 
                INNER JOIN user u ON p.author = u.user_id
                INNER JOIN usergroup ug ON ug.usergroup_id = u.usergroup_id';
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return new Collection($query->result());
        }

        return false;
    }
}
