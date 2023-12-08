<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Ebook extends Model
{
    use HasFactory;

    public function getCreatedAtDateAttribute($value) {
        return date('d/m/Y H:i a', strtotime($this->created_at));
    }

    public function getImageAttribute($value) {
        if($value) {
            return asset('https://chatsupport.co.in/public/ebooks/'.$value);
        }
        return '';
    }

    public function getOriginalImageAttribute()
    {
       return $this->attributes['image'];
    }

    public function getEbookAttribute($value) {
        if($value) {
            return asset('https://chatsupport.co.in/public/ebooks/'.$value);
        }
        return asset('https://chatsupport.co.in/public/no-image.png/');
    }

    public function getOriginalEbookAttribute()
    {
       return $this->attributes['ebook'];
    }
}
