<?php
class Note extends Base
{
    public function viewNote($note_id)
    {
        $hasRight = false;
        $note = '';
        if ($this->model->Note->ifHasRight($note_id, $this->uid)) {
            $note = $this->model->Note->getNote($note_id);
            $hasRight = true;
        }
        $this->rightBarInfo['rightBar'] = array('note_info');
        $this->view->assign('rightBarInfo', $this->rightBarInfo)->assign('hasRight', $hasRight)->assign('note', $note)->display();
    }

    public function viewNoteDir()
    {
        $note_dir = $this->model->Note->getNoteDir($this->uid);
        $root_dir_note = $this->model->Note->getRootDirNote($this->uid);
        $is_mk_note_dir = false;
        $note_dir_name = false;
        if (isset($_SESSION['is_mk_note_dir']) && $_SESSION['is_mk_note_dir']) {
            unset($_SESSION['is_mk_note_dir']);
            $is_mk_note_dir = true;
            $note_dir_name = $_SESSION['note_dir_name'];
            unset($_SESSION['note_dir_name']);
        }
        $this->rightBarInfo['rightBar'] = array('note');
        $this->view->assign('is_mk_note_dir', $is_mk_note_dir)->assign('root_dir_note', $root_dir_note)->assign('note_dir', $note_dir)->assign('note_dir_name', $note_dir_name)->assign('rightBarInfo', $this->rightBarInfo)->display();
    }

    public function viewDirNote($dir_name)
    {
        if (!$this->model->Note->existDir($dir_name, $this->uid)) {
            $url = $this->route->url('notes');
            $this->response->redirect($url, true);
        }
        $dir_id = $this->model->Note->getDirId($dir_name);
        $dir_note = $this->model->Note->getDirNote($dir_id, $this->uid);
        $this->view->assign('dir_note', $dir_note)->assign('dir_id', $dir_id)->display();
    }

    public function addNote()
    {
        $note_dir = $this->model->Note->getNoteDir($this->uid);
        $folder_id = $this->request->input('get.folder_id');
        $current_dir_id = false;
        foreach ($note_dir as $key => $value) {
            if ($value['dir_id'] == $folder_id) {
                $current_dir_id = $folder_id;
                break;
            }
        }
        $this->view->assign('note_dir', $note_dir)->assign('current_dir_id', $current_dir_id)->display();
    }

    public function insertNote()
    {
        $note_content = $this->request->input('post.content');
        $note_dir_id = $this->request->input('post.parent_id');
        $note_dir = $this->model->Note->getNoteDir($this->uid);
        $exist_note_dir_id = false;
        foreach ($note_dir as $key => $value) {
            if ($value['dir_id'] == $note_dir_id) {
                $exist_note_dir_id = true;
                break;
            }
        }
        $note_dir_id = ($exist_note_dir_id) ? $note_dir_id : 0;
        $handler['note_content'] = $this->model->Note->validateNoteContent($note_content);
        $isPass = false;
        foreach ($handler as $key => $value) {
            if ($value['msg'] != 'pass') {
                $isPass = false;
                break;
            }
            $isPass = true;
        }
        if ($isPass) {
            $note_title_index = strpos($note_content, chr(10));
            $note['title'] = trim(substr($note_content, 0, $note_title_index));
            $note['dir_id'] = $note_dir_id;
            $note['content'] = $note_content;
            $note['created_at'] = strtotime(date('Y-m-d H:i:s'));
            $note['user_id'] = $this->uid;
            $note_id = $this->model->Note->insertNote($note);
            $url = $this->route->url('notes/' . $note_id);
            $this->response->redirect($url, true);
        }
    }

    public function mkNoteDir()
    {
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('rightBarInfo', $this->rightBarInfo)->display();
    }

    public function insertNoteDir()
    {
        $dir_name = trim($this->request->input('post.name'));
        $dir_title = trim($this->request->input('post.title'));
        $dir_description = $this->request->input('post.description');
        $handler['dir_name'] = $this->model->Note->validateDirName($dir_name);
        $handler['dir_description'] = $this->model->Note->validateDirDescription($dir_description);
        $handler['dir_title'] = $this->model->Note->validateDirTitle($dir_title);
        $isPass = false;
        foreach ($handler as $key => $value) {
            if ($value['msg'] != 'pass') {
                $isPass = false;
                break;
            }
            $isPass = true;
        }
        if ($isPass) {
            $dir['name'] = $dir_name;
            $dir['title'] = $dir_title;
            $dir['description'] = $dir_description;
            $dir['user_id'] = $this->uid;
            $dir['created_at'] = strtotime(date('Y-m-d H:i:s'));
            $this->model->Note->insertNoteDir($dir);
            $url = $this->route->url('notes');
            $_SESSION['is_mk_note_dir'] = true;
            $_SESSION['note_dir_name'] = $dir_name;
            $this->response->redirect($url, true);
        } else {
            $this->rightBarInfo['rightBar'] = array('myInfo');
            $problem = $this->model->Error->mkNoteDir_error($handler);
            $this->view->assign('rightBarInfo', $this->rightBarInfo)->assign('problem', $problem)->display('Note/mkNoteDir');
        }
    }
}
