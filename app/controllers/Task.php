<?php
class Task extends Base
{
    public function dailyLoginTask()
    {
        $first_registration = false;
        $if_undo_daily_registration_task = false;
        $daily = array();
        if (isset($this->rightBarInfo['daily_task'])) {
            foreach ($this->rightBarInfo['daily_task'] as $key => $value) {
                $daily[] = $value['ename'];
            }
        }
        if (in_array('registration', $daily)) {
            $if_undo_daily_registration_task = true;
        }
        if (isset($_SESSION['first_registration']) && $_SESSION['first_registration']) {
            $first_registration = true;
            unset($_SESSION['first_registration']);
        }
        $signed_day = $this->model->Task->getSignedDay($this->uid);
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('signed_day', $signed_day)->assign('first_registration', $first_registration)->assign('if_undo_daily_registration_task', $if_undo_daily_registration_task)->assign('rightBarInfo', $this->rightBarInfo)->display('Mission/dailyRegistration');
    }

    public function redeemDailyTask($daily_task_name)
    {
        $task_info = $this->model->Task->getTaskInfo($daily_task_name, 'default');
        $task_info['created_at'] = strtotime(date('Y-m-d H:i:s'));
        $task_info['user_id'] = $this->uid;
        $task_info['event_id'] = $task_info['id'];
        $task_info['task_coin'] = $task_info['coin'] + rand(-20, 20);
        $task_info['coin'] = $task_info['task_coin'] + $this->rightBarInfo['user_record']['coin'];
        $task_info['event_type'] = 'task';
        unset($task_info['id']);
        unset($task_info['role']);
        if ($this->model->Task->isUndoDailyTask($task_info['event_id'], $this->uid)) {
            $this->model->Task->doneTask($task_info);
            $update_info = array(
                'coin[+]' => $task_info['task_coin'],
                'last_signed_at' => strtotime(date('Y-m-d')),
            );
            $this->model->Task->updateKeepSignedDay($this->uid);
            $this->model->User->updateUserRecord($update_info, $this->uid);
        }
        $url = $this->route->url('mission/daily');
        $_SESSION['first_registration'] = true;
        $this->response->redirect($url, true);
    }

    public function redeemDefaultTask($default_task_name)
    {
        $task_info = $this->model->Task->getTaskInfo($default_task_name, 'default');
        $task_info['created_at'] = strtotime(date('Y-m-d H:i:s'));
        $task_info['user_id'] = $this->uid;
        $task_info['event_id'] = $task_info['id'];
        $task_info['task_coin'] = $task_info['coin'];
        $task_info['coin'] = $task_info['task_coin'] + $this->rightBarInfo['user_record']['coin'];
        $task_info['event_type'] = 'task';
        unset($task_info['id']);
        unset($task_info['role']);
        if ($this->model->Task->isUndoDefaultTask($task_info['event_id'], $this->uid)) {
            $this->model->Task->doneTask($task_info);
            $update_info = array('status' => 3);
            $this->model->User->updateUserInfo($update_info, $this->uid);
            $update_info = array('coin[+]' => $task_info['task_coin']);
            $this->model->User->updateUserRecord($update_info, $this->uid);
        }
        $url = $this->route->url('balance');
        $this->response->redirect($url, true);
    }
}
