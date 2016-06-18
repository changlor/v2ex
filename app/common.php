<?php
//公用函数
function custom_error($level, $event, $value = '')
{
    $errors = array();
    $errors['user'] = array(
        'legal' => 'pass',
        'undefined' => '用户不存在',
        'lack' => '哎呀，又没钱了，何不尝试来一发py交易，马上就能获取大量金币，请+qq958142428你懂得',
    );
    $errors['topic'] = array(
        'legal' => 'pass',
        'undefined' => '主题未找到',
    );
    $errors['comment'] = array(
        'legal' => 'pass',
        'undefined' => '回复内容不能为空',
        'repeated' => '你上一条回复的内容和这条相同',
    );
    $errors['username'] = array(
        'legal' => 'pass',
        'exist' => '用户名 ' . $value . ' 已经被注册，请重新选择一个',
        'illegal' => '用户名只能使用大小写英文字母和数字 a-Z 0-9',
        'long' => '用户名长度不能超过 16 个字符',
        'undefined' => '请输入用户名',
    );
    $errors['email'] = array(
        'legal' => 'pass',
        'exist' => '电子邮件地址 ' . $value . ' 已经注册过了',
        'illegal' => '输入的电子邮件地址格式不正确',
        'undefined' => '请输入邮箱',
    );
    $errors['verifycode'] = array(
        'illegal' => '输入的验证码不正确',
    );
    $errors['password'] = array(
        'unmatch' => '用户名和密码无法匹配',
    );
    $errors['title'] = array(
        'legal' => 'pass',
        'exist' => '这个主题标题在本站已经存在',
        'undefined' => '主题标题不能为空',
        'long' => '主题标题不能超过 120 个字符',
    );
    $errors['content'] = array(
        'legal' => 'pass',
        'long' => '主题内容不能超过 2000 个字符',
    );
    $errors['website'] = array(
        'legal' => 'pass',
        'illegal' => '你输入的网址格式不正确',
        'undefined' => 'pass',
    );
    $errors['company'] = array(
        'legal' => 'pass',
        'long' => '你输入的公司名过长，请精简',
        'undefined' => 'pass',
    );
    $errors['job'] = array(
        'legal' => 'pass',
        'long' => '你输入的职位名过长，请精简',
        'undefined' => 'pass',
    );
    $errors['location'] = array(
        'legal' => 'pass',
        'long' => '你输入的地址过长，请精简',
        'undefined' => 'pass',
    );
    $errors['signature'] = array(
        'legal' => 'pass',
        'long' => '你输入的签名过长，请精简',
        'undefined' => 'pass',
    );
    $errors['introduction'] = array(
        'legal' => 'pass',
        'long' => '你输入的简介过长，请精简',
        'undefined' => 'pass',
    );
    $errors['dir_name'] = array(
        'legal' => 'pass',
        'long' => '你的文件名过长，请精简',
        'illegal' => '为了便于兼容管理，请输入英文与数字的组合',
        'undefined' => '请输入文件名',
    );
    $errors['dir_title'] = array(
        'legal' => 'pass',
        'long' => '你的标题过长，请精简',
        'undefined' => '请输入标题',
    );
    $errors['dir_content'] = array(
        'legal' => 'pass',
        'long' => '你的内容过长，请精简',
        'undefined' => '请输入内容',
    );
    $errors['dir_description'] = array(
        'legal' => 'pass',
        'long' => '你的简介过长，请精简',
        'undefined' => 'pass',
    );
    return $errors[$level][$event];
}
function rcookie($key = '', $value = '', $expire = 36000, $path = '/')
{
    if ('' === $value) {
        if (isset($_COOKIE[$key])) {
            return authCode($_COOKIE[$key], 'DECODE');
        } else {
            return null;
        }
    } else {
        if (is_null($value)) {
            setcookie($key, '', time() - 36000, $path);
            unset($_COOKIE[$key]);
        } else {
            $authValue = authCode($value, 'ENCODE', $expire);
            setcookie($key, $authValue, time() + $expire, $path);
            $_COOKIE[$key] = $authValue;
        }
    }
    return null;
}
function makeNo()
{
    return substr(date('Ymd'), 2) . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}
