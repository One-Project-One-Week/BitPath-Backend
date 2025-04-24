<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resourcelink extends Model
{
    protected $table = 'resourcelinks';
    protected $primaryKey = 'id';

    protected $fillable = [
        'recommand_resource_id',
        'name',
        'link'
    ];

    public function recommand_resource()
    {
        return $this->belongsTo(RecommandResource::class);
    }
}
