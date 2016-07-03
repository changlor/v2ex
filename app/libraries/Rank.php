<?php
class Rank
{
    protected $username;
    protected $quantity;
    protected $user_pool;
    protected $user_count;
    protected $rank_date;
    protected $log_file_dir;

    public function __construct($username)
    {
        $this->rank_date = date('Ymd');
        $this->log_file_dir = dirname(__FILE__) . '/rank/' . $this->rank_date . '.txt';
        $this->read_user_pool();
        $this->username = $username;
        $this->user_count = $this->get_user_count();
        $this->rank = $this->user_pool[$this->username]['rank'];
        $this->quantity = $this->user_pool[$this->username]['quantity'];
    }

    public function record()
    {
        $isset_user = $this->get_user_status();
        if (!$isset_user) {
            $this->user_pool[$this->username]['rank'] = $this->user_count + 1;
            $this->user_pool[$this->username]['quantity'] = 1;
            $this->save_user_pool();
        } else {
            $this->setUserInfo();
            $this->refreshUserRank();
            $this->save_user_pool();
        }
    }

    public function get_user_status()
    {
        if (isset($this->user_pool[$this->username])) {
            return true;
        }
        return false;
    }

    protected function setUserInfo()
    {
        $this->quantity = $this->user_pool[$this->username]['quantity'] + 1;
    }

    protected function read_user_pool()
    {
        if (!file_exists($this->log_file_dir)) {
            $user_pool = fopen($this->log_file_dir, 'w');
            fclose($user_pool);
        }
        $user_pool = file_get_contents($this->log_file_dir);
        $this->user_pool = json_decode($user_pool, true);
    }

    protected function save_user_pool()
    {
        $user_pool = json_encode($this->user_pool);
        file_put_contents($this->log_file_dir, $user_pool);
    }

    public function refreshUserRank()
    {
        $rank = 1;
        unset($this->user_pool[$this->username]);
        foreach ($this->user_pool as $key => $value) {
            if ($value['quantity'] >= $this->quantity) {
                $rank++;
            } elseif ($value['quantity'] + 1 == $this->quantity) {
                $this->user_pool[$key]['rank']++;
            }
        }
        $this->user_pool[$this->username]['rank'] = $rank;
        $this->user_pool[$this->username]['quantity'] = $this->quantity;
    }

    public function get_user_count()
    {
        return $this->user_count = count($this->user_pool);
    }

    public function getUserRank()
    {
        return $this->rank;
    }

    public function getUserQuantity()
    {
        return $this->quantity;
    }
}
