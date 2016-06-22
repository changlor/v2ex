<?php
require './Kotori.php';

$app = new Kotori();

$config['APP_PATH'] = './app/';
$config['DB_HOST'] = '127.0.0.1';
$config['DB_USER'] = 'root';
$config['DB_PWD'] = 'root';
$config['DB_TYPE'] = 'mysql';
$config['DB_NAME'] = 'v2ex';
$config['URL_MODE'] = 'PATH_INFO';
$config['URL_ROUTE'] = array(
    'notes/edit/([^/]+)' => array(
        'get' => 'Note/editNote/$1',
        'post' => 'Note/updateNote/$1',
    ),
    'notes/delete/([0-9]+)' => 'Note/deleteNote/$1',
    'notes/rmdir/([^/]+)' => 'Note/deleteNoteDir/$1',
    'notes/list/([^/]+)' => 'Note/viewDirNote/$1',
    'notes/manage/([^/]+)' => array(
        'get' => 'Note/editNoteDir/$1',
        'post' => 'Note/updateNoteDir/$1',
    ),
    'notes/([0-9]+)' => 'Note/viewNote/$1',
    'notes' => 'Note/viewNoteDir',
    'notes/mkdir' => array(
        'get' => 'Note/mkNoteDir',
        'post' => 'Note/insertNoteDir',
    ),
    'notes/new' => array(
        'get' => 'Note/addNote',
        'post' => 'Note/insertNote',
    ),
    'ip' => 'Member/queryIp',
    'settings' => array(
        'get' => 'User/setting',
        'post' => 'User/userSetting',
    ),
    'thank/reply/([^/]+)' => 'Profit/thankReply/$1',
    'thank/topic/([^/]+)' => 'Profit/thankTopic/$1',
    'mission/daily' => 'Task/dailySigninTask',
    'mission/daily/redeem' => 'Task/redeemDailyTask/signin',
    'mission/complete/redeem' => 'Task/redeemDefaultTask/base',
    'planes' => 'Node/viewAllNode',
    'notifications' => 'Member/viewMemberNotice',
    'tag/([^/]+)' => 'Tag/viewTagTopic/$1',
    'go/([^/]+)' => 'Node/viewNodeTopic/$1',
    'member/([^/]+)/replies' => 'Member/viewMemberComment/$1',
    'member/([^/]+)/topics' => 'Member/viewMemberTopic/$1',
    'member/([^/]+)' => 'Member/home/$1',
    't/([^/]+)' => array(
        'get' => 'Topic/viewTopic/$1',
        'post' => 'Comment/insertComment/$1',
    ),
    'signup' => array(
        'get' => 'User/signup',
        'post' => 'User/register',
    ),
    'signin' => array(
        'get' => 'User/signin',
        'post' => 'User/login',
    ),
    'signout' => 'User/signout',
    'balance' => 'User/balance',
    'new' => array(
        'get' => 'Topic/addTopic',
        'post' => 'Topic/insertTopic',
    ),
    'preview/markdown' => array(
        'post' => 'Topic/previewTopic',
    ),
);
$config['ERROR_TPL'] = 'Public/404';

$app->run();
