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
            $referer_page_url = trim(preg_replace('/%3F|%2F/i', ' ', $referer));
            $referer_page_url = preg_replace('/[ ]+/i', ' ', $referer_page_url);
            $referer_page_parse = explode(' ', $referer_page_url);
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
                $ip = new Ip($this->request->getClientIp());
                $update_user_record_info = array('last_login_ip' => $ip->ip2int());
                $user_id = $this->model->User->getUserId('username', $username);
                $this->model->User->updateUserRecord($update_user_record_info, $user_id);
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
            $handler['username'] = $this->model->User->validateUsername($username);
            $handler['email'] = $this->model->User->validateEmail($email);
            $handler['verifycode'] = $this->model->User->validateCaptcha($verifycode, $_SESSION['captcha']);
            unset($_SESSION['captcha']);
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
                $this->model->User->signin($user);
                $user_id = $this->model->User->getUserId('username', $username);
                $this->model->User->createUserRecord($user_id);
                $ip = new Ip($_SERVER['REMOTE_ADDR']);
                $update_user_record_info = array('last_login_ip' => $ip->ip2int());
                $this->model->User->updateUserRecord($update_user_record_info, $user_id);
                $this->model->User->createUserSetting($user_id);
                $update_user_setting_info = array('email' => $email)
                ;
                $this->model->User->updateUserSetting($update_user_setting_info, $user_id);
                $url = $this->route->url('balance');
                rcookie('NA', $username);
                $this->response->redirect($url, true);
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
        $this->rightBarInfo['rightBar'] = array('myInfo', 'referral');
        $this->view->assign('rightBarInfo', $this->rightBarInfo);
        $task = $this->model->User->getUserAsset($this->uid);
        $this->view->assign('task', $task)->display();
    }

    public function setting()
    {
        $user_setting = $this->model->User->getUserSetting($this->uid);
        $is_done_setting = false;
        if (isset($_SESSION['is_done_setting']) && $_SESSION['is_done_setting']) {
            $is_done_setting = true;
            unset($_SESSION['is_done_setting']);
        }
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('rightBarInfo', $this->rightBarInfo)->assign('is_done_setting', $is_done_setting)->assign('user_setting', $user_setting)->display();
    }

    public function avatar()
    {
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $avatar_status = false;
        if (isset($_SESSION['avatar_status']) && !empty($_SESSION['avatar_status'])) {
            $avatar_status = $_SESSION['avatar_status'];
            unset($_SESSION['avatar_status']);
        }
        $this->view->assign('avatar_status', $avatar_status)->assign('rightBarInfo', $this->rightBarInfo)->display();
    }

    public function getUserAvatar()
    {
        $img_size = array('large' => 72, 'normal' => 48, 'mini' => 24);
        $isPass = false;
        foreach ($img_size as $key => $value) {
            $avatar = new upload($_FILES['avatar']);
            $avatar->file_max_size = 1024 * 1024 * 2;
            $avatar->allowed = array('image/jpeg', 'image/pjpeg');
            $avatar->image_convert = 'png';
            $avatar->file_overwrite = true;
            if ($avatar->uploaded) {
                $avatar->file_new_name_body = $this->uid . '_' . $key;
                $avatar->image_resize = true;
                $avatar->image_x = $value;
                $avatar->image_y = $value;
                $avatar->process('./public/img/avatar/');
                if ($avatar->processed) {
                    $isPass = true;
                }
            }
        }
        $avatar->clean();
        if ($isPass) {
            $url = $this->route->url('settings/avatar');
            $_SESSION['avatar_status'] = '新头像设置成功';
            $update_user_setting_info = array('avatar' => 1);
            $this->model->User->updateUserSetting($update_user_setting_info, $this->uid);
            $this->response->redirect($url, true);
        } else {
            $url = $this->route->url('settings/avatar');
            $_SESSION['avatar_status'] = '文件上传格式不正确或图片超出限制大小';
            $this->response->redirect($url, true);
        }
    }

    public function userSetting()
    {
        $user_setting = $this->model->User->getUserSetting($this->uid);
        $email = $this->request->input('post.email');
        $website = $this->request->input('post.website');
        $company = $this->request->input('post.company');
        $job = $this->request->input('post.company_title');
        $location = $this->request->input('post.location');
        $signature = $this->request->input('post.tagline');
        $introduction = $this->request->input('post.bio');
        if ($user_setting['email'] != $email) {
            $handler['email'] = $this->model->User->validateEmail($email);
        }
        $handler['website'] = $this->model->User->validateWebsite($website);
        $handler['company'] = $this->model->User->validateCompany($company);
        $handler['job'] = $this->model->User->validateJob($job);
        $handler['location'] = $this->model->User->validateLocation($location);
        $handler['signature'] = $this->model->User->validateSignature($signature);
        $handler['introduction'] = $this->model->User->validateIntroduction($introduction);
        $isPass = false;
        foreach ($handler as $key => $value) {
            if ($value['msg'] != 'pass') {
                $isPass = false;
                break;
            }
            $isPass = true;
        }
        if ($isPass) {
            $update_user_setting_info['email'] = $email;
            $update_user_setting_info['website'] = $website;
            $update_user_setting_info['company'] = $company;
            $update_user_setting_info['job'] = $job;
            $update_user_setting_info['location'] = $location;
            $update_user_setting_info['signature'] = $signature;
            $update_user_setting_info['introduction'] = $introduction;
            $this->model->User->updateUserSetting($update_user_setting_info, $this->uid);
            $update_user_info = array('email' => $email);
            $this->model->User->updateUserInfo($update_user_info, $this->uid);
            $_SESSION['is_done_setting'] = true;
            $url = $this->route->url('settings');
            $this->response->redirect($url, true);
        } else {
            $problem = $this->model->Error->userSetting_error($handler);
            $this->rightBarInfo['rightBar'] = array('myInfo');
            $this->view->assign('rightBarInfo', $this->rightBarInfo)->assign('problem', $problem)->display('User/setting');
        }
    }

    public function captcha()
    {
        $captcha = new Captcha();
        $captcha->getImg();
        $_SESSION['captcha'] = $captcha->getCode();
    }
}
