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
                        'task_id' => $task_id,
                    ),
                )
            )) {
                return true;
            }
        }
        return false;
    }

    public function getTaskInfo($task_id)
    {
        return $this->db->select('task',
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
    }

    public function doneTask($task)
    {
        $this->db->insert('user_task',
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
