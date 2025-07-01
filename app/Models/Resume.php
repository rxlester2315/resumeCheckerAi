<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    protected $table = 'resume';
     protected $fillable = [
        'user_id', 'original_name', 'storage_path', 'file_type','file_size','extracted_text'
      ];
}