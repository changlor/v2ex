<?php
class Base extends Kotori_Controller
{
    protected $uid = '';
    protected $rightBarInfo = array('user_info', 'rightBar');
    public function __construct()
    {
        parent::__construct();
        $this->decryptUserName();
        $this->redirect_login();
        $this->initRightBarInfo();
    }

    public function redirect_login()
    {
        if (rCookie('NA') == '') {
            $needLogin = array('balance');
            if (ACTION_NAME == 'balance') {
                $url = $this->route->url('signin?referer=balance');
                $this->response->redirect($url, true);
            }
        }
    }

    public function initRightBarInfo()
    {
        if ($this->uid != '') {
            $this->rightBarInfo['user_info'] = $this->model->User->getUserInfo($this->uid);
        }
    }

    public function decryptUserName()
    {
        $username = rCookie('NA');
        if ($username != '') {
            $uid = $this->model->User->checkExist('username', $username);
            $this->uid = $uid[0]['id'];
            if ($this->uid == '') {
                rCookie('NA', null);
            }
        }
    }
}