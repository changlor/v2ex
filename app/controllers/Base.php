<?php
class Base extends Kotori_Controller
{
    protected $uid = '';
    protected $rightBarInfo = array('user_record', 'rightBar');
    public function __construct()
    {
        parent::__construct();
        $this->decryptUserName();
        $this->redirect_login();
        $this->initRightBarInfo();
    }

    public function redirect_login()
    {
        if (rcookie('NA') == '') {
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
            $this->rightBarInfo['user_record'] = $this->model->User->getUserRecord($this->uid);
        }
    }

    public function decryptUserName()
    {
        $username = rcookie('NA');
        if ($username != '') {
            if ($this->model->User->validateUser('username', $username)) {
                $user_id = $this->model->User->getUserId('username', $username);
                $this->uid = $user_id;
            } else {
                rcookie('NA', null);
            }
        }
    }
}
