<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Train extends Model
{
    use HasFactory;

    protected $table = 'trains';
    protected $primaryKey = 'train_id';

    protected $fillable = [
        'motion_name',
        'threshold',
        'level',
    ];

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'motion_detected', 'motion_name')
                    ->where('level', $this->level);
    }
}
