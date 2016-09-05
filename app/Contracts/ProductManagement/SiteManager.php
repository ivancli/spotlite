<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/5/2016
 * Time: 4:06 PM
 */

namespace App\Contracts\ProductManagement;


interface SiteManager
{
    public function getSites();

    public function getSite($id);

    public function createSite($options);

    public function updateSite($id, $options);

    public function deleteSite($id);
}