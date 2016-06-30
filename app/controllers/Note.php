<?php
class Note extends Base
{
    public function viewNote($note_id)
    {
        $is_publish = false;
        $is_unpublish = false;
        $hasRight = false;
        $note = '';
        if ($this->model->Note->ifHasRight($note_id, $this->uid)) {
            $note = $this->model->Note->getNote($note_id);
            $note_title = $this->model->Note->getNote($note_id, 'title');
            $hasRight = true;
        }
        if (isset($_SESSION['is_publish']) && $_SESSION['is_publish']) {
            $is_publish = true;
            unset($_SESSION['is_publish']);
        }
        if (isset($_SESSION['is_unpublish']) && $_SESSION['is_unpublish']) {
            $is_unpublish = true;
            unset($_SESSION['is_unpublish']);
        }
        $note_uid = false;
        if ($note['is_publish'] == 1) {
            $note_uid = $this->model->Note->getNoteUid($note_id);
        }
        $this->rightBarInfo['rightBar'] = array('myInfo');
        if ($hasRight) {
            $this->rightBarInfo['rightBar'] = array('note_info');
            $this->rightBarInfo['is_publish'] = $note['is_publish'];
            $this->rightBarInfo['created_at'] = $note['created_at'];
            $this->rightBarInfo['updated_at'] = $note['updated_at'];
            $this->rightBarInfo['updated_record'] = $note['updated_record'];
            $this->rightBarInfo['note_length'] = $note['note_length'];
        }
        $this->view->assign('note_uid', $note_uid)->assign('note_title', $note_title)->assign('is_publish', $is_publish)->assign('is_unpublish', $is_unpublish)->assign('rightBarInfo', $this->rightBarInfo)->assign('hasRight', $hasRight)->assign('note', $note)->display();
    }

    public function viewPublishNote($note_uid)
    {
        $update_note_info = array('published_hits[+]' => 1);
        $this->model->Note->updateNoteInfo($update_note_info, $note_uid, 'note_uid');
        $note = $this->model->Note->getNoteByNoteUid($note_uid);
        $this->view->assign('note', $note)->assign('use_avatar', $this->rightBarInfo['use_avatar'])->display();
    }

    public function publishNote($note_id)
    {
        if (!$this->model->Note->ifHasRight($note_id, $this->uid)) {
            $url = $this->route->url('notes');
            $this->response->redirect($url, true);
        }
        $note_title = $this->model->Note->getNote($note_id, 'title');
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('note_title', $note_title)->assign('note_id', $note_id)->assign('rightBarInfo', $this->rightBarInfo)->display();
    }

    public function insertPublishNote($note_id)
    {
        if (!$this->model->Note->ifHasRight($note_id, $this->uid)) {
            $url = $this->route->url('notes');
            $this->response->redirect($url, true);
        }
        $note_uid = getRandChar(12);
        $update_note_info = array('is_publish' => 1, 'note_uid' => $note_uid, 'published_at' => strtotime(date('Y-m-d H:i:s')));
        $this->model->Note->updateNoteInfo($update_note_info, $note_id);
        $_SESSION['is_publish'] = true;
        $url = $this->route->url('notes/' . $note_id);
        $this->response->redirect($url, true);
    }

