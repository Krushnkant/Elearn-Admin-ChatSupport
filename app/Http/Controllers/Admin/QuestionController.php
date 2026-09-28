<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator, Session, Redirect, Response, DB, Config, File;
use App\Models\{Question, Category, Course, QuestionOption, CategoryQuestion, Assessment};
use DataTables;
use App\Imports\QuestionsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Exception;
use Illuminate\Database\QueryException;

class QuestionController extends Controller
{
  public function index(Request $request, $assessment_id)
  {
    $data['title'] = "Question List";
    $data['assessment_id'] = $assessment_id;
    if ($request->ajax())
    {
      $data = Question::where('assessment_id', decode($assessment_id))->with(['course','category:id,name,type', 'assessment'])->orderBy('id', 'desc');
                return Datatables::of($data)
                ->editColumn('created_at', function($data){
                  return date(Config::get('constants.DATE_FORMAT'), strtotime($data->created_at));
                })
                ->addColumn('action', 'admin.datatable.action.question.question-action')
                ->editColumn('status', 'admin.datatable.status.category-status')
                ->rawColumns(['status', 'action'])
                ->addIndexColumn()
                ->make(true);
    }

    return view('admin.question.index', $data);
  }

  public function create(Request $request, $assessment_id)
  {
    $data['title'] = "Create Question";
    $data['category_info'] = Category::get();
    $data['assessment_id'] = $assessment_id;
    $data['course_info'] = Course::get();
    return view('admin.question.add', $data);
  }

  public function store(Request $request)
  {

    $course = Assessment::where('id', decode($request->get('assessment_id')))->select(['course_id'])->first();
    $request->validate([
      'set_type'       => 'required',
      'domain_id'       => 'required',
      'knowledge_id'      => 'required',
      'approach_id'       => 'required',
      'title'             => 'required',
      'explanation'       => 'required',
      'dificulty_level'   => 'required',
      'marks'             => 'required',
      'question_type'     => 'required',
      'options.*'         => 'required',
    ],
    [
      'category_id.required'    => " The category field is required.",
      'knowledge_id.required'   => " The knowledge field is required.",
      'approach_id.required'    => " The approach field is required.",
      'options.*.required'      => " The options field is required.",
    ]);

    // Extra question metadata merged into every fanned-out question row.
    $extra = [
      'process_group' => $request->get('process_group'),
    ];
    $whyWrong   = $request->get('why_wrong', []);
    $whyCorrect = $request->get('why_correct', []);
    $addExtra = function ($rows) use ($extra) {
      return array_map(function ($r) use ($extra) { return array_merge($r, $extra); }, $rows);
    };

    \DB::beginTransaction();
    try {


      if(!empty($request->get('domain_id'))) {

        $domain = $request->get('domain_id');
        
        $domain_info = [];
        $is_domain_exists = Category::where('type', 1)
                                        ->whereIn('id', $domain)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_domain_exists)) {
            foreach($is_domain_exists as $kn) {
                $domain_info[] = [
                    'set_type'             => $request->get('set_type'),
                   'category_id'   =>  1,
                   'sub_category_id'   =>  $kn->id,
                    'course_id'         => $course->course_id,
                    'assessment_id'     => decode($request->get('assessment_id')),
                    'marks'             => $request->get('marks'),
                    'title'             => $request->get('title'),
                    'explanation'       => $request->get('explanation'),
                    'dificulty_level'   => $request->get('dificulty_level'),
                    
                    'question_type'     => $request->get('question_type'),
                    'status'            => !empty($request->get('status')) ? 1 : 0,
                    'created_at'        => date('Y-m-d H:i:s'),
                ];
               // Question::insert($domain_info);
                
            }
         
            Question::insert($addExtra($domain_info));
            // $insert_id = Question::insertGetId($domain_info);
             $insert_id1 = DB::getPDO()->lastInsertId();
          // dd ($domain_info); 
            //$insert_id1 = Question::insertGetId($domain_info);
            
            if(!empty($request->get('options'))) {
              $options = $request->get('options');
              $image = $request->file('image');
              $is_correct = $request->get('is_correct');
              foreach($options as $key => $d) {
                $que_options = [
                  'question_id'   => $insert_id1,
                  'options'       => $d,
                  'is_correct'    => $is_correct[$key] == 1 ? 1 : 0,
                  'why_wrong'     => isset($whyWrong[$key]) ? $whyWrong[$key] : null,
                  'why_correct'   => isset($whyCorrect[$key]) ? $whyCorrect[$key] : null,
                  'created_at'    => date('Y-m-d H:i:s'),
                ];
      
                if(isset($request->file('image')[$key])) {
                  $files = $request->file('image');
                  $destinationPath = 'public/options/'; // upload path
                  $rand = md5(time() . mt_rand(100000000, 999999999));
                  $profileImage = $rand . "." . @$files[$key]->getClientOriginalExtension();
                  $files[$key]->move($destinationPath, $profileImage);
                  $que_options['image'] = $profileImage;
                }
                QuestionOption::insert($que_options);
              }
            }
      
      
          //CategoryQuestion::insert($knowledge_info);
        }
      }

