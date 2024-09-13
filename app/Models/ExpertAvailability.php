<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'expert_id',
        'date',
        'time_slot',
        'is_booked',
    ];

    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }
}
