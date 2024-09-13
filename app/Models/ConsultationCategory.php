<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];
    public function experts()
{
    return $this->hasMany(Expert::class, 'consultation_category_id');
}

}
