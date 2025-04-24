<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommandResource extends Model
{
    protected $table = 'recommand_resources';
    protected $primaryKey = 'id';

    protected $fillable = [
        'skill',
    ];
    
}
