<style type="text/css">
  .dropdown-menu li>a
  {
    color:black;
    text-decoration: none;
  }
  .dropdown-menu {
    width: 125px !important;
  }
</style>
<?php

  $tbl = 'questions';
  $id = $id;

  // A question can be orphaned if the assessment it pointed to was later
  // deleted — don't let that crash the whole list, just drop the Edit link.
  $assessmentId = is_array($assessment) ? ($assessment['id'] ?? null) : ($assessment->id ?? null);
  if ($assessmentId) {
    $editurl = url('admin/assessments/'.encode($assessmentId).'/questions/'.encode($id).'/edit/');
    $editButton = '<a type="" title="Edit" href="' . $editurl . '"><i class="fa fa-edit"></i>&nbsp;Edit</a>';
  } else {
    $editButton = '<a type="" title="No assessment linked" class="disabled" style="opacity:.5;pointer-events:none;"><i class="fa fa-edit"></i>&nbsp;Edit</a>';
  }

  $deleteButton = '<a data-title ="Confirmation" data-toggle="tooltip" data-placement="top" title="Delete Record"  onclick="questionDelete('. $id .')" href="javascript:void(0)" data-original-title="Delet"><i class="fa fa-trash"></i>&nbsp;Delete</a>';

?>


<div class="">
    <div class="btn-group">
      <button type="button" class="btn btn-primary btn-flat dropdown-toggle" data-toggle="dropdown">Actions <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu" style="margin: 0px 0 0;">
          <li class="action-button"><?php echo $editButton; ?></li> 
          <li class="action-button"><?php echo $deleteButton; ?></li> 
        </ul>
    </div>
  </div>