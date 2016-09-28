<?php
namespace App\Libraries;
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 5/08/2016
 * Time: 10:12 PM
 */
trait CommonFunctions
{
    public function sendCurl($url, $options)
    {
        $ch = curl_init();
        $curlHeaders = array(
            'Accept-Language: en-us',
            'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.15) Gecko/20110303 Firefox/3.6.15',
            'Connection: Keep-Alive',
            'Cache-Control: no-cache',
        );
        if (isset($options['cookie'])) {
            if (is_array($options['cookie']) && isset($options['cookie']['header'])) {
                $curlHeaders[] = $options['cookie']['header'];
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);

        if (isset($options['ips'])) {
            if (is_array($options['ips']) && count($options['ips']) > 0) {
                $ipRandKey = array_rand($options['ips'], 1);
                curl_setopt($ch, CURLOPT_INTERFACE, $options['ips'][$ipRandKey]);
            }
        }

        if (isset($options['cookie'])) {
            if (is_array($options['cookie']) && isset($options['cookie']['file'])) {
                curl_setopt($ch, CURLOPT_COOKIEFILE, $options['cookie']['file']);
                curl_setopt($ch, CURLOPT_COOKIEJAR, $options['cookie']['file']);
            }
        }

        if (!is_null($options['userpass']) && is_string($options['userpass'])) {
            curl_setopt($ch, CURLOPT_USERPWD, $options['userpass']);
        }

        if (isset($options['method'])) {
            switch ($options['method']) {
                case "post":
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    break;
                case "put":
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    break;
                case "delete":
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                    break;
                case "get":
                default:
            }
        }
        if (isset($options['fields'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['fields']);
            if (isset($options['data_type']) && $options['data_type'] == "json") {
                $curlHeaders[] = 'Content-Type: application/json';
                $curlHeaders[] = 'Content-Length: ' . strlen($options['fields']);
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
        curl_setopt($ch, CURLOPT_HEADER, isset($options['show_header']) && $options['show_header'] == 1 ? 1 : 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        /*disable this before push to live*/
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $buffer = curl_exec($ch);
        $result = curl_close($ch);

        unset($ch);
        return $buffer;
    }

    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function removeGlobalWebTracking($url)
    {
        $adTrackingParameters = config("constants.ad_tracking_parameters");
        foreach ($adTrackingParameters as $adTrackingParameter) {
            $url = $this->removeqsvar($url, $adTrackingParameter);
        }
        return $url;
    }

    /**
     *Removing parameters from URL
     * http://stackoverflow.com/questions/1251582/beautiful-way-to-remove-get-variables-with-php
     * @param $url
     * @param $varname
     * @return mixed
     */
    public function removeqsvar($url, $varname)
    {
        return preg_replace('/([?&])' . $varname . '=[^&]+(&|$)/', '$1', $url);
    }
}