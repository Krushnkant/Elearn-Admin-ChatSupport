<?php

namespace App\Imports;
use App\Models\{Question, Category, QuestionOption, Course, CategoryQuestion};
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class QuestionsImport implements ToCollection, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection  $rows)
    {
        \DB::beginTransaction();
        $i = 1;
        try {
             foreach ($rows as $key => $row) 
                {
                if(($row['set_type'] != null) && ($row['question'] != null) && ($row['levelone'] != null) && ($row['marks'] != null)) 
                {
                  
                  $is_course = Course::where('name', $row['levelone'])->select(['id'])->first();
                  $course_id = $is_course ? $is_course->id : NULL;

                    $created_at = isset($row['recordupdated']) ? date('Y-m-d H:i:s', strtotime($row['recordupdated'])) : date('Y-m-d H:i:s');
                        
                    if(!empty($row['leveltwo'])) {
                            $domain = explode(',', $row['leveltwo']);
                            $domain_info = [];
                            $is_knowledge_exists = Category::where('type', 1)
                                                            ->whereIn('name', $domain)
                                                            ->select(['id'])
                                                            ->get();
                            if(!empty($is_knowledge_exists)) {
                                foreach($is_knowledge_exists as $kn) {
                                    $domain_info[] = [
                                        'set_type'             => $row['set_type'],
                                        'category_id'   => 1,
                                         'course_id'         => $course_id,
                                        'sub_category_id'   => $kn->id,//$row['question_category'],
                                        'title'             => $row['question'],
                                        'question_type'     => $row['questiontype'] == 'SINGLE' ? 1 : 2,
                                        'marks'             => $row['marks'],
                                        'dificulty_level'   => $row['difficultylevel'],
                                        'explanation'       => $row['explanation'],
                                        'assessment_id'     => request()->assessment_id ? request()->assessment_id : $row['assessmentid'],
                                        'created_at'        => $created_at,
                                    ];
                                }
                                 Question::insert($domain_info);
                                // $insert_id = Question::insertGetId($domain_info);
                                 $insert_id1 = DB::getPDO()->lastInsertId();
                                 for($i = 1; $i <= $row['noofoptions']; $i++) {
                                    $que_options = [
                                        'question_id'   => $insert_id1,
                                        'options'       => $row['option'.$i],
                                        'image'         => isset($row['option'.$i.'img']) ?? Null,
                                        'is_correct'    => $row['correctoption'] == $i ? 1 : 0,
                                        'created_at'    => date('Y-m-d H:i:s'),
                                    ];
                                    QuestionOption::insert($que_options);
                                }
        
                            }  
                        }

                        if(!empty($row['levelthree'])) {
                            $knowledge = explode(',', $row['levelthree']);
                            $knowledge_info = [];
                            $is_knowledge_exists = Category::where('type', 2)
                                                            ->whereIn('name', $knowledge)
                                                            ->select(['id'])
                                                            ->get();
                            if(!empty($is_knowledge_exists)) {
                                foreach($is_knowledge_exists as $kn) {
                                    $knowledge_info[] = [
                                        'set_type'             => $row['set_type'],
                                        'category_id'   => 2,
                                        'course_id'         => $course_id,
                                       'sub_category_id'   => $kn->id,//$row['question_category'],
                                       'title'             => $row['question'],
                                       'question_type'     => $row['questiontype'] == 'SINGLE' ? 1 : 2,
                                       'marks'             => $row['marks'],
                                       'dificulty_level'   => $row['difficultylevel'],
                                       'explanation'       => $row['explanation'],
                                       'assessment_id'     => request()->assessment_id ? request()->assessment_id : $row['assessmentid'],
                                       'created_at'        => $created_at,
                                    ];
                                }
                                 Question::insert($knowledge_info);
                                 $insert_id2 = DB::getPDO()->lastInsertId();
                                 for($i = 1; $i <= $row['noofoptions']; $i++) {
                                    $que_options = [
                                        'question_id'   => $insert_id2,
                                        'options'       => $row['option'.$i],
                                        'image'         => isset($row['option'.$i.'img']) ?? Null,
                                        'is_correct'    => $row['correctoption'] == $i ? 1 : 0,
                                        'created_at'    => date('Y-m-d H:i:s'),
                                    ];
                                    QuestionOption::insert($que_options);
                                }
                            }
                        }

                      

                        if(!empty($row['question_category'])) {
                            $approach = explode(',', $row['question_category']);
                            $approach_info = [];
                            $is_approach_exists = Category::where('type', 3)
                                                            ->whereIn('name', $approach)
                                                            ->select(['id'])
                                                            ->get();
                            if(!empty($is_approach_exists)) {
                                foreach($is_approach_exists as $kn) {
                                    $approach_info[] = [
                                        'set_type'             => $row['set_type'],
                                        'category_id'   => 3,
                                        'course_id'         => $course_id,
                                       'sub_category_id'   => $kn->id,//$row['question_category'],
                                       'title'             => $row['question'],
                                       'question_type'     => $row['questiontype'] == 'SINGLE' ? 1 : 2,
                                       'marks'             => $row['marks'],
                                       'dificulty_level'   => $row['difficultylevel'],
                                       'explanation'       => $row['explanation'],
                                       'assessment_id'     => request()->assessment_id ? request()->assessment_id : $row['assessmentid'],
                                       'created_at'        => $created_at,
                                    ];
                                }
                                Question::insert($approach_info);
                            $insert_id3 = DB::getPDO()->lastInsertId();
                                 for($i = 1; $i <= $row['noofoptions']; $i++) {
                                    $que_options = [
                                        'question_id'   => $insert_id3,
                                        'options'       => $row['option'.$i],
                                        'image'         => isset($row['option'.$i.'img']) ?? Null,
                                        'is_correct'    => $row['correctoption'] == $i ? 1 : 0,
                                        'created_at'    => date('Y-m-d H:i:s'),
                                    ];
                                    QuestionOption::insert($que_options);
                                }
                            }
                        }

                        if(!empty($row['leveltwo'])) {
                            $domain = explode(',', $row['leveltwo']);
                           // $set_type = explode(',', $row['set_type']);
                            $domain_info = [];
                            $is_domain_exists = Category::where('type', 1)
                                                            ->where('name', $domain)
                                                            ->select(['id'])
                                                            ->get();
                            if(!empty($is_domain_exists)) {
                                foreach($is_domain_exists as $kn) {
                                    $domain_info[] = [

                                        'question_id'   => $insert_id1,
                                        'set_type'   => $row['set_type'],
                                        'category_id'   =>  1,
                                        'sub_category_id'   => $kn->id,
                                    ];
                                }
                                CategoryQuestion::insert($domain_info);
                            }
                        }


                        if(!empty($row['levelthree'])) {
                            $knowledge = explode(',', $row['levelthree']);
                           // $set_type = explode(',', $row['set_type']);
                            $knowledge_info = [];
                            $is_knowledge_exists = Category::where('type', 2)
                                                            ->whereIn('name', $knowledge)
                                                            ->select(['id'])
                                                            ->get();
                            if(!empty($is_knowledge_exists)) {
                                foreach($is_knowledge_exists as $kn) {
                                    $knowledge_info[] = [
                                        'question_id'   => $insert_id1,
                                        'set_type'   => $row['set_type'],
                                        'category_id'   =>  2,
                                        'sub_category_id'   => $kn->id,
                                    ];
                                }
                                CategoryQuestion::insert($knowledge_info);
                            }
                        }

                       
                        if(!empty($row['question_category'])) {
                            $approach = explode(',', $row['question_category']);
                         //   $set_type = explode(',', $row['set_type']);
                            $approach_info = [];
                            $is_approach_exists = Category::where('type', 3)
                                                            ->whereIn('name', $approach)
                                                            ->select(['id'])
                                                            ->get();
                            if(!empty($is_approach_exists)) {
                                foreach($is_approach_exists as $kn) {
                                    $approach_info[] = [
                                        'question_id'   => $insert_id1,
                                        'set_type'   => $row['set_type'],
                                        'category_id'   =>  3,
                                        'sub_category_id'   => $kn->id,
                                    ];
                                }
                                CategoryQuestion::insert($approach_info);
                            }
                        }
                        \DB::commit();
                }
            } 
        }
           catch (\Exception $e) {
            \DB::rollback();
            throw $e;
    }
}
    

    public function headingRow(): int
    {
        return 1;
    }

    public function createSlug($name, $id = Null)
    {
        $slug = \Str::slug($name);
        $is_exists = $this->getRelatedSlugs($slug, $id);

        if($is_exists == 0) {
          return $slug;
        }

        for ($i = 1; $i <= 10; $i++) {
          $newSlug = $slug.'-'.$i;
          $unique = $this->getRelatedSlugs($newSlug, $id);
          if($unique == 0) {
            return $newSlug;
          }
        }
        throw new \Exception('Can not create a unique slug');
      }

    protected function getRelatedSlugs($slug, $id = Null)
        {
            $query = Question::query();
            if($id){
            $query->where('id','!=',$id);     
        }
        return $query->select('slug')
                    ->where('slug', $slug)
                    ->count();
    }
}