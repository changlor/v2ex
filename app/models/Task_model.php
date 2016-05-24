<?php
class Task_model extends Kotori_Model
{
    public function isUndoDefaultTask($task_id, $user_id)
    {
        if ($this->db->has('task',
            array('id' => $task_id)
        )) {
            if (!$this->db->has('user_asset',
                array(
                    'AND' => array(
                        'user_id' => $user_id,
                        'event_id' => $task_id,
                        'event_type' => 'task',
                    ),
                )
            )) {
                return true;
            }
        }
        return false;
    }

    public function isUndoDailyTask($task_id, $user_id)
    {
        if ($this->db->has('task',
            array('id' => $task_id)
        )) {
            if (!$this->db->has('user_asset',
                array(
                    'AND' => array(
                        'user_id' => $user_id,
                        'event_id' => $task_id,
                        'event_type' => 'task',
                        'created_at[>]' => strtotime(date('Y-m-d')),
                    ),
                )
            )) {
                return true;
            }
        }
        return false;
    }

    public function getTaskInfo($task_id, $type = '')
    {
        if ($type == 'default') {
            $task_info = $this->db->select('task',
                array(
                    'id',
                    'type',
                    'coin',
                    'about',
                    'role',
                ),
                array(
                    'ename' => $task_id,
                )
            );
            return $task_info[0];
        }
        $task_info = $this->db->select('task',
            array(
                'id',
                'type',
                'coin',
                'about',
                'role',
            ),
            array(
                'id' => $task_id,
            )
        );
        return $task_info[0];
    }

    public function getDailyTask($user_id)
    {
        $daily_task = $this->db->select('task',
            array(
                'id',
                'type',
                'coin',
                'about',
                'role',
                'ename',
            ),
            array(
                'role' => 'daily',
            )
        );
        foreach ($daily_task as $key => $value) {
            if ($this->db->has('user_asset',
                array(
                    'AND' => array(
                        'created_at[>]' => strtotime(date('Y-m-d')),
                        'event_id' => $value['id'],
                        'event_type' => 'task',
                        'user_id' => $user_id,
                    ),
                )
            )) {
                unset($daily_task[$key]);
            }
        }
        return $daily_task;
    }

    public function doneTask($task)
    {
        $this->db->insert('user_asset',
            $task
        );
    }

    public function getDoneTask($user_id)
    {
        return $this->db->select('user_asset',
            array(
                'type',
                'about',
                'event_coin',
                'coin',
                'created_at',
            ),
            array(
                'user_id' => $user_id,
            )
        );
    }

    public function getKeepSigninDay($user_id)
    {
        $keep_signin_day = $this->db->select('user_record',
            array(
                'keep_signin_day',
            ),
            array(
                'user_id' => $user_id,
            )
        );
        return $keep_signin_day[0]['keep_signin_day'];
    }

    public function updateKeepSigninDay($user_id)
    {
        $last_signin_at = $this->db->select('user_record',
            array(
                'last_signin_at',
            ),
            array(
                'user_id' => $user_id,
            )
        );
        if (($last_signin_at[0]['last_signin_at'] + 24 * 60 * 60) == strtotime(date('Y-m-d'))) {
            $this->db->update('user_record',
                array(
                    'keep_signin_day[+]' => 1,
                ),
                array(
                    'user_id' => $user_id,
                )
            );
        } else {
            $this->db->update('user_record',
                array(
                    'keep_signin_day' => 1,
                ),
                array(
                    'user_id' => $user_id,
                )
            );
        }
    }
}
