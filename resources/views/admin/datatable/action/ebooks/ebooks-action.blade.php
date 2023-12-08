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

  $tbl = 'categories';
  $id = $id;

  $editurl = url('admin/ebooks/'.$id.'/edit');
  $editButton = '<a type="" title="Edit" href="' . $editurl . '"><i class="fa fa-edit"></i> Edit</a>';

  $deleteButton = '<a data-title ="Confirmation" data-toggle="tooltip" data-placement="top" title="Delete Record"  onclick="hardDelete('. $id .')" href="javascript:void(0)" data-original-title="Delet"><i class="fa fa-trash"></i> Delete</a>';

?>


<div class="">
    <div class="btn-group">
      <button type="button" class="btn btn-primary btn-flat dropdown-toggle" data-toggle="dropdown">Actions <span class="caret"></span></button>
        <ul class="dropdown-menu pull-center" role="menu" style="margin: 0px 0 0;">
          <li class="action-button"><?php echo $editButton; ?></li> 
          <li class="action-button"><?php echo $deleteButton; ?></li> 
        </ul>
    </div>
  </div>