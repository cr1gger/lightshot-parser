<?php
set_time_limit(0);
require_once './vendor/autoload.php';

class Parser
{
    public function actionIndex()
    {
        $path = __DIR__ . '\randomimg\\';
        $cook = __DIR__ . '\txt.txt';
        if (!file_exists($cook)) {
            $f = fopen("txt.txt", "w");
            fclose($f);
        }
        if (!is_dir($path)) {
            mkdir($path);
        }
        $iner = 100;
        for ($i = 0; $i < $iner; $i++) {
            if ($i === ($iner - 1)) {
                system("php index.php");
            }
            $name = $this->randName();
            $html = $this->request('https://prnt.sc/' . $name, $http_code);
            $dom = \phpQuery::newDocument($html);
            $page = pq($dom);
            $imgUrl = $page->find('.screenshot-image')->attr('src');
            if (!isset(parse_url($imgUrl)['scheme'])) {
                continue;
            }
            $newName = $name . '.' . end(explode('.', $imgUrl));
            if (!file_exists($path . $newName)) {
                file_put_contents(__DIR__ . '\randomimg\\' . $newName, $this->request($imgUrl));
                echo "Save " . $newName . "\n";
            }
        }
    }
    public function randName()
    {
        $a = 'abcdefghijklmnopqrstuvwxyz1234567890';
        $str = substr(str_shuffle($a), 0, 6);
        return $str;
    }
    public function request($url, &$response_code = '')
    {

        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'User-Agent: PostmanRuntime/7.26.1',
                'Accept: */*',
                'Accept-Encoding: utf-8',
                'Connection: keep-alive',
                'Cookie: __cfduid=' . $this->getCookies($url)
            ));
            $out = curl_exec($curl);
            $response_code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
            curl_close($curl);
            return $out;
        }
    }
    function getCookies($url)
    {

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'txt.txt');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: PostmanRuntime/7.26.1',
            'Accept: */*',
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
        ));
        $result = curl_exec($ch);
        $fl = file('txt.txt');
        $cookie = explode('__cfduid', $fl[4])[1];
        return trim($cookie);
    }
}

(new Parser())->actionIndex();