function authCode($string, $operation, $expiry = 0)
{
    $key = 'waytoexplore';
    $ckey_length = 4;
    $key = md5($key);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

function randString($length, $specialChars = false)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    if ($specialChars) {
        $chars .= '!@#$%^&*()';
    }

    $result = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $result .= $chars[rand(0, $max)];
    }
    return $result;
}

function hashString($string, $salt = null)
{
    /** 生成随机字符串 */
    $salt = empty($salt) ? randString(9) : $salt;
    $length = strlen($string);
    $hash = '';
    $last = ord($string[$length - 1]);
    $pos = 0;

    /** 判断扰码长度 */
    if (strlen($salt) != 9) {
        /** 如果不是9直接返回 */
        return;
    }

    while ($pos < $length) {
        $asc = ord($string[$pos]);
        $last = ($last * ord($salt[($last % $asc) % 9]) + $asc) % 95 + 32;
        $hash .= chr($last);
        $pos++;
    }

    $result['hash'] = '$T$' . $salt . md5($hash);
    $result['salt'] = $salt;
    return $result;
}

function hashValidate($from, $to)
{
    if ('$T$' == substr($to, 0, 3)) {
        $salt = substr($to, 3, 9);
        return hashString($from, $salt) === $to;
    } else {
        return md5($from) === $to;
    }
}

function getuid()
{
    return BaseController::getInstance()->uid;
}

function eventGenerate($level, $event, $value = '')
{
    $handler['event'] = $event;
    $handler['msg'] = custom_error($level, $event, $value);
    return $handler;
}

