<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users;

class Booking extends Model
{

    Protected $guarded =[];

    // Relationship For Doctor and User
    public function doctor()
    {
        return $this->belongsTo(User::class);
    }
    
    public function user(){

        return $this->belongsTo(User::class);
    }

 
}
