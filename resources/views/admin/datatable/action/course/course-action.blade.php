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

  $tbl = 'courses';
  $id = $id;

  $editurl = url('admin/courses/'.$id.'/edit');
  $chapter_url = url('admin/courses/'.encode($id).'/chapters');
  $assessment_url = url('admin/courses/'.encode($id).'/assessments');
  $bookurl = url('admin/courses/'.encode($id).'/books');

  $editButton = '<a type="" style="text-decoration: none; color: black;" title="Edit" href="' . $editurl . '"><i class="fa fa-edit"></i> Edit</a>';

  $deleteButton = '<a  style="text-decoration: none; color: black;" data-title ="Confirmation" data-toggle="tooltip" data-placement="top" title="Delete Record"  onclick="hardDelete('. $id .')" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-trash"></i> Delete</a>';

  $chapterList = '<a type="" style="text-decoration: none; color: black;" title="Chapter" href="' . $chapter_url . '"><i class="fa fa-book"></i> Chapters</a>';

  $assessmentList = '<a type="" style="text-decoration: none;" title="Chapter" href="' . $assessment_url . '"><i class="fas fa-file-csv"></i>&nbsp;Assessments</a>';
  $bookList = '<a type="" style="text-decoration: none;" title="Book" href="' . $bookurl . '"><i class="fas fa-file-csv"></i>&nbsp;Books</a>';

?>


<div class="">
    <div class="btn-group">
      <button type="button" class="btn btn-primary btn-flat dropdown-toggle" data-toggle="dropdown">Actions <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu" style="margin: 0px 0 0;">
          <li class="action-button"><?php echo $editButton; ?></li> 
          <li class="action-button"><?php echo $deleteButton; ?></li> 
          <li class="action-button"><?php echo $chapterList; ?></li> 
          <li class="action-button"><?php echo $assessmentList; ?></li> 
          <li class="action-button">
          <?php echo $bookList; ?>
            <!-- <a type="" style="text-decoration: none;" title="Books" href="{{ $bookurl }}">
              <i class="fas fa-file-csv"></i>&nbsp;Books
            </a> -->
          </li> 
        </ul>
    </div>
  </div>