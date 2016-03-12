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
            $need_login_method = array(
                'balance' => 'balance',
                'insertTopic' => '%2F',
                'previewTopic' => '%2F',
                'addTopic' => 'new',
                'insertComment' => '%2F',
            );
            foreach ($need_login_method as $key => $value) {
                if (ACTION_NAME == $key) {
                    $url = $this->route->url('signin?referer=' . $need_login_method[ACTION_NAME]);
                    $this->response->redirect($url, true);
                }
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
