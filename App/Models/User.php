<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class User extends Model{
    protected $table = 'usuarios';
    // desactivar updated_at y created_at
    public $timestamps = false;
    
}