<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;


class Category extends Model
{
    use HasFactory;

    const DOMAIN = "Domain";
    const KNOWLEDGE = "Knowledge";
    const APPROACH = "Approach";

    
    public static function slug($title, $id = Null) {
        return (new self())->createSlug($title, $id);
    }

    public function getTypeAttribute($value)
    {
        
        switch ($value) {
            case $value == 1:
                return "Domain";
                break;
            case $value == 2:
                return "Knowledge";
                break;
            case $value == 3:
                return "Approach";
                break;
            
            default:
                return "Domain";
                break;
        }
    }


    public function getOriginalTypeAttribute()
    {
        return $this->attributes['type'];
    }

    public function createSlug($title, $id = Null)
    {
        $seo_name = Str::slug($title);
        $is_exists = $this->getRelatedSlugs($seo_name, $id);

        if($is_exists == 0) {
        return $seo_name;
        }

        for ($i = 1; $i <= 10; $i++) {
        $newSlug = $seo_name.'-'.$i;
        $unique = $this->getRelatedSlugs($newSlug, $id);
        if($unique == 0) {
            return $newSlug;
        }
        }
        throw new \Exception('Can not create a unique slug');
    }

    protected function getRelatedSlugs($seo_name, $id = Null)
    {
      $query = Self::query();
      if($id){
        $query->where('id','!=',$id);     
      }
      return $query->select('name')
                  ->where('name', $seo_name)
                  ->count();
    }

    public function question()
    {
        return $this->belongsToMany(Question::class,'category_id');
    }
 
    public function getCreatedAtAttribute($date) {
        return date('Y-m-d H:i:s', strtotime($date));
    }
    public function getUpdatedAtAttribute($date) {
        return date('Y-m-d H:i:s', strtotime($date));
    }
    
   
}
