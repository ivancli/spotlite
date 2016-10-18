<?php
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/18/2016
 * Time: 3:17 PM
 */
class CreateTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreate()
    {
        $user = \App\Models\User::where('email', 'ivan.invigor@gmail.com')->first();

        $category = \App\Models\Category::create(array(
            "user_id" => $user->getKey(),
            "category_name" => "random category"
        ));

        $this->be($user);
        $this->call('get', route('product.create'), array("category_id" => $category->getKey()));
        $this->see("Add");
        $this->assertResponseOk();
    }
}