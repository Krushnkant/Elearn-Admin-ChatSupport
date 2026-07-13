<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyMaterial extends Model
{
    protected $table = 'study_materials';

    protected $guarded = [];

    public function lessons()
    {
        return $this->hasMany(StudyMaterialLesson::class, 'study_material_id')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc');
    }

    public function activeLessons()
    {
        return $this->lessons()->where('status', 1);
    }
}
