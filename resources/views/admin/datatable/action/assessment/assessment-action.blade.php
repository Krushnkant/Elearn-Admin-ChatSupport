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

  $tbl = 'assessments';
  $id = $id;

  $editurl = url('admin/courses/'.encode($course['id']).'/assessments/'.encode($id).'/edit/');
  $questionUrl = url('admin/assessments/'.encode($id).'/questions');
  $editButton = '<a type="" style="text-decoration: none; color: black;" class="edit-link" title="Edit" href="' . $editurl . '"><i class="fa fa-edit"></i> Edit</a>';

  $deleteButton = '<a data-title ="Confirmation" data-toggle="tooltip" data-placement="top" title="Delete Record"  onclick="hardDelete('. $id .')" href="javascript:void(0)" data-original-title="Delet"><i class="fa fa-trash"></i> Delete</a>';

  $questionList = '<a type="" title="Questions" href="' . $questionUrl . '"><i class="fas fa-question-circle"></i> Questions</a>';

?>


<div class="">
    <div class="btn-group">
      <button type="button" class="btn btn-primary btn-flat dropdown-toggle" data-toggle="dropdown">Actions <span class="caret"></span></button>
        <ul class="dropdown-menu pull-center" role="menu" style="margin: 0px 0 0;">
          <li class="action-button"><?php echo $editButton; ?></li> 
          <li class="action-button"><?php echo $deleteButton; ?></li> 
          <li class="action-button"><?php echo $questionList; ?></li> 
        </ul>
    </div>
  </div>