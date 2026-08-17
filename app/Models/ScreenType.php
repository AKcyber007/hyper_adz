<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreenType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'status'];

    /**
     * Get the screens of this type.
     */
    public function screens()
    {
        return $this->hasMany(Screen::class, 'screen_type_id');
    }
}
