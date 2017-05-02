<?php
namespace App\Repositories\Ebay;

use App\Contracts\Repository\Ebay\EbayContract;
use Illuminate\Support\Facades\Cache;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/10/2017
 * Time: 12:09 PM
 */
class EbayRepository implements EbayContract
{
    protected $accessTokenUrl = "https://api.ebay.com/identity/v1/oauth2/token";

    public function getAccessToken()
    {

        return Cache::remember('ebay_access_token', 110, function () {
            $client_id = config('ebay.client_id');
            $client_secret = config('ebay.client_secret');
            $authKey = base64_encode("{$client_id}:{$client_secret}");

            $ch = curl_init();
            $curlHeaders = array(
                "Authorization: Basic {$authKey}",
                "Content-Type: application/x-www-form-urlencoded"
            );
            curl_setopt($ch, CURLOPT_URL, $this->accessTokenUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

            $fields = "grant_type=client_credentials&scope=https://api.ebay.com/oauth/api_scope";

            $curlHeaders[] = 'Content-Length: ' . strlen($fields);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120);

            /*disable this before push to live*/
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $buffer = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($buffer);
            if (isset($result->access_token)) {
                return $result->access_token;
            }
        });
    }

    public function getItem($id)
    {
        $access_token = $this->getAccessToken();
        if (strpos($id, 'v1') !== false) {
            $id = urlencode($id);
            $url = "https://api.ebay.com/buy/browse/v1/item/{$id}";
        } else {
            $url = "https://api.ebay.com/buy/browse/v1/item/v1%7C{$id}%7C0";
        }
        $ch = curl_init();
        $curlHeaders = array(
            "Authorization: Bearer {$access_token}",
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        /*disable this before push to live*/
        $buffer = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($buffer);
        return $result;
    }

    public function getItemGroup($id)
    {
        $access_token = $this->getAccessToken();
        $url = "https://api.ebay.com/buy/browse/v1/item/get_items_by_item_group?item_group_id={$id}";
        $ch = curl_init();
        $curlHeaders = array(
            "Authorization: Bearer {$access_token}",
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        /*disable this before push to live*/
        $buffer = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($buffer);
        return $result;
    }
}