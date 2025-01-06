<?php

namespace App\Models;

use App\Models\Base;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hiscore extends Model
{
    use HasFactory;

    protected $table = 'hiscores';
    protected $primaryKey = 'id';
	
    protected $fillable = ['user_name', 'game_name', 'score'];

}
