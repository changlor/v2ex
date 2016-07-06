<?php
class Avatar
{
    protected $user_id;
    protected $user_avatar;
    protected $log_file_dir;
    protected $user_pool;

    public function __construct($user_id)
    {
        $this->log_file_dir = dirname(__FILE__) . '/avatar.txt';
        $this->read_user_pool();
        $this->user_id = $user_id;
    }

    protected function get_user_status()
    {
        if (isset($this->user_pool[$this->user_id])) {
            return true;
        }
        return false;
    }

    protected function read_user_pool()
    {
        $user_pool = file_get_contents($this->log_file_dir);
        $this->user_pool = json_decode($user_pool, true);
    }

    protected function save_user_pool()
    {
        $user_pool = json_encode($this->user_pool);
        file_put_contents($this->log_file_dir, $user_pool);
    }

    public function getUserAvatar()
    {
        return $this->user_pool[$this->user_id];
    }

    public function hasRecord()
    {
        return $this->get_user_status;
    }

    public function setRecord($user_avatar)
    {
        $this->user_avatar = $user_avatar;
        $this->user_pool[$this->user_id] = $user_avatar;
    }

    public function saveRecord()
    {
        $this->save_user_pool();
    }
}
