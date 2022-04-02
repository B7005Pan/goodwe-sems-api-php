<?php

namespace GoodWe\Sems;

class Client
{
    private $username;
    private $password;
    private $httpclient;

    const HOSTNAME = "https://www.semsportal.com";
    const USERAGENT = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.82 Safari/537.36";

    function __construct($username = null, $password = null)
    {
        if (!$username || !$password) {
            throw new \Exception("Username and password are required");
        }

        $this->username = $username;
        $this->password = $password;

        if (!$this->httpclient) {
            $this->setHTTPClient();
        }
    }

    private function setHTTPClient()
    {
        $this->httpclient = curl_init(); // TODO: Use Guzzle
        curl_setopt($this->httpclient, CURLOPT_COOKIEJAR, __DIR__ . '/http_cookies.txt');
        curl_setopt($this->httpclient, CURLOPT_COOKIEFILE, __DIR__ . '/http_cookies.txt');
        curl_setopt($this->httpclient, CURLOPT_USERAGENT, self::USERAGENT);
        curl_setopt($this->httpclient, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($this->httpclient, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->httpclient, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = 'Accept: application/json, text/javascript, */*; q=0.01';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
        $headers[] = 'X-Requested-With: XMLHttpRequest';
        $headers[] = 'Sec-Ch-Ua-Mobile: ?0';
        $headers[] = 'Sec-Ch-Ua-Platform: \"Linux\"';
        $headers[] = 'Origin: https://www.semsportal.com';
        $headers[] = 'Sec-Fetch-Site: same-origin';
        $headers[] = 'Sec-Fetch-Mode: cors';
        $headers[] = 'Sec-Fetch-Dest: empty';
        $headers[] = 'Accept-Language: en-US,en;q=0.9,nl-NL;q=0.8,nl;q=0.7';
        curl_setopt($this->httpclient, CURLOPT_HTTPHEADER, $headers);
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function login()
    {
        // https://www.semsportal.com/home/login
        $ch = $this->httpclient;
        curl_setopt($ch, CURLOPT_URL, self::HOSTNAME . '/Home/Login');

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            "account=" . $this->getUsername() .
                "&pwd=" . $this->getPassword() .
                "&agreement_agreement=1"
        );

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return $this;
    }

    public function getApiData($endpoint, $param)
    {
        $data = [
            "api" => $endpoint,
            "param" => $param
        ];

        $ch = $this->httpclient;
        curl_setopt($ch, CURLOPT_URL, self::HOSTNAME . '/GopsApi/Post?s=' . $endpoint);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            "str=" . urlencode(json_encode($data))
        );


        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $result = curl_exec($ch);
        if ($result === FALSE) {
            printf(
                "cUrl error (#%d): %s<br>\n",
                curl_errno($ch),
                htmlspecialchars(curl_error($ch))
            );
        }

        return json_decode($result, true);
    }

    public function GetMonitorDetailByPowerstationId($powerStationId) {
        return $this->getApiData(
            "v1/PowerStation/GetMonitorDetailByPowerstationId", 
            [ 
                "powerStationId" => $powerStationId
            ]
        );
    }

    public function GetMonthlyChartDataByPowerstationId($powerStationId) {
        return $this->getApiData(
            "v2/Charts/GetChartByPlant", 
            [ 
                "id" => $powerStationId,
                "date" => date("Y-m-d"),
                "range" => "3",
                "chartIndexId" => "3",
                "isDetailFull" => ""
            ]
        );
    }
}
