<?php
class Task_model extends Kotori_Model
{
    public function isUndoTask($task_id, $user_id)
    {
        if ($this->db->has('task',
            array('id' => $task_id)
        )) {
            if (!$this->db->has('user_task',
                array(
                    'AND' => array(
                        'user_id' => $user_id,
                        'event_id' => $task_id,
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
        return $this->db->select('user_task',
            array(
                'type',
                'about',
                'task_coin',
                'coin',
                'created_at',
            )
        );
    }
}
