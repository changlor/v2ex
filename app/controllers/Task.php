<?php
class Task extends Base
{
    public function dailySigninTask()
    {
        $is_done_signin = false;
        $is_undo_daily_signin_task = false;
        $daily = array();
        if (isset($this->rightBarInfo['daily_task'])) {
            foreach ($this->rightBarInfo['daily_task'] as $key => $value) {
                $daily[] = $value['ename'];
            }
        }
        if (in_array('signin', $daily)) {
            $is_undo_daily_signin_task = true;
        }
        if (isset($_SESSION['is_done_signin']) && $_SESSION['is_done_signin']) {
            $is_done_signin = true;
            unset($_SESSION['is_done_signin']);
        }
        $keep_signin_day = $this->model->Task->getKeepSigninDay($this->uid);
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('keep_signin_day', $keep_signin_day)->assign('is_done_signin', $is_done_signin)->assign('is_undo_daily_signin_task', $is_undo_daily_signin_task)->assign('rightBarInfo', $this->rightBarInfo)->display('Mission/dailySignin');
    }

    public function redeemDailyTask($daily_task_name)
    {
        $task_info = $this->model->Task->getTaskInfo($daily_task_name, 'default');
        $task_info['created_at'] = strtotime(date('Y-m-d H:i:s'));
        $task_info['user_id'] = $this->uid;
        $task_info['event_id'] = $task_info['id'];
        $task_info['event_coin'] = $task_info['coin'] + rand(-20, 20);
        $task_info['coin'] = $task_info['event_coin'] + $this->rightBarInfo['user_record']['coin'];
        $task_info['event_type'] = 'task';
        unset($task_info['id']);
        unset($task_info['role']);
        if ($this->model->Task->isUndoDailyTask($task_info['event_id'], $this->uid)) {
            $this->model->Task->doneTask($task_info);
            $update_info = array(
                'coin[+]' => $task_info['event_coin'],
                'last_signin_at' => strtotime(date('Y-m-d')),
            );
            $this->model->Task->updateKeepSigninDay($this->uid);
            $this->model->User->updateUserRecord($update_info, $this->uid);
        }
        $url = $this->route->url('mission/daily');
        $_SESSION['is_done_signin'] = true;
        $this->response->redirect($url, true);
    }

    public function redeemDefaultTask($default_task_name)
    {
        $task_info = $this->model->Task->getTaskInfo($default_task_name, 'default');
        $task_info['created_at'] = strtotime(date('Y-m-d H:i:s'));
        $task_info['user_id'] = $this->uid;
        $task_info['event_id'] = $task_info['id'];
        $task_info['event_coin'] = $task_info['coin'];
        $task_info['coin'] = $task_info['event_coin'] + $this->rightBarInfo['user_record']['coin'];
        $task_info['event_type'] = 'task';
        unset($task_info['id']);
        unset($task_info['role']);
        if ($this->model->Task->isUndoDefaultTask($task_info['event_id'], $this->uid)) {
            $this->model->Task->doneTask($task_info);
            $update_info = array('status' => 3);
            $this->model->User->updateUserInfo($update_info, $this->uid);
            $update_info = array('coin[+]' => $task_info['event_coin']);
            $this->model->User->updateUserRecord($update_info, $this->uid);
        }
        $url = $this->route->url('balance');
        $this->response->redirect($url, true);
    }
}
