<?php
class Base extends Kotori_Controller
{
    protected $uid = '';
    protected $rightBarInfo = array();
    public function __construct()
    {
        parent::__construct();
        $this->decryptUserName();
        $this->redirectLogin();
        $this->expireInfo();
        $this->initRightBarInfo();
    }

    public function redirectLogin()
    {
        if (rcookie('NA') == '') {
            $need_login_method = array(
                'balance' => 'balance',
                'insertTopic' => '%2F',
                'previewTopic' => '%2F',
                'addTopic' => 'new',
                'insertComment' => '%2F',
                'setting' => 'settings',
                'userSetting' => '%2F',
            );
            foreach ($need_login_method as $key => $value) {
                if (ACTION_NAME == $key) {
                    $url = $this->route->url('signin?referer=' . $need_login_method[ACTION_NAME]);
                    $this->response->redirect($url, true);
                }
            }
        }
    }

    public function expireInfo()
    {
        if (ACTION_NAME == 'viewMemberNotice') {
            $update_info = array('unread_notice_count' => 0);
            $this->model->User->updateUserRecord($update_info, $this->uid);
        }
    }

    public function initRightBarInfo()
    {
        if ($this->uid != '') {
            $this->rightBarInfo['user_record'] = $this->model->User->getUserRecord($this->uid);
            $this->rightBarInfo['user_role'] = $this->model->User->getUserRole($this->uid);
            $this->rightBarInfo['daily_task'] = $this->model->Task->getDailyTask($this->uid);
            $this->rightBarInfo['signature'] = $this->model->User->getUserSignature($this->uid);
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
