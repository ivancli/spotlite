<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 9/21/2016
 * Time: 1:56 PM
 */

namespace App\Models\Logs;


use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class CrawlerLog extends Model
{
    protected $primaryKey = "crawler_log_id";
    protected $fillable = [
        'crawler_id', 'type', 'content',
    ];

    public function crawler()
    {
        return $this->belongsTo('App\Models\Crawler', 'crawler_id', 'crawler_id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}