function fadeTime($time)
{
    $dT = strtotime(date('Y-m-d H:i:s')) - $time;
    $y = floor($dT / 60 / 60 / 24 / 30 / 12);
    $m = floor($dT / 60 / 60 / 24 / 30);
    $d = floor($dT / 60 / 60 / 24);
    $h = floor($dT / 60 / 60);
    $i = floor($dT / 60);
    $s = $dT;
    if ($y > 0) {
        return $y . ' 年前';
    } elseif ($m > 0) {
        return $m . ' 月前';
    } elseif ($d > 0) {
        return $d . ' 天前';
    } elseif ($h > 0) {
        return $h . ' 小时前';
    } elseif ($i > 0) {
        return $i . ' 分钟前';
    } elseif ($s > 5) {
        return $s . ' 秒前';
    } elseif ($s >= 0 && $s <= 5) {
        return '刚刚';
    }
}
function parseUA($ua)
{
    $os = null;
    if (preg_match('/Windows NT 6.0/i', $ua)) {
        $os = "Windows Vista";
    } elseif (preg_match('/Windows NT 6.1/i', $ua)) {
        $os = "Windows 7";
    } elseif (preg_match('/Windows NT 6.2/i', $ua)) {
        $os = "Windows 8";
    } elseif (preg_match('/Windows NT 6.3/i', $ua)) {
        $os = "Windows 8.1";
    } elseif (preg_match('/Windows NT 10.0/i', $ua)) {
        $os = "Windows 10";
    } elseif (preg_match('/Windows NT 5.1/i', $ua)) {
        $os = "Windows XP";
    } elseif (preg_match('/Windows NT 5.2/i', $ua) && preg_match('/Win64/i', $ua)) {
        $os = "Windows XP 64 bit";
    } elseif (preg_match('/Android ([0-9.]+)/i', $ua, $matches)) {
        $os = "Android" . $matches[1];
    } elseif (preg_match('/iPhone OS ([_0-9]+)/i', $ua, $matches)) {
        $os = 'iPhone' . $matches[1];
    } elseif (preg_match('/Ubuntu/i', $ua, $matches)) {
        $os = 'Ubuntu';
    } elseif (preg_match('/Mac OS X ([0-9_]+)/i', $ua, $matches)) {
        $os = 'Mac OS' . $matches[1];
    } elseif (preg_match('/Linux/i', $ua, $matches)) {
        $os = 'Linux';
    } else {
        $os = '未知';
    }
    if (preg_match('#(Camino|Chimera)[ /]([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser = 'Camino ' . $matches[2];
    } elseif (preg_match('#SE 2([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser = '搜狗浏览器 2' . $matches[1];
    } elseif (preg_match('#360([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser = '360浏览器 ' . $matches[1];
    } elseif (preg_match('#Maxthon( |\/)([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser = 'Maxthon ' . $matches[2];
    } elseif (preg_match('#Chrome/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser = 'Chrome ' . $matches[1];
    } elseif (preg_match('#XiaoMi/MiuiBrowser/([0-9.]+)#i', $ua, $matches)) {
        $browser = '小米浏览器 ' . $matches[1];
    } elseif (preg_match('#Safari/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser = 'Safari ' . $matches[1];
    } elseif (preg_match('#opera mini#i', $ua)) {
        preg_match('#Opera/([a-zA-Z0-9.]+)#i', $ua, $matches);
        $browser = 'Opera Mini ' . $matches[1];
    } elseif (preg_match('#Opera.([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser = 'Opera ' . $matches[1];
    } elseif (preg_match('#TencentTraveler ([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser = '腾讯TT浏览器 ' . $matches[1];
    } elseif (preg_match('#UCWEB([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser = 'UCWEB ' . $matches[1];
    } elseif (preg_match('#MSIE ([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser = 'Internet Explorer ' . $matches[1];
    } elseif (preg_match('#Trident#', $ua, $matches)) {
        $browser = 'Internet Explorer 11';
    } elseif (preg_match('#Edge/12.0#i', $ua, $matches)) {
        //win10中spartan浏览器
        $browser = 'Spartan';
    } elseif (preg_match('#(Firefox|Phoenix|Firebird|BonEcho|GranParadiso|Minefield|Iceweasel)/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser = 'Firefox ' . $matches[2];
    } else {
        $browser = '未知';
    }
    $client['os'] = $os;
    $client['browser'] = $browser;
    return $client['os'];
}

function get_tags_arr($title)
{
    $app_root = Kotori_Soul::getSoul('Config')->APP_FULL_PATH . '/libraries';
    $pscws = new PSCWS4();
    $pscws->set_dict($app_root . '/scws/dict.utf8.xdb');
    $pscws->set_rule($app_root . '/scws/rules.utf8.ini');
    $pscws->set_ignore(true);
    $pscws->send_text($title);
    $words = $pscws->get_tops(5);
    $tags = array();
    foreach ($words as $val) {
        $tags[] = $val['word'];
    }
    $pscws->close();
    return $tags;
}

function get_keywords_str($content)
{
    PhpAnalysis::$loadInit = false;
    $pa = new PhpAnalysis('utf-8', 'utf-8', false);
    $pa->LoadDict();
    $pa->SetSource($content);
    $pa->StartAnalysis(false);
    $tags = $pa->GetFinallyResult();
    return $tags;
}

function coin($coin)
{
    $gold = floor($coin / 100 / 100);
    $silver = floor(($coin % 10000) / 100);
    $bronze = $coin % 100;
    $coid = '';
    if ($gold >= 1) {
        $coid .= $gold . ' %gold% ';
    }
    if ($silver >= 1) {
        $coid .= $silver . ' %silver% ';
    }
    $coid .= $bronze . ' %bronze% ';
    return $coid;
}
function encrypt($data, $key)
{
    $key = md5($key);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';
    $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= $key{$x};
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
    }
    return base64_encode($str);
}
function decrypt($data, $key)
{
    $key = md5($key);
    $x = 0;
    $data = base64_decode($data);
    $len = strlen($data);
    $l = strlen($key);
    $char = '';
    $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return $str;
}
