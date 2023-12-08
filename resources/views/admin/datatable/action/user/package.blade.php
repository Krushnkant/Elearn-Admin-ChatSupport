@if(isset($membership['plan']) && isset($membership['plan']) != "" )
<?php 

$today = strtotime($membership['end_date']);
$myBirthDate = strtotime(date('Y-m-d h:i:s'));
$days = round(abs($today-$myBirthDate)/60/60/24);
if($days > 0){
  $day = $days;
}else{
  $day = 0;
}
//printf("I'm %d days old.", round(abs($today-$myBirthDate)/60/60/24));

?>
   <p>{{ $membership['plan'] }} ( {{ $day  }} left to expire )</p>
 
@else
   <p>Free</p>
@endif