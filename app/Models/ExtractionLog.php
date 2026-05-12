<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtractionLog extends Model
{
    use HasFactory;

    protected $fillable = ['username', 'filter_date', 'extraction_type'];
}
