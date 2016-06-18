<?php
class Note_model extends Kotori_Model
{
    public function validateDirName($dir_name)
    {
        $event = 'legal';
        if (strlen($dir_name) > 18) {
            $event = 'long';
        }
        if (!ctype_alnum($dir_name)) {
            $event = 'illegal';
        }
        if (empty($dir_name)) {
            $event = 'undefined';
        }
        return eventGenerate('dir_name', $event);
    }

    public function validateDirTitle($dir_title)
    {
        $event = 'legal';
        if (strlen($dir_title) > 60) {
            $event = 'long';
        }
        if (empty($dir_title)) {
            $event = 'undefined';
        }
        return eventGenerate('dir_title', $event);
    }

    public function validateDirDescription($dir_description)
    {
        $event = 'legal';
        if (empty($dir_description)) {
            $event = 'undefined';
        }
        if (strlen($dir_description) > 120) {
            $event = 'long';
        }
        return eventGenerate('dir_description', $event);
    }

    public function validateNoteContent($dir_content)
    {
        $event = 'legal';
        if (empty($dir_content)) {
            $event = 'undefined';
        }
        if (strlen($dir_content) > 2000) {
            $event = 'long';
        }
        return eventGenerate('dir_content', $event);
    }

    public function insertNoteDir($dir)
    {
        return $this->db->insert('note_dir', $dir);
    }

    public function getNoteDir($user_id)
    {
        return $this->db->select('note_dir',
            array(
                'id(dir_id)',
                'name(dir_name)',
                'title(dir_title)',
            ),
            array(
                'user_id' => $user_id,
                'ORDER' => 'id DESC',
            )
        );
    }

    public function insertNote($note)
    {
        $max_id = $this->db->max('note', 'id');
        $max_id = $max_id + 1;
        $note['id'] = $max_id;
        $this->db->insert('note', $note);
        return $max_id;
    }

    public function ifHasRight($note_id, $user_id)
    {
        return $this->db->has('note',
            array(
                'AND' => array(
                    'id' => $note_id,
                    'user_id' => $user_id,
                ),
            )
        );
    }

    public function getNote($note_id)
    {
        $note = $this->db->select('note',
            array(
                'content',
            ),
            array(
                'id' => $note_id,
            )
        );
        return $note[0];
    }

    public function getRootDirNote($user_id)
    {
        return $this->db->select('note',
            array(
                'title',
                'id(note_id)',
            ),
            array(
                'user_id' => $user_id,
            )
        );
    }

    public function existDir($dir_name, $user_id)
    {
        return $this->db->has('note_dir',
            array(
                'AND' => array(
                    'name' => $dir_name,
                    'user_id' => $user_id,
                ),
            )
        );
    }

    public function getDirNote($dir_id, $user_id)
    {
        return $this->db->select('note',
            array(
                'title',
            ),
            array(
                'dir_id' => $dir_id,
                'user_id' => $user_id,
            )
        );
    }

    public function getDirId($dir_name)
    {
        $dir_id = $this->db->select('note_dir',
            array(
                'id',
            ),
            array(
                'name' => $dir_name,
            )
        );
        return $dir_id[0]['id'];
    }
}
