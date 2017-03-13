<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportFile extends Model
{
    protected $primaryKey = 'import_file_id';

    protected $fillable = [
        'file_name', 'extension', 'status'
    ];

    /**
     * relationship with user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }
}