      if(!empty($request->get('knowledge_id'))) {
        $knowledge = $request->get('knowledge_id');
     
        $knowledge_info = [];
        $is_knowledge_exists = Category::where('type', 2)
                                        ->whereIn('id', $knowledge)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_knowledge_exists)) {
            foreach($is_knowledge_exists as $kn) {
                $knowledge_info[] = [
                  'set_type'             => $request->get('set_type'),
                  'category_id'   =>  2,
                  'sub_category_id'   => $kn->id,
                    'course_id'         => $course->course_id,
                    'assessment_id'     => decode($request->get('assessment_id')),
                    'marks'             => $request->get('marks'),
                    'title'             => $request->get('title'),
                    'explanation'       => $request->get('explanation'),
                    'dificulty_level'   => $request->get('dificulty_level'),
                   
                    'question_type'     => $request->get('question_type'),
                    'status'            => !empty($request->get('status')) ? 1 : 0,
                    'created_at'        => date('Y-m-d H:i:s'),
                ];
                //Question::insert($knowledge_info);
            }
            Question::insert($addExtra($knowledge_info));
            // $insert_id = Question::insertGetId($domain_info);
             $insert_id2 = DB::getPDO()->lastInsertId();
            //$insert_id1 =  Question::insertGetId($knowledge_info);
            if(!empty($request->get('options'))) {
              $options = $request->get('options');
              $image = $request->file('image');
              $is_correct = $request->get('is_correct');
              foreach($options as $key => $d) {
                $que_options = [
                  'question_id'   => $insert_id2,
                  'options'       => $d,
                  'is_correct'    => $is_correct[$key] == 1 ? 1 : 0,
                  'why_wrong'     => isset($whyWrong[$key]) ? $whyWrong[$key] : null,
                  'why_correct'   => isset($whyCorrect[$key]) ? $whyCorrect[$key] : null,
                  'created_at'    => date('Y-m-d H:i:s'),
                ];
      
                if(isset($request->file('image')[$key])) {
                  $files = $request->file('image');
                  $destinationPath = 'public/options/'; // upload path
                  $rand = md5(time() . mt_rand(100000000, 999999999));
                  $profileImage = $rand . "." . @$files[$key]->getClientOriginalExtension();
                  $files[$key]->move($destinationPath, $profileImage);
                  $que_options['image'] = $profileImage;
                }
                QuestionOption::insert($que_options);
              }
            }
      
      
          //CategoryQuestion::insert($knowledge_info);
        }
      }

      if(!empty($request->get('approach_id'))) {
        $approach = $request->get('approach_id');
      
        $approach_info = [];
        $is_approach_exists = Category::where('type', 3)
                                        ->whereIn('id', $approach)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_approach_exists)) {
            foreach($is_approach_exists as $kn) {
                $approach_info[] = [
                  'set_type'             => $request->get('set_type'),
                  'category_id'   =>  3,
                  'sub_category_id'   => $kn->id,
                  'course_id'         => $course->course_id,
                  'assessment_id'     => decode($request->get('assessment_id')),
                  'marks'             => $request->get('marks'),
                  'title'             => $request->get('title'),
                  'explanation'       => $request->get('explanation'),
                  'dificulty_level'   => $request->get('dificulty_level'),
                  
                  'question_type'     => $request->get('question_type'),
                  'status'            => !empty($request->get('status')) ? 1 : 0,
                  'created_at'        => date('Y-m-d H:i:s'),
                ];
               // Question::insert($approach_info);
            }
            Question::insert($addExtra($approach_info));
            // $insert_id = Question::insertGetId($domain_info);
             $insert_id3 = DB::getPDO()->lastInsertId();
            // $insert_id1 =  Question::insertGetId($approach_info);
            if(!empty($request->get('options'))) {
              $options = $request->get('options');
              $image = $request->file('image');
              $is_correct = $request->get('is_correct');
              foreach($options as $key => $d) {
                $que_options = [
                  'question_id'   => $insert_id3,
                  'options'       => $d,
                  'is_correct'    => $is_correct[$key] == 1 ? 1 : 0,
                  'why_wrong'     => isset($whyWrong[$key]) ? $whyWrong[$key] : null,
                  'why_correct'   => isset($whyCorrect[$key]) ? $whyCorrect[$key] : null,
                  'created_at'    => date('Y-m-d H:i:s'),
                ];
      
                if(isset($request->file('image')[$key])) {
                  $files = $request->file('image');
                  $destinationPath = 'public/options/'; // upload path
                  $rand = md5(time() . mt_rand(100000000, 999999999));
                  $profileImage = $rand . "." . @$files[$key]->getClientOriginalExtension();
                  $files[$key]->move($destinationPath, $profileImage);
                  $que_options['image'] = $profileImage;
                }
                QuestionOption::insert($que_options);
              }
            }
          //CategoryQuestion::insert($approach_info);
        }
      }

      if(!empty($request->get('domain_id'))) {
        $domain = $request->get('domain_id');
        $knowledge_info = [];
        $is_knowledge_exists = Category::where('type', 1)
                                        ->whereIn('id', $domain)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_knowledge_exists)) {
            foreach($is_knowledge_exists as $kn) {
                $knowledge_info[] = [
                  'question_id'   => $insert_id1,
                  'set_type'   =>  $request->get('set_type'),
                    'category_id'   =>1,
                    'sub_category_id'   => $kn->id,
                    
                ];
            }
          CategoryQuestion::insert($knowledge_info);
        }
      }
      if(!empty($request->get('knowledge_id'))) {
        $knowledge = $request->get('knowledge_id');
        $knowledge_info = [];
        $is_knowledge_exists = Category::where('type', 2)
                                        ->whereIn('id', $knowledge)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_knowledge_exists)) {
            foreach($is_knowledge_exists as $kn) {
                $knowledge_info[] = [
                  'question_id'   => $insert_id1,
                  'set_type'   =>  $request->get('set_type'),
                  'category_id'   => 2,
                    'sub_category_id'   => $kn->id,
                ];
            }
          CategoryQuestion::insert($knowledge_info);
        }
      }

      if(!empty($request->get('approach_id'))) {
        $approach = $request->get('approach_id');
        $approach_info = [];
        $is_approach_exists = Category::where('type', 3)
                                        ->whereIn('id', $approach)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_approach_exists)) {
            foreach($is_approach_exists as $kn) {
                $approach_info[] = [
                  'question_id'   => $insert_id1,
                  'set_type'   =>  $request->get('set_type'),
                    'category_id'   =>3,
                    'sub_category_id'   => $kn->id,
                ];
            }
          CategoryQuestion::insert($approach_info);
        }
      }
      \DB::commit();
      if($insert_id1) {
      return Redirect::to("admin/assessments/".$request->get('assessment_id')."/questions")->withSuccess("Great! Info has been added");
    } else {
      return Redirect::to("admin/assessments/".$request->get('assessment_id')."/questions/create")->withWarning("Oops! Something went wrong");
    }
    } catch (Exception $e) {
      \DB::rollback();
      throw $e;
    }
    
  }


  public function getImportCSV(Request $request, $assessment_id)
  {
    $data['title'] = "Import CSV";
    $data['assessment_id'] = decode($assessment_id);
    return view('admin.question.import-csv', $data);
  }


  /**
   * Header names (case/space-insensitive) for the mock-test question import
   * format, mapped to a short key used while building the insert rows.
   */
  const IMPORT_HEADER_MAP = [
    'question id'                                  => 'external_id',
    'question'                                      => 'title',
    'option a'                                       => 'option_a',
    'option b'                                       => 'option_b',
    'option c'                                       => 'option_c',
    'option d'                                       => 'option_d',
    'correct answer'                                 => 'correct_answer',
    'correct answer rationale'                       => 'rationale',
    'why a is incorrect'                             => 'why_a',
    'why b is incorrect'                             => 'why_b',
    'why c is incorrect'                             => 'why_c',
    'why d is incorrect'                             => 'why_d',
    'why option a is incorrect'                      => 'why_a',
    'why option b is incorrect'                      => 'why_b',
    'why option c is incorrect'                      => 'why_c',
    'why option d is incorrect'                      => 'why_d',
    'pmp domain'                                     => 'domain',
    'eco task'                                       => 'eco_task',
    'topic category'                                 => 'topic_category',
    'tested skill / topic'                           => 'tested_skill',
    'methodology'                                    => 'methodology',
    'question type'                                  => 'question_style',
    'difficulty'                                     => 'difficulty',
    'cognitive level'                                => 'cognitive_level',
    'pmbok topic / knowledge area'                   => 'knowledge_area',
    'predictive process group / lifecycle stage'     => 'process_group',
    'pmbok reference'                                => 'pmbok_ref',
    'agile reference'                                => 'agile_ref',
    'pmi exam tip'                                    => 'exam_tip',
    'exam trap'                                      => 'exam_trap',
    'last reviewed'                                  => 'last_reviewed',
  ];

  /** Normalise a header cell for matching against IMPORT_HEADER_MAP. */
  private function normalizeHeader($v)
  {
    return trim(preg_replace('/\s+/', ' ', strtolower((string) $v)));
  }

  /** A-D letters -> option column keys, in display order. */
  const IMPORT_OPTION_LETTERS = ['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d'];

  /** Excel serial date (or literal date string) -> Y-m-d, best-effort. */
  private function importDate($val)
  {
    if ($val === null || $val === '') {
      return null;
    }
    if (is_numeric($val)) {
      try {
        return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('Y-m-d');
      } catch (\Exception $e) {
        return null;
      }
    }
    $ts = strtotime((string) $val);
    return $ts ? date('Y-m-d', $ts) : null;
  }

  /**
   * Bulk-import questions from the mock-test question workbook (see
   * IMPORT_HEADER_MAP for the expected columns). Every row becomes one
   * question tied to the assessment picked on the import page, with its
   * options and the full PMP metadata (domain, methodology, difficulty,
   * cognitive level, ECO task, references, tips, etc.) that the mock test
   * builder and exam runner already read.
   */
  public function postImportCSV(Request $request)
  {
    $request->validate([
      'question_csv' => 'required|file',
    ]);

    // getImportCSV() already decoded the id once for the hidden field, so the
    // posted value is the plain assessment id — decoding it again here would
    // garble it (and silently insert every question with assessment_id = 0).
    $assessmentId = (int) $request->get('assessment_id');
    if ($assessmentId <= 0) {
      \Log::warning('postImportCSV: could not resolve assessment id', [
        'raw_posted_value' => $request->get('assessment_id'),
        'referer'          => $request->headers->get('referer'),
        'all_input_keys'   => array_keys($request->all()),
      ]);
      return Redirect::back()->withErrors(['question_csv' => 'Could not tell which assessment to import into — please reopen the Import CSV page from that assessment\'s question list and try again.']);
    }
    $assessment = Assessment::where('id', $assessmentId)->select(['course_id'])->first();
    if (!$assessment) {
      return Redirect::back()->withErrors(['question_csv' => 'This assessment no longer exists — please reopen the Import CSV page from the assessment list and try again.']);
    }

    $file      = $request->file('question_csv');
    $storedPath = $file->storeAs('temp', time() . '_' . $file->getClientOriginalName());
    $fullPath   = storage_path('app') . '/' . $storedPath;

    try {
      $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fullPath);
    } catch (\Exception $e) {
      return Redirect::back()->withErrors(['question_csv' => 'That file could not be read as an Excel/CSV workbook. Please upload the sample file format (download it via the link below) filled in with your questions.']);
    }
    $sheet       = $spreadsheet->getActiveSheet();
    $highestRow  = $sheet->getHighestRow();
    $highestCol  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());

    // Header row -> column-letter map, keyed by our short field names.
    $colByKey = [];
    for ($c = 1; $c <= $highestCol; $c++) {
      $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
      $header = $this->normalizeHeader($sheet->getCell($letter . '1')->getValue());
      if (isset(self::IMPORT_HEADER_MAP[$header])) {
        $colByKey[self::IMPORT_HEADER_MAP[$header]] = $letter;
      }
    }

    // Every column the importer relies on to build a usable question; if any
    // are missing, this isn't the expected sheet — say so instead of guessing.
    $requiredKeys = ['title', 'option_a', 'option_b', 'correct_answer', 'domain'];
    $missingKeys  = array_diff($requiredKeys, array_keys($colByKey));
    if ($missingKeys) {
      $missingLabels = array_keys(array_intersect(self::IMPORT_HEADER_MAP, $missingKeys));
      return Redirect::back()->withErrors([
        'question_csv' => 'Wrong format: this file is missing the "' . implode('", "', array_map('ucwords', $missingLabels)) . '" column(s) the mock test question sheet needs. Please use the sample file format (download it via the link below).',
      ]);
    }

    $cell = function ($row, $key) use ($sheet, $colByKey) {
      return isset($colByKey[$key]) ? trim((string) $sheet->getCell($colByKey[$key] . $row)->getValue()) : '';
    };

    $imported = 0;

    \DB::beginTransaction();
    try {
      for ($row = 2; $row <= $highestRow; $row++) {
        $title         = $cell($row, 'title');
        $correctAnswer = $cell($row, 'correct_answer');
        if ($title === '' || $correctAnswer === '') {
          continue;
        }

        $domainName = $cell($row, 'domain');
        $domainCat  = $domainName !== '' ? Category::where('type', 1)->where('name', $domainName)->first() : null;
        $domainId   = $domainCat->id ?? 0;

        $correctLetters = array_values(array_filter(array_map('trim', preg_split('/[,\/\s]+/', strtoupper($correctAnswer)))));

        $questionRow = [
          'external_id'      => $cell($row, 'external_id') ?: null,
          'set_type'          => 'set1',
          'category_id'       => 1,
          'categoryid'        => $domainId,
          'sub_category_id'   => $domainId,
          'course_id'         => $assessment->course_id ?? 1,
          'assessment_id'     => $assessmentId,
          'marks'             => 1,
          'title'             => $title,
          'explanation'       => $cell($row, 'rationale'),
          'question_type'     => count($correctLetters) > 1 ? 2 : 1,
          'question_style'    => $cell($row, 'question_style') ?: null,
          'dificulty_level'   => $cell($row, 'difficulty') ?: null,
          'process_group'     => $cell($row, 'process_group') ?: null,
          'methodology'       => $cell($row, 'methodology') ?: null,
          'cognitive_level'   => $cell($row, 'cognitive_level') ?: null,
          'topic_category'    => $cell($row, 'topic_category') ?: null,
          'tested_skill'      => $cell($row, 'tested_skill') ?: null,
          'pmbok_ref'         => $cell($row, 'pmbok_ref') ?: null,
          'agile_ref'         => $cell($row, 'agile_ref') ?: null,
          'exam_tip'          => $cell($row, 'exam_tip') ?: null,
          'exam_trap'         => $cell($row, 'exam_trap') ?: null,
          'eco_task'          => $cell($row, 'eco_task') ?: null,
          'last_reviewed'     => $this->importDate($cell($row, 'last_reviewed')),
          'status'            => 1,
          'created_at'        => date('Y-m-d H:i:s'),
        ];
        DB::table('questions')->insert($questionRow);
        $questionId = DB::getPdo()->lastInsertId();

        if ($domainId) {
          DB::table('category_questions')->insert([
            'question_id'     => $questionId,
            'set_type'        => 'set1',
            'category_id'     => 1,
            'sub_category_id' => $domainId,
          ]);
        }

        foreach (self::IMPORT_OPTION_LETTERS as $letter => $key) {
          $optionText = $cell($row, $key);
          if ($optionText === '') {
            continue;
          }
          $isCorrect = in_array($letter, $correctLetters, true);
          DB::table('question_options')->insert([
            'question_id' => $questionId,
            'options'     => $optionText,
            'is_correct'  => $isCorrect ? 1 : 0,
            'why_correct' => $isCorrect ? ($cell($row, 'rationale') ?: null) : null,
            'why_wrong'   => !$isCorrect ? ($cell($row, 'why_' . strtolower($letter)) ?: null) : null,
            'created_at'  => date('Y-m-d H:i:s'),
          ]);
        }

        $imported++;
      }
      \DB::commit();
    } catch (Exception $e) {
      \DB::rollback();
      throw $e;
    }

    if ($imported === 0) {
      return Redirect::back()->withErrors(['question_csv' => 'Wrong format: the column headers matched, but no row had both a Question and a Correct Answer filled in. Please check the file against the sample format.']);
    }

    return Redirect::to("admin/assessments/" . encode($assessmentId) . "/questions")
      ->withSuccess("Great! {$imported} question(s) have been imported.");
  }

  public function hardDelete(Request $request){
    $id = $request->get('id');
    $where = array('id' => $id);
    $question_info = Question::where('id', $id)->first();
    $question_info->questionOptions()->delete();
    $question_info->categoryQuestion()->delete();
    $result = $question_info->delete();
    if ($result) {
      die('1');
    } else {
      die('0');
    }
  }

  public function edit(Request $request, $assessment_id, $id)
  {
    $data['title'] = "Edit Question";
    $data['category_info'] = Category::get();
    $data['course_info'] = Course::get();
    $data['assessment_id'] = $assessment_id;
    $data['id'] = $id;
    $data['question_info'] = Question::where('id', decode($id))->with(['questionOptions', 'categoryQuestion'])->first();
    return view('admin.question.edit', $data);
  }

  public function update(Request $request)
  {
    //dd($request->all());
    $id = $request->get('id');
    $course = Assessment::where('id', decode($request->get('assessment_id')))->select(['course_id'])->first();
    //dd($course);
    $request->validate([
   //   'domain_id'       => 'required',
    //  'knowledge_id'       => 'required',
   //   'approach_id'       => 'required',
      'title'             => 'required',
      'explanation'       => 'required',
      'dificulty_level'   => 'required',
      'marks'             => 'required',
      'question_type'     => 'required',
    ]);

    // Extra question metadata merged into the update, + per-option why_wrong/why_correct.
    $extra = [
      'process_group' => $request->get('process_group'),
    ];
    $whyWrong   = $request->get('why_wrong', []);
    $whyCorrect = $request->get('why_correct', []);

    \DB::beginTransaction();
    try {

       if(!isset($request->domain_id) && !isset($request->knowledge_id) && !isset($request->approach_id) && !isset($request->knowledge_id)){

      
      
     
                $domain_info = [
                   'category_id'   =>  1,
                   //'sub_category_id'   =>  $kn->id,
                    'sub_category_id'   =>  0,
                    'course_id'         => $course->course_id,
                    'assessment_id'     => decode($request->get('assessment_id')),
                    'marks'             => $request->get('marks'),
                    'title'             => $request->get('title'),
                    'explanation'       => $request->get('explanation'),
                    'dificulty_level'   => $request->get('dificulty_level'),
                    
                    'question_type'     => $request->get('question_type'),
                    'status'            => !empty($request->get('status')) ? 1 : 0,
                    'created_at'        => date('Y-m-d H:i:s'),
                ];
               // Question::insert($domain_info);
                
         
            //Question::insert($domain_info);
            $update =  Question::where('id', $id)->update(array_merge($domain_info, $extra));
            
         
             if(!empty($request->get('options'))) {
              $option_id = $request->get('options_id');
              $options = $request->get('options');
              $is_correct = $request->get('is_correct');
              foreach($options as $key => $d) {
                $que_options = [
                  'question_id'   => $id,
                  'options'       => $d,
                  'is_correct'    => $is_correct[$key] == 1 ? 1 : 0,
                  'why_wrong'     => isset($whyWrong[$key]) ? $whyWrong[$key] : null,
                  'why_correct'   => isset($whyCorrect[$key]) ? $whyCorrect[$key] : null,
                  'created_at'    => date('Y-m-d H:i:s'),
                ];
                if(isset($request->file('image')[$key])) {
                  $files = $request->file('image');
                    $destinationPath = 'public/options/'; // upload path
                    $rand = md5(time() . mt_rand(100000000, 999999999));
                    $profileImage = $rand . "." . @$files[$key]->getClientOriginalExtension();
                    $files[$key]->move($destinationPath, $profileImage);
                    $que_options['image'] = $profileImage;
                }
                QuestionOption::updateOrCreate(['id' => $option_id[$key]], $que_options);
              }
            }
      

       }


      if(isset($request->domain_id) && !empty($request->get('domain_id'))) {
         
        $domain = $request->get('domain_id');
        
        $domain_info = "";
        $is_domain_exists = Category::where('type', 1)
                                        ->whereIn('id', $domain)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_domain_exists)) {
          $cat_id = [];
            foreach($is_domain_exists as $kn) {
              if(isset($kn->id)){
               $kn_id =  $kn->id;
              }else{
                $kn_id = 0;
              }
              //$cat_id[] = $kn->id;
              $cat_id[] = $kn_id;
                $domain_info = [
                   'category_id'   =>  1,
                   //'sub_category_id'   =>  $kn->id,
                    'sub_category_id'   =>  $kn_id,
                    'course_id'         => $course->course_id,
                    'assessment_id'     => decode($request->get('assessment_id')),
                    'marks'             => $request->get('marks'),
                    'title'             => $request->get('title'),
                    'explanation'       => $request->get('explanation'),
                    'dificulty_level'   => $request->get('dificulty_level'),
                    
                    'question_type'     => $request->get('question_type'),
                    'status'            => !empty($request->get('status')) ? 1 : 0,
                    'created_at'        => date('Y-m-d H:i:s'),
                ];
               // Question::insert($domain_info);
                
            }
         
            //Question::insert($domain_info);
            $update =  Question::where('id', $id)->update(array_merge($domain_info, $extra));
            
             
          
            
             if(!empty($request->get('options'))) {
              $option_id = $request->get('options_id');
              $options = $request->get('options');
              $is_correct = $request->get('is_correct');
              foreach($options as $key => $d) {
                $que_options = [
                  'question_id'   => $id,
                  'options'       => $d,
                  'is_correct'    => $is_correct[$key] == 1 ? 1 : 0,
                  'why_wrong'     => isset($whyWrong[$key]) ? $whyWrong[$key] : null,
                  'why_correct'   => isset($whyCorrect[$key]) ? $whyCorrect[$key] : null,
                  'created_at'    => date('Y-m-d H:i:s'),
                ];
                if(isset($request->file('image')[$key])) {
                  $files = $request->file('image');
                    $destinationPath = 'public/options/'; // upload path
                    $rand = md5(time() . mt_rand(100000000, 999999999));
                    $profileImage = $rand . "." . @$files[$key]->getClientOriginalExtension();
                    $files[$key]->move($destinationPath, $profileImage);
                    $que_options['image'] = $profileImage;
                }
                QuestionOption::updateOrCreate(['id' => $option_id[$key]], $que_options);
              }
            }
      
      
      
          //CategoryQuestion::insert($knowledge_info);
        }
      }

      if(isset($request->knowledge_id) && !empty($request->get('knowledge_id'))) {
        $knowledge = $request->get('knowledge_id');
     
        $knowledge_info = "";
        $is_knowledge_exists = Category::where('type', 2)
                                        ->whereIn('id', $knowledge)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_knowledge_exists)) {
          $cat_id = [];
            foreach($is_knowledge_exists as $kn) {
              if(isset($kn->id)){
               $kn_id =  $kn->id;
              }else{
                $kn_id = 0;
              }
              $cat_id[] = $kn_id;
                $knowledge_info = [
                  'category_id'   =>  2,
                  'sub_category_id'   => $kn_id,
                    'course_id'         => $course->course_id,
                    'assessment_id'     => decode($request->get('assessment_id')),
                    'marks'             => $request->get('marks'),
                    'title'             => $request->get('title'),
                    'explanation'       => $request->get('explanation'),
                    'dificulty_level'   => $request->get('dificulty_level'),
                   
                    'question_type'     => $request->get('question_type'),
                    'status'            => !empty($request->get('status')) ? 1 : 0,
                    'created_at'        => date('Y-m-d H:i:s'),
                ];
                //Question::insert($knowledge_info);
            }
            $update =  Question::where('id', $id)->update(array_merge($knowledge_info, $extra));
            
            
            
             if(!empty($request->get('options'))) {
              $option_id = $request->get('options_id');
              $options = $request->get('options');
              $is_correct = $request->get('is_correct');
              foreach($options as $key => $d) {
                $que_options = [
                  'question_id'   => $id,
                  'options'       => $d,
                  'is_correct'    => $is_correct[$key] == 1 ? 1 : 0,
                  'why_wrong'     => isset($whyWrong[$key]) ? $whyWrong[$key] : null,
                  'why_correct'   => isset($whyCorrect[$key]) ? $whyCorrect[$key] : null,
                  'created_at'    => date('Y-m-d H:i:s'),
                ];
                if(isset($request->file('image')[$key])) {
                  $files = $request->file('image');
                    $destinationPath = 'public/options/'; // upload path
                    $rand = md5(time() . mt_rand(100000000, 999999999));
                    $profileImage = $rand . "." . @$files[$key]->getClientOriginalExtension();
                    $files[$key]->move($destinationPath, $profileImage);
                    $que_options['image'] = $profileImage;
                }
                QuestionOption::updateOrCreate(['id' => $option_id[$key]], $que_options);
              }
            }
      
      
      
          //CategoryQuestion::insert($knowledge_info);
        }
      }

      if(isset($request->approach_id) && !empty($request->get('approach_id'))) {
        $approach = $request->get('approach_id');
      
        $approach_info = "";
        $is_approach_exists = Category::where('type', 3)
                                        ->whereIn('id', $approach)
                                        ->select(['id'])
                                        ->get();
        if(!empty($is_approach_exists)) {
          $cat_id = [];
            foreach($is_approach_exists as $kn) {
              if(isset($kn->id)){
               $kn_id =  $kn->id;
              }else{
                $kn_id = 0;
              }
              $cat_id[] = $kn_id;
                $approach_info = [
                  'category_id'   =>  3,
                  'sub_category_id'   => $kn_id,
                  'course_id'         => $course->course_id,
                  'assessment_id'     => decode($request->get('assessment_id')),
                  'marks'             => $request->get('marks'),
                  'title'             => $request->get('title'),
                  'explanation'       => $request->get('explanation'),
                  'dificulty_level'   => $request->get('dificulty_level'),
                  
                  'question_type'     => $request->get('question_type'),
                  'status'            => !empty($request->get('status')) ? 1 : 0,
                  'created_at'        => date('Y-m-d H:i:s'),
                ];
               // Question::insert($approach_info);
            }
            $update =    Question::where('id', $id)->update(array_merge($approach_info, $extra));
            
            if(!empty($request->get('options'))) {
              $option_id = $request->get('options_id');
              $options = $request->get('options');
              $is_correct = $request->get('is_correct');
              foreach($options as $key => $d) {
                $que_options = [
                  'question_id'   => $id,
                  'options'       => $d,
                  'is_correct'    => $is_correct[$key] == 1 ? 1 : 0,
                  'why_wrong'     => isset($whyWrong[$key]) ? $whyWrong[$key] : null,
                  'why_correct'   => isset($whyCorrect[$key]) ? $whyCorrect[$key] : null,
                  'created_at'    => date('Y-m-d H:i:s'),
                ];
                if(isset($request->file('image')[$key])) {
                  $files = $request->file('image');
                    $destinationPath = 'public/options/'; // upload path
                    $rand = md5(time() . mt_rand(100000000, 999999999));
                    $profileImage = $rand . "." . @$files[$key]->getClientOriginalExtension();
                    $files[$key]->move($destinationPath, $profileImage);
                    $que_options['image'] = $profileImage;
                }
                QuestionOption::updateOrCreate(['id' => $option_id[$key]], $que_options);
              }
            }
      
          //CategoryQuestion::insert($approach_info);
        }
      }

      

      if(isset($request->domain_id) && !empty($request->get('domain_id'))) {
        $domain = $request->get('domain_id');
        $domain_info = "";
        $is_domain_exists = Category::where('type', 2)
                                        ->whereIn('id', $domain)
                                        ->select(['id'])
                                        ->get();
                                        //dd(count($is_knowledge_exists));
        if(!empty($is_domain_exists)) {
          $cat_id = [];
          foreach($is_domain_exists as $key => $kn) {
            if(isset($kn->id)){
             $kn_id =  $kn->id;
            }else{
              $kn_id = 0;
            }
            $cat_id[] = $kn_id;
            $domain_info = [
              'category_id'   => $kn_id,
              'question_id'   => $id,
            ];
            CategoryQuestion::updateOrCreate(['question_id' => $id, 'category_id' => $kn_id], $domain_info);
          }
        }
      }

      if(isset($request->knowledge_id) && !empty($request->get('knowledge_id'))) {
        $knowledge = $request->get('knowledge_id');
        $knowledge_info = "";
        $is_knowledge_exists = Category::where('type', 2)
                                        ->whereIn('id', $knowledge)
                                        ->select(['id'])
                                        ->get();
                                        //dd(count($is_knowledge_exists));
        if(!empty($is_knowledge_exists)) {
          if(isset($kn->id)){
             $kn_id =  $kn->id;
            }else{
              $kn_id = 0;
            }
          $cat_id = [];$cat_id[] = $kn_id;
          foreach($is_knowledge_exists as $key => $kn) {
            if(isset($kn->id)){
             $kn_id =  $kn->id;
            }else{
              $kn_id = 0;
            }
            $cat_id[] = $kn_id;
            $knowledge_info = [
              'category_id'   => $kn_id,
              'question_id'   => $id,
            ];
            CategoryQuestion::updateOrCreate(['question_id' => $id, 'category_id' => $kn_id], $knowledge_info);
          }
        }
      }

      if(isset($request->approach_id) && !empty($request->get('approach_id'))) {
        $approach = $request->get('approach_id');
        $approach_info = "";
        $is_approach_exists = Category::where('type', 3)
                                        ->whereIn('id', $approach)
                                        ->select(['id'])
                                        ->get();
                                        //dd(count($is_knowledge_exists));
        if(!empty($is_approach_exists)) {
            $cat_id = [];
            foreach($is_approach_exists as $key => $kn) {
              if(isset($kn->id)){
               $kn_id =  $kn->id;
              }else{
                $kn_id = 0;
              }
              $cat_id[] = $kn_id;
              $approach_info = [
                'category_id'   => $kn_id,
                'question_id'   => $id,
              ];
              CategoryQuestion::updateOrCreate(['question_id' => $id, 'category_id' => $kn_id], $approach_info);
            }
        }
      }

      \DB::commit();
    //  if($update) {
        return Redirect::to("admin/assessments/".$request->get('assessment_id')."/questions")->withSuccess("Great! Info has been updated");
    //  } else {
    //    return Redirect::to("admin/assessments/".$request->get('assessment_id')."/questions/".$request->get('id')."edit")->withWarning("Oops! Something went wrong");
   //   }
    } catch (Exception $e) {
      \DB::rollback();
      throw $e;
    }
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

