<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'date_captured',
        'motion_detected',
        'level',
        'status',
        'snapshot',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function train()
    {
        return $this->belongsTo(Train::class, 'motion_detected', 'motion_name')
                    ->where('level', $this->level);
    }
}
