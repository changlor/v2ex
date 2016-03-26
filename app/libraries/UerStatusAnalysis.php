<?php
class UserStatusAnalysis
{
    protected $username;
    protected $session_begin_time;
    protected $session_expired_time;
    protected $user_pool;

    public function __construct($username)
    {
        $this->username = $username;
        $this->session_begin_time = strtotime(date('Y-m-d H:i:s'));
        $this->session_expired_time = $this->session_begin_time - 3 * 60;
        $this->read_user_pool();
        $this->clear_expired_session();
        $this->new_session();
        $this->save_user_pool();
    }

    protected function new_session()
    {
        $this->user_pool[$this->username] = $this->session_begin_time;
    }

    protected function residual_expiration_time($username)
    {
        $fade_time = $this->session_begin_time - $this->user_pool[$username];
        return $remain_time = 3 * 60 - $fade_time;
    }

    protected function clear_expired_session()
    {
        foreach ($this->user_pool as $key => $value) {
            if ($value < $this->session_expired_time) {
                unset($this->user_pool[$key]);
            }
        }
    }

    protected function read_user_pool()
    {
        $user_pool = file_get_contents('./user_pool/user_pool.txt');
        $this->user_pool = json_decode($user_pool, true);
    }

    protected function save_user_pool()
    {
        $user_pool = json_encode($this->user_pool);
        file_put_contents('./user_pool/user_pool.txt', $user_pool);
    }

    public function getUserStatus()
    {
        if (isset($this->user_pool[$this->username])) {
            return ture;
        }
        return false;
    }

    public function getOnlineUserNumber()
    {
        return count($this->user_pool);
    }

    public function getOnlineUserInfo()
    {
        $user_info = false;
        $user_info = '当前在线人数为' . $this->getOnlineUserNumber();
        $user_info .= '<br />当前在线用户<br />';
        foreach ($this->user_pool as $key => $value) {
            $user_info .= $key;
            $user_info .= '&nbsp;';
            $user_info .= '剩余' . $this->residual_expiration_time($key) . 's过期<br />';
        }
        return $user_info;
    }
}