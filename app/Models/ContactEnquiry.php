<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactEnquiry extends Model
{
    protected $table = 'contact_enquiries';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
