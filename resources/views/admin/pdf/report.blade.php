<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Example 1</title>
    <link rel="stylesheet" href="style.css" media="all" />
	 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      var visitor = <?php echo $visitor; ?>;
      console.log(visitor);
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable(visitor);
        var options = {
          title: 'Progress Report',
          curveType: 'function',
          legend: { position: 'bottom' }
        };
        var chart = new google.visualization.LineChart(document.getElementById('linechart'));
        chart.draw(data, options);
      }
    </script>
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
  width: 21cm;  
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
  padding: 20px;
  text-align: right;
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
  height: 30px;
  position: absolute;
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
  color: green;
  display: inline-block;
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
  color: red;
  display: inline-block;
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
  color: #fff;
  display: inline-block;
  font-weight: bold;
  line-height: 1.6em;
  margin-right: 5px;
  text-align: center;
  width: 1.6em; 
}

    </style>
  </head>
  <body>
    <header class="clearfix">
      <div id="logo">
        <img src="logo.png">
      </div>
      
      
	  <h1>Your Result</h1>
      <div id="project">
        <div><span>Status</span> Website development</div>
		<br/>
        <div><span>Percenatage Scored</span> John Doe</div>
		<br/>
        <div><span>Assessment Completion Date</span> 796 Silver Harbour, TX 79273, US</div>
		<br/>
        <div><span>Pass Percenatage</span> <a href="mailto:john@example.com">john@example.com</a></div>
		<br/>
        <div><span>Test name</span> August 17, 2015</div>
       
      </div>
    </header>

    <main>
	
		 <h1>Questions</h1>
		 <div style="margin:20px 20px">
		 <div style="float:left">
	<div class="green">1</div>Correct
	<div class="red">1</div>Wrong
	<div class="grey">1</div>Un-attampted
	</div>
	<br/>
	<br/>
	<br/>
	<div class="step">1</div>
	<div class="step">2</div>
	<div class="step">3</div>
	<div class="step">4</div>
	<div class="step">5</div>
	<div class="step">6</div>
	<div class="step">7</div>
	<div class="step">8</div>
	<div class="step">9</div>
	<div class="step">10</div>
	
	</div>
	
	 <h1>Progress Report</h1>
		 <div style="margin:20px 20px">
		 <div id="linechart" style="width: 900px; height: 300px"></div>
	
	</div>
	<div style="margin-top:20px">
	  <h1>Questions List</h1>
      <table>
        <thead>
          <tr>
            <th class="service">Category</th>
            <th class="desc">Sub Category </th>
            <th>Total Questions</th>
            <th>Correct Questions</th>
            <th>Percenatage Scored</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="service">Design</td>
            <td class="desc">Creating a recognizable design solution based on the company's existing visual identity</td>
            <td class="unit">$40.00</td>
            <td class="qty">26</td>
            <td class="total">$1,040.00</td>
          </tr>
         
        
        </tbody>
      </table>
      </div>
    </main>
    <footer>
      Invoice was created on a computer and is valid without the signature and seal.
    </footer>
  </body>
</html>