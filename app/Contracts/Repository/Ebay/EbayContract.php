<?php
namespace App\Contracts\Repository\Ebay;
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/10/2017
 * Time: 11:55 AM
 */
interface EbayContract
{
    public function getAccessToken();

    public function getItem($id);

    public function getItemGroup($id);
}