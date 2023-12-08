<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Example 1</title>
    <!-- <link rel="stylesheet" href="{{url('public/css/pdf.css')}}" media="all" /> -->
    <style>
.clearfix:after {
  content: "";
  display: table;
  clear: both;
}

a {
  color: #5D6975;
  text-decoration: underline;
}

body {
  position: relative;
  width: 19cm;
  height: 29.7cm;
  margin: 0 auto;
  color: #001028;
   background: #FFFFFF;
  font-family: Arial, sans-serif;
  font-size: 12px;
  font-family: Arial;
}

header {
  padding: 10px 0;
  margin-bottom: 30px;
}

#logo {
  text-align: center;
  margin-bottom: 10px;
}

#logo img {
  width: 90px;
}

h1 {
  border-top: 1px solid  #5D6975;
  border-bottom: 1px solid  #5D6975;
  color: #5D6975;
  font-size: 2.4em;
  line-height: 1.4em;
  font-weight: normal;
  text-align: center;
  margin: 0 0 20px 0;
  /* background: url(dimension.png); */
}

#project {
  float: left;
}

#project span {
  color: #5D6975;
  text-align: left;
  width: 125px;
  margin-right: 10px;
  display: inline-block;
  font-size: 0.8em;
}

#company {
  float: right;
  text-align: right;
}

#project div,
#company div {
  white-space: nowrap;
}

table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 20px;
}

table tr:nth-child(2n-1) td {
  background: #F5F5F5;
}

table th,
table td {
  text-align: center;
}

table th {
  padding: 5px 20px;
  color: #5D6975;
  border-bottom: 1px solid #C1CED9;
  white-space: nowrap;
  font-weight: normal;
}

table .service,
table .desc {
  text-align: left;
}

table td {
  padding: 10px;
  text-align: center;
}

table td.service,
table td.desc {
  vertical-align: top;
}

table td.unit,
table td.qty,
table td.total {
  font-size: 1.2em;
}

table td.grand {
  border-top: 1px solid #5D6975;;
}

#notices .notice {
  color: #5D6975;
  font-size: 1.2em;
}

footer {
  color: #5D6975;
  width: 100%;
  /* height: 30px; */
  /* position: absolute; */
  bottom: 0;
  border-top: 1px solid #C1CED9;
  padding: 8px 0;
  text-align: center;
}
body {
  font-family: "open sans", sans-serif;
  /* background: #f1f1f1; */
}
#content {
  margin: 40px auto;
  text-align: center;
  width: 600px;
}
#content h1 {
  text-transform: uppercase;
  font-weight: 700;
  margin: 0 0 40px 0;
  font-size: 25px;
  line-height: 30px;
}
.step {
  background: #cccccc;
  border-radius: 0.8em;
  -moz-border-radius: 0.8em;
  -webkit-border-radius: 0.8em;
  color: #ffffff;
  display: inline-block;
  font-weight: bold;
  line-height: 1.6em;
  margin-right: 5px;
  text-align: center;
  width: 1.6em;
}

.green {
  background: green;
  border-radius: 0.8em;
  -moz-border-radius: 0.8em;
  -webkit-border-radius: 0.8em;
  display: inline-block;
  color:green;
  font-weight: bold;
  line-height: 1.6em;
  margin-right: 5px;
  text-align: center;
  width: 1.6em;
}
.red {
  background: red;
  border-radius: 0.8em;
  -moz-border-radius: 0.8em;
  -webkit-border-radius: 0.8em;
  display: inline-block;
  color:red;
  font-weight: bold;
  line-height: 1.6em;
  margin-right: 5px;
  text-align: center;
  width: 1.6em;
}
.grey {
  background: gray;
  border-radius: 0.8em;
  -moz-border-radius: 0.8em;
  -webkit-border-radius: 0.8em;
  display: inline-block;
  color: gray;
  font-weight: bold;
  line-height: 1.6em;
  margin-right: 5px;
  text-align: center;
  width: 1.6em;
}


thead {
    display: table-header-group;
}
tfoot {
    display: table-row-group;
}
tr {
    page-break-inside: avoid;
}

</style>
</head>

<body>
    <header class="clearfix">
        <div id="logo">
            <!--<img src="https://chatsupport.co.in/public/Admin/logo.jpeg" >-->
			<img src="https://chatsupport.co.in/public/Admin/kwmain.png" >
			
        </div>


        <h1>Your Result</h1>
        
		<div id="project">
            <div><span>Status</span>{{$data['status']}}</div>
            <br />
            <div><span>Percenatage Scored</span>{{$data['total_scored']}}</div>
            <br />
            <div><span>Assessment Completion Date</span>&nbsp;&nbsp;{{$data['assessment']['assessment_campletion_date']}}</div>
            <br />
            <div><span>Pass Percenatage</span> {{$data['passing_percentage']}}</div>
            <br />
            <div><span>Test name</span>{{$user->name}}</div>
			
        </div>
    </header>

    <main>

        <h1>Questions</h1>
        <div style="margin:20px 20px">
            <div>
                <div class="step green">1</div>Correct
                <div class="step red">1</div>Wrong
                <div class="step grey">1</div>Un-attampted
            </div>
            <br />
            <br />
            <br />
            @foreach ($data['data'] as $key=> $result)
            <div
                class="step @if($result['is_correct']=='correct') green @elseif($result['is_correct']=='incorrect') red @elseif($result['is_correct']=='unattempted') grey @endif">
                {{$key+1}} 
			</div>
            @endforeach
        </div>

        <h1>Progress Report</h1>
        <div style="margin:20px 20px">
            <img src="https://quickchart.io/chart?width=250&height=200&c={type:'line',data:{labels:['People','Process', 'Business Environment'], datasets:[{label:'Result', data: [{{$data['categoryWiseReport'][0]['category'][0]['correctQuestion']}},{{$data['categoryWiseReport'][0]['category'][1]['correctQuestion']}},{{$data['categoryWiseReport'][0]['category'][2]['correctQuestion']}}], fill:false,borderColor:'rgba(255,128,0,0.6)',fill:true,backgroundColor:'rgba(255,128,0,0.1)'}]}}" style="text-align: center">
        </div>
        <div style="margin-top:20px">
            <h1>Questions List</h1>
            <table>
                <thead> 
                    <tr>
                        <th class="service">Category</th>
                        <th>Total Questions</th>
                        <th>Correct Questions</th>
                        <th>Percenatage Scored</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['categoryWiseReport'] as $categoryWiseReport)
                    <tr>
                        <td style="color:green;font-weight: bold;" class="service">{{$categoryWiseReport['title']}}</td>
                        <td class="unit">{{$categoryWiseReport['totalQuestion']}}</td>
                        <td class="qty">{{$categoryWiseReport['correctQuestion']}}</td>
                        <td class="total">{{$categoryWiseReport['scored']}}%</td>
                    </tr>
                    @foreach ($categoryWiseReport['category'] as $category)
                    <tr>
                        <td class="service">{{$category['name']}}</td>
                        <td class="unit">{{$category['totalQuestion']}}</td>
                        <td class="qty">{{$category['correctQuestion']}}</td>
                        <td class="total">{{$category['scored']}}%</td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>  
    <footer>
    Copyright © 2021 <a href="#"> Knowledgewoods.</a> All rights reserved.
    </footer>
</body>

</html>
