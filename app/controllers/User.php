<?php
class User extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function signin()
    {
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('rightBarInfo', $this->rightBarInfo);
        $this->view->assign('referer', $referer);
        $this->view->display();
    }

    public function signup()
    {
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('rightBarInfo', $this->rightBarInfo);
        $this->view->display();
    }

    public function login()
    {
        $referer = array();
        $referer = $this->request->input('get.referer');
        if ($referer != '') {
            $getReferer = 'y';
            $this->view->assign('getReferer', $getReferer);
        }
        if ($this->request->isPost()) {
            $event = '';
            $referer = $this->request->input('post.referer');
            $username = $this->request->input('post.u');
            $password = $this->request->input('post.p');
            $userpass = $this->model->User->checkPass($username);
            $password = hashString($password, $userpass[0]['auth_key']);
            if ($userpass[0]['password_hash'] != $password['hash']) {
                $event = 'unmatch';
                $this->_eventGenerate('password', $event);
                $this->view->assign('output', $this->user['output']);
            }
            if ($userpass[0]['password_hash'] == $password['hash']) {
                $url = $this->route->url($referer);
                rCookie('NA', $username);
                $this->response->redirect($url, true);
            }
        }
        if (rCookie('referer') != '') {
            $referer = rCookie('referer');
            rCookie('referer', null);
        }
        //$user_info = $this->model->User->getUserInfo(1);
        //$rightBarInfo['user_info'] = $user_info;
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('rightBarInfo', $this->rightBarInfo);
        $this->view->assign('referer', $referer);
        $this->view->display();
    }

    public function register()
    {
        if ($this->request->isPost()) {
            $this->user['input']['username'] = $this->request->input('post.username');
            $this->user['input']['username'] = trim($this->user['input']['username']);
            $this->user['input']['password'] = $this->request->input('post.password');
            $this->user['input']['email'] = $this->request->input('post.email');
            $verifycode = $this->request->input('post.c');
            $this->_checkUsername();
            $this->_checkEmail();
            $pos = 'pass';
            foreach ($this->user['output'] as $key => $value) {
                if ($value['msg'] != 'pass') {
                    $pos = 'failure';
                }
            }
            if ($pos == 'failure') {
                $this->view->assign('output', $this->user['output']);
            }
            if ($pos == 'pass') {
                $password = hashString($this->user['input']['password']);
                $this->user['input']['password_hash'] = $password['hash'];
                $this->user['input']['auth_key'] = $password['salt'];
                unset($this->user['input']['password']);
                $last_id = $this->model->User->signin($this->user['input']);
                if ($last_id != 0) {
                    $url = $this->route->url('User/balance');
                    rCookie('NA', $this->user['input']['username']);
                    $this->response->redirect($url, true);
                }
            }
        }
        //$user_info = $this->model->User->getUserInfo(1);
        //$rightBarInfo['user_info'] = $user_info;
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('rightBarInfo', $this->rightBarInfo);
        $this->view->display();
    }

    public function balance()
    {
        //$user_info = $this->model->User->getUserInfo(1);
        //$rightBarInfo['user_info'] = $user_info;
        $this->rightBarInfo['rightBar'] = array('myInfo', 'referral');
        $this->view->assign('rightBarInfo', $this->rightBarInfo);
        $this->view->display();
    }

    protected function _checkUsername()
    {
        $event = 'legal';
        $username = $this->user['input']['username'];
        //用户名太长
        if (strlen($username) > 16) {
            $event = 'long';
        }
        //用户名不合法
        $pattern = '/[a-zA-Z0-9]+/i';
        if (!preg_match($pattern, $this->user['input']['username'])) {
            $event = 'illegal';
        }
        //用户名存在
        $id = $this->model->User->checkExist('username', $username);
        if (isset($id[0]['id'])) {
            $event = 'exist';
        }
        //未输入用户名
        if ($username == '') {
            $event = 'undefined';
        }
        $this->_eventGenerate('username', $event, $username);
    }

    protected function _checkEmail()
    {
        $event = 'legal';
        $email = $this->user['input']['email'];
        //邮箱格式错误
        $pattern = '/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/i';
        if (!preg_match($pattern, $email)) {
            $event = 'illegal';
        }
        //邮箱存在
        $id = $this->model->User->checkExist('email', $email);
        if (isset($id[0]['id'])) {
            $event = 'exist';
        }
        //未输入邮箱
        if ($email == '') {
            $event = 'undefined';
        }
        $this->_eventGenerate('email', $event, $email);
    }

    protected function _eventGenerate($level, $event, $value = '')
    {
        $this->user['output'][$level]['event'] = $event;
        $this->user['output'][$level]['msg'] = custom_error($level, $event, $value);
    }
}
