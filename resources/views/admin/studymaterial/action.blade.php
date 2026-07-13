<style type="text/css">
  .dropdown-menu li>a { color:black; text-decoration: none; }
  .dropdown-menu { width: 125px !important; }
</style>
<?php
  $editurl = url('admin/study-materials/'.$id.'/edit');
  $lessonsurl = url('admin/study-materials/'.$id.'/lessons');
  $lessonsButton = '<a type="" title="Manage Lessons" href="' . $lessonsurl . '"><i class="fa fa-list"></i> Lessons</a>';
  $editButton = '<a type="" title="Edit" href="' . $editurl . '"><i class="fa fa-edit"></i> Edit</a>';
  $deleteButton = '<a data-title ="Confirmation" data-toggle="tooltip" data-placement="top" title="Delete Record" onclick="hardDelete('. $id .')" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-trash"></i> Delete</a>';
?>
<div class="">
  <div class="btn-group">
    <button type="button" class="btn btn-primary btn-flat dropdown-toggle" data-toggle="dropdown">Actions <span class="caret"></span></button>
    <ul class="dropdown-menu pull-center" role="menu" style="margin: 0px 0 0;">
      <li class="action-button"><?php echo $lessonsButton; ?></li>
      <li class="action-button"><?php echo $editButton; ?></li>
      <li class="action-button"><?php echo $deleteButton; ?></li>
    </ul>
  </div>
</div>