    public function unpublishNote($note_id)
    {
        if (!$this->model->Note->ifHasRight($note_id, $this->uid)) {
            $url = $this->route->url('notes');
            $this->response->redirect($url, true);
        }
        $update_note_info = array('is_publish' => 0, 'note_uid' => '', 'published_at' => '', 'published_hits' => 0);
        $this->model->Note->updateNoteInfo($update_note_info, $note_id);
        $_SESSION['is_unpublish'] = true;
        $url = $this->route->url('notes/' . $note_id);
        $this->response->redirect($url, true);
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
        $this->rightBarInfo['note_count'] = count($root_dir_note);
        $this->rightBarInfo['note_dir_count'] = count($note_dir);
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
        $this->rightBarInfo['rightBar'] = array('note');
        $this->rightBarInfo['note_count'] = count($dir_note);
        $this->view->assign('dir_note', $dir_note)->assign('rightBarInfo', $this->rightBarInfo)->assign('dir_name', $dir_name)->assign('dir_id', $dir_id)->display();
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
            if (empty($note_title_index) || $note_title_index > 32) {
                $note['title'] = trim(substr($note_content, 0, 32));
            }
            $note['dir_id'] = $note_dir_id;
            $note['content'] = trim(str_replace($note['title'], '', $note_content));
            $note['str_length'] = strlen($note_content);
            $note['created_at'] = strtotime(date('Y-m-d H:i:s'));
            $note['user_id'] = $this->uid;
            $note_id = $this->model->Note->insertNote($note);
            $url = $this->route->url('notes/' . $note_id);
            $this->response->redirect($url, true);
        }
    }

    public function editNoteDir($dir_name)
    {
        if (!$this->model->Note->existDir($dir_name, $this->uid)) {
            $url = $this->route->url('notes');
            $this->response->redirect($url, true);
        }
        $note_dir_info = $this->model->Note->getNoteDirInfo($dir_name, $this->uid);
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('rightBarInfo', $this->rightBarInfo)->assign('note_dir_info', $note_dir_info)->display();
    }

    public function editNote($note_id)
    {
        $hasRight = false;
        $note = '';
        $current_dir_id = false;
        if ($this->model->Note->ifHasRight($note_id, $this->uid)) {
            $note = $this->model->Note->getNote($note_id);
            $current_dir_id = $note['dir_id'];
            $hasRight = true;
        }
        $current_dir_id = ($current_dir_id == 0) ? false : $current_dir_id;
        $note_dir = $this->model->Note->getNoteDir($this->uid);
        $this->view->assign('current_dir_id', $current_dir_id)->assign('hasRight', $hasRight)->assign('note_dir', $note_dir)->assign('note', $note)->display();
    }

    public function deleteNote($note_id)
    {
        if (!$this->model->Note->ifHasRight($note_id, $this->uid)) {
            $url = $this->route->url('notes');
            $this->response->redirect($url, true);
        }
        $this->model->Note->deleteNote($note_id);
        $url = $this->route->url('notes');
        $this->response->redirect($url, true);
    }

    public function updateNote($note_id)
    {
        if (!$this->model->Note->ifHasRight($note_id, $this->uid)) {
            $url = $this->route->url('notes');
            $this->response->redirect($url, true);
        }
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
            $note_title_index = strpos(trim($note_content), chr(10));
            $update_note_info['title'] = trim(substr($note_content, 0, $note_title_index));
            if (empty($note_title_index) || $note_title_index > 32) {
                $update_note_info['title'] = trim(substr($note_content, 0, 32));
            }
            $update_note_info['dir_id'] = $note_dir_id;
            $update_note_info['content'] = $note_content;
            $update_note_info['str_length'] = strlen($note_content);
            $update_note_info['updated_at'] = strtotime(date('Y-m-d H:i:s'));
            $update_note_info['updated_record[+]'] = 1;
            $this->model->Note->updateNoteInfo($update_note_info, $note_id);
            $url = $this->route->url('notes/' . $note_id);
            $this->response->redirect($url, true);
        }
    }

    public function updateNoteDir($dir_name)
    {
        if (!$this->model->Note->existDir($dir_name, $this->uid)) {
            $url = $this->route->url('notes');
            $this->response->redirect($url, true);
        }
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
            $update_note_dir_info['name'] = $dir_name;
            $update_note_dir_info['title'] = $dir_title;
            $update_note_dir_info['description'] = $dir_description;
            $update_note_dir_info['user_id'] = $this->uid;
            $update_note_dir_info['updated_at'] = strtotime(date('Y-m-d H:i:s'));
            $update_note_dir_info['updated_record[+]'] = 1;
            $this->model->Note->updateNoteDirInfo($update_note_dir_info, $this->uid);
            $url = $this->route->url('notes');
            $this->response->redirect($url, true);
        } else {
            $this->rightBarInfo['rightBar'] = array('myInfo');
            $problem = $this->model->Error->mkNoteDir_error($handler);
            $this->view->assign('rightBarInfo', $this->rightBarInfo)->assign('problem', $problem)->display('Note/mkNoteDir');
        }
    }

    public function mkNoteDir()
    {
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('rightBarInfo', $this->rightBarInfo)->display();
    }

    public function deleteNoteDir($dir_name)
    {
        if (!$this->model->Note->existDir($dir_name, $this->uid)) {
            $url = $this->route->url('notes');
            $this->response->redirect($url, true);
        }
        $this->model->Note->deleteNoteDir($dir_name, $this->uid);
        $url = $this->route->url('notes');
        $this->response->redirect($url, true);
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
