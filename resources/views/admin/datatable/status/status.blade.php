@if($status == '1')
 <a href="javascript:void(0)"  id="changeStatus-{{ $id }}-Active" class="btn btn-info btn-xs" ><i class="fa fa-thumbs-up"> Active </i> </a>
@else
 <a href="javascript:void(0)"  id="changeStatus-{{ $id }}-Inactive" class="btn btn-danger btn-xs" ><i class="fa fa-thumbs-down"> Inactive</i></a>
@endif
