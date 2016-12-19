<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 12/20/2016
 * Time: 9:26 AM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Unsubscriber extends Model
{
    protected $table = "unsubscribers";
    protected $primaryKey = "unsubscriber_id";
    protected $fillable = [
        "email", "blocked"
    ];

    public function block()
    {
        $this->blocked++;
        $this->save();
    }
}