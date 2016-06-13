<?php
class Ip
{
    //protected $ip;
    protected $ip_api = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=';
    protected $url;
    protected $ip;
    protected $ip_addr = array();
    public function __construct($ip)
    {
        $this->url = $this->ip_api . $ip;
        $this->ip = $ip;
    }
    public function addr()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->ip_api . $this->ip);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $this->ip_addr = curl_exec($ch);
        curl_close($ch);
        return json_decode($this->ip_addr, true);
    }
    public function ip2int()
    {
        $ip = explode('.', $this->ip);
        foreach ($ip as $key => $value) {
            if (strlen($value) > 1 && substr($value, 0, 1) == 0) {
                $ip[$key] = substr($value, 1);
            }
        }
        return ip2long(implode('.', $ip));
    }
}
