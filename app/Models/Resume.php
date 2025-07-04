<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    protected $table = 'resume';
     protected $fillable = [
        'user_id', 'original_name', 'storage_path', 'file_type','file_size','extracted_text','ai_analysis_status','ai_progress','ai_results'
      ];


      protected $casts = [
    'ai_analysis' => 'array',
     'ai_results' => 'array', // Automatically cast JSON to array
    'ai_progress' => 'integer',
];



protected $attributes = [
    'ai_analysis_status' => 'pending',
];


}