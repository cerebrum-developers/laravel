<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machines extends Model
{
    use HasFactory;

    protected $table = 'machines';
    // Primary Key
    public $primaryKey = 'id';
    // Timestamps
    public $timestamps = true;


   
}
