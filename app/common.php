<?php
//公用函数
function custom_error($level, $event, $value = '')
{
    $errors = array();
    $errors['user'] = array(
        'legal' => 'pass',
        'undefined' => '用户不存在',
    );
    $errors['topic'] = array(
        'legal' => 'pass',
        'undefined' => '主题未找到',
    );
    $errors['comment'] = array(
        'legal' => 'pass',
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
    $y = round($dT / 60 / 60 / 24 / 30 / 12);
    $m = round($dT / 60 / 60 / 24 / 30);
    $d = round($dT / 60 / 60 / 24);
    $h = round($dT / 60 / 60);
    $i = round($dT / 60);
    $s = $dT;
    if ($y > 0) {
        return $y . '年前';
    } elseif ($m > 0) {
        return $m . '月前';
    } elseif ($d > 0) {
        return $d . '天前';
    } elseif ($h > 0) {
        return $h . '小时前';
    } elseif ($i > 0) {
        return $i . '分钟前';
    } elseif ($s > 10) {
        return $s . '秒前';
    } elseif ($s > 0 && $s <= 10) {
        return '刚刚';
    }
}
