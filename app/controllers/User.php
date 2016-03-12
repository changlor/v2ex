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
        $referer = $this->request->input('get.referer');
        $isReferer = false;
        if ($referer != '') {
            $isReferer = true;
        }
        if (!$isReferer) {
            $pattern = '/' . urlencode($this->route->url()) . '/i';
            $referer = preg_replace($pattern, '', urlencode($_SERVER['HTTP_REFERER']));
            $referer_page_parse = preg_split('/%3F|%2F/i', $referer);
            $referer_page_method = $referer_page_parse[0];
            $redirect_index_page = array('signin', 'signup', 'signout', '');
            if (in_array($referer_page_method, $redirect_index_page)) {
                $referer = '/';
            }
        }
        $this->view->assign('rightBarInfo', $this->rightBarInfo)->assign('isReferer', $isReferer)->assign('referer', $referer)->display();
    }

    public function signup()
    {
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('rightBarInfo', $this->rightBarInfo)->display();
    }

    public function signout()
    {
        rcookie('NA', null);
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('rightBarInfo', $this->rightBarInfo)->display();
    }

    public function login()
    {
        if ($this->request->isPost()) {
            $isReferer = false;
            $referer = $this->request->input('post.referer');
            if ($referer != '') {
                $isReferer = true;
            }
            $referer = empty($referer) ? '/' : urldecode($referer);
            $username = $this->request->input('post.u');
            $password = $this->request->input('post.p');
            $userpass = $this->model->User->validatePass($username);
            $password = hashString($password, $userpass['auth_key']);
            if ($userpass['password_hash'] == $password['hash']) {
                $url = $this->route->url($referer);
                rcookie('NA', $username);
                $this->response->redirect($url, true);
            } else {
                $handler['password'] = eventGenerate('password', 'unmatch');
                $problem = $this->model->Error->signin_error($handler);
                $this->rightBarInfo['rightBar'] = array('myInfo');
                $this->view->assign('problem', $problem)->assign('isReferer', $isReferer)->assign('rightBarInfo', $this->rightBarInfo)->assign('referer', $referer)->display('User/signin');
            }
        } else {
            $this->response->setStatus('403');
            echo '403';
        }
    }

    public function register()
    {
        if ($this->request->isPost()) {
            $username = $this->request->input('post.username');
            $password = $this->request->input('post.password');
            $email = $this->request->input('post.email');
            $verifycode = $this->request->input('post.c');
            $username = trim($username);
            $handler['username'] = $this->model->User->checkUsername($username);
            $handler['email'] = $this->model->User->checkEmail($email);
            $isPass = false;
            foreach ($handler as $key => $value) {
                if ($value['msg'] != 'pass') {
                    $isPass = false;
                    break;
                }
                $isPass = true;
            }
            if ($isPass) {
                $password = hashString($password);
                $user['password_hash'] = $password['hash'];
                $user['auth_key'] = $password['salt'];
                $user['username'] = $username;
                $user['email'] = $email;
                $user['created_at'] = strtotime(date('Y:m:d H:i:s'));
                $last_id = $this->model->User->signin($user);
                if ($last_id != 0) {
                    $url = $this->route->url('User/balance');
                    rcookie('NA', $user['username']);
                    $this->response->redirect($url, true);
                }
            } else {
                $problem = $this->model->Error->signup_error($handler);
                $this->rightBarInfo['rightBar'] = array('myInfo');
                $this->view->assign('problem', $problem)->assign('rightBarInfo', $this->rightBarInfo)->display('User/signup');
            }
        } else {
            $this->response->setStatus('403');
            echo '403';
        }
    }

    public function balance()
    {
        //$user_info = $this->model->User->getUserInfo(1);
        //$rightBarInfo['user_info'] = $user_info;
        $this->rightBarInfo['rightBar'] = array('myInfo', 'referral');
        $this->view->assign('rightBarInfo', $this->rightBarInfo);
        $this->view->display();
    }
}
