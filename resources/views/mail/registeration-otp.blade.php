<?php 
   date_default_timezone_set('America/Los_Angeles');   
    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <title>{{ Config::get('constants.PROJECT_TITLE') }}</title>
      <link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
   </head>
   <style type="text/css">
      table, th, td {
      border: 1px solid #dddddd;
      border-collapse: collapse;
      padding: 10px;
      }
      body{
      font-family: 'Quicksand', sans-serif;
      }
   </style>
   <body>
      <div style="background:#ededed;padding-left:40px; padding-right:60px;height:635px; clear: both;overflow: auto;">
         <div style="background:#fff;margin-left:auto;margin-right:auto; height:735px;max-width: 700px;margin-top:55px;margin-bottom:55px;">
           
            <div style="padding: 0px 25px 0px 25px;">
                           
                <div style="overflow:auto; padding:20px;font-size: 16px;line-height: 22px;">
                    
                    <h4 style="font-weight:normal;">Hello {{ (!empty($name)) ? ($name) : ('') }},
                      <br> Thank you for registration To {{ Config::get('constants.PROJECT_TITLE') }}. We are so excited to welcome you.<br>
                      <p>You OTP is : <b>{{ $otp }}</b></p>


                   <p>Regards<br>
                      Support Team
                   </p>

                   <a href="#" style="text-decoration: none;color: #d30102;">{{ Config::get('constants.PROJECT_TITLE') }}</a>
                </div>

         </div>
      </div>
   </body>
</html>