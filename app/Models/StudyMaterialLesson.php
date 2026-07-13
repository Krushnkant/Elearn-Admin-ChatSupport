<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyMaterialLesson extends Model
{
    protected $table = 'study_material_lessons';

    protected $guarded = [];

    public function studyMaterial()
    {
        return $this->belongsTo(StudyMaterial::class, 'study_material_id');
    }

    /**
     * Public URL for the uploaded lesson file (null when none).
     */
    public function getFileUrlAttribute()
    {
        return $this->file ? url('public/study_materials/' . $this->file) : null;
    }
}
