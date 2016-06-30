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

    public function getNote($note_id, $field = '')
    {
        if (!empty($field)) {
            $field_info = $this->db->select('note',
                array(
                    $field,
                ),
                array(
                    'id' => $note_id,
                )
            );
            return $field_info[0][$field];
        }
        $note = $this->db->select('note',
            array(
                'content',
                'title',
                'id(note_id)',
                'dir_id',
                'str_length(note_length)',
                'is_publish',
                'created_at',
                'updated_at',
                'updated_record',
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
                'str_length(note_length)',
                'is_publish',
                'created_at',
                'note_uid',
            ),
            array(
                'AND' => array(
                    'user_id' => $user_id,
                    'dir_id' => 0,
                ),
            )
        );
    }

    public function getNoteUid($note_id)
    {
        $note_uid = $this->db->select('note',
            array('note_uid'),
            array(
                'id' => $note_id,
            )
        );
        return $note_uid[0]['note_uid'];
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
                'id(dir_id)',
                'str_length(note_length)',
                'created_at',
                'is_publish',
                'note_uid',
            ),
            array(
                'AND' => array(
                    'dir_id' => $dir_id,
                    'user_id' => $user_id,
                ),
            )
        );
    }

    public function updateNoteDirInfo($update_note_dir_info, $user_id)
    {
        return $this->db->update('note_dir',
            $update_note_dir_info,
            array(
                'user_id' => $user_id,
            )
        );
    }

    public function deleteNoteDir($dir_name, $user_id)
    {
        $dir_id = $this->getDirId($dir_name);
        $this->db->delete('note_dir',
            array(
                'AND' => array(
                    'name' => $dir_name,
                    'user_id' => $user_id,
                ),
            )
        );
        $this->db->delete('note',
            array(
                'dir_id' => $dir_id,
            )
        );
    }

    public function getNoteByNoteUid($note_uid)
    {
        $note = $this->db->select('note',
            array(
                '[><]user' => array('user_id' => 'id'),
                '[><]user_setting' => array('user_id' => 'user_id'),
            ),
            array(
                'note.title',
                'note.content',
                'user.username(author)',
                'user_setting.avatar(use_avatar)',
                'user.id(author_id)',
                'note.published_at',
                'note.published_hits(hits)',
            ),
            array(
                'note.note_uid' => $note_uid,
            )
        );
        return $note[0];
    }

    public function deleteNote($note_id)
    {
        return $this->db->delete('note',
            array(
                'id' => $note_id,
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

    public function getNoteDirInfo($dir_name, $user_id)
    {
        $note_dir_info = $this->db->select('note_dir',
            array(
                'title',
                'description',
                'name',
            ),
            array(
                'AND' => array(
                    'name' => $dir_name,
                    'user_id' => $user_id,
                ),
            )
        );
        return $note_dir_info[0];
    }

    public function updateNoteInfo($update_note_info, $note_id, $field = '')
    {
        if (empty($field)) {
            return $this->db->update('note',
                $update_note_info,
                array(
                    'id' => $note_id,
                )
            );
        }
        return $this->db->update('note',
            $update_note_info,
            array(
                $field => $note_id,
            )
        );
    }
}
