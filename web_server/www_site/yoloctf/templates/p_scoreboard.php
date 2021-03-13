<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Y0L0 CTF</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/yoloctf/js/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
  
  <script src="/yoloctf/js/jquery.min.js"></script>
  <script src="/yoloctf/js/popper.min.js"></script>
  <script src="/yoloctf/js/bootstrap.min.js"></script>

  <script src="/yoloctf/js/moment.min.js"></script>
	<script src="/yoloctf/js/Chart.min.js"></script>
	<script src="/yoloctf/js/Chart_utils.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
	<style>
		canvas {
			-moz-user-select: none;
			-webkit-user-select: none;
			-ms-user-select: none;
		}
	</style>


</head>
<body>

<!--- Page Header  -->
<?php
/*
    include "Parsedown.php";
    $Parsedown = new Parsedown();
	include 'header.php'; 
	require_once 'ctf_env.php'; 
	*/
?>




        <!--- Page Content  -->
        <div class="col">

			<div class="container">
				<div>
					<canvas id='canvas_00'></canvas>
				</div>

			<div>
				<button type="submit" class="btn btn-primary" onclick="return refreshMyFlags()">Mes Flags</button> 
				<button type="submit" class="btn btn-primary" onclick="return refreshChartFlags(20)">TOP 20</button> 
				<button type="submit" class="btn btn-primary" onclick="return refreshChartFlags(50)">TOP 50</button>   
				<select class="btn btn-primary dropdown-toggle" id="CatFilter" name = "dropdown">
					<option value = "" selected></option>
<?php 
	$cat = getCategories();
	foreach ($cat as $c) {
		echo '					<option value = "'.$c.'">'.$c.'</option>';
	}
?>
				 </select>
                <select class="btn btn-primary dropdown-toggle" id="GroupFilter" name = "dropdown">
					<option value = "" selected></option>
<?php 
   
function getMyGroups(){
    require_once('ctf_sql.php');
    $uid = $_SESSION['uid'];
    $request = "SELECT groupname FROM groups -- - WHERE UIDADMIN='$uid'";
    $result = $mysqli->query($request);
    $count  = $result->num_rows;
    $ret=[];
    if($count>0) {
        while ($row = $result->fetch_array()) {
            array_push($ret, $row['groupname']);
        }
    }
    return $ret;
}

	$cat = getMyGroups();
	foreach ($cat as $c) {
		echo '					<option value = "'.$c.'">'.$c.'</option>';
	}
?>
				 </select>		 
			</div>
			<div id='IUT'>	</div>
        <div class="row chall-titre bg-secondary text-white">
        <div class="col-sm text-left">Synthèse</div>
        </div>
        <div id='Synthese'></div>
			
        <div class="row chall-titre bg-secondary text-white">
        <div class="col-sm text-left">Global scores</div>
        </div>
        <div id='Top20'></div>
  
</body>
</html>

<script>
	

	var color = Chart.helpers.color;

	/* user can be unsafe and must to be escaped */
	function addFlagDataset(myBarChart, user, uid) {
		var user_dataset_url = "https://localhost/yoloctf/api/zen_data.php?UsersFlags=5e0f2b9684325";
		var user_dataset = [{ x: '1/3/2020 11:55', y: 1}, { x: '1/3/2020 11:55', y: 3}, { x: '1/3/2020 11:55', y: 8}, { x: '1/3/2020 11:56', y: 13}, { x: '1/3/2020 11:56', y: 18}, { x: '1/3/2020 11:56', y: 23}, { x: '1/3/2020 11:56', y: 28}, { x: '1/3/2020 11:56', y: 33}, { x: '1/3/2020 11:56', y: 38}, { x: '1/3/2020 11:56', y: 43}, { x: '1/3/2020 11:56', y: 48}, { x: '1/3/2020 11:56', y: 53}, { x: '1/3/2020 11:56', y: 58}, { x: '1/3/2020 11:56', y: 65}, { x: '1/3/2020 11:56', y: 72}, { x: '1/3/2020 11:56', y: 79}, { x: '1/3/2020 11:57', y: 89},]
		var r=55+Math.floor(Math.random() * 200);
		var g=55+Math.floor(Math.random() * 200);
		var b=55+Math.floor(Math.random() * 200);
		var color_str = 'rgb('+r.toString()+', '+g.toString()+', '+b.toString()+')';
		var params = {UsersFlags : uid};
		var catFilter = $("#CatFilter option:selected").text();
		if (catFilter!="") {
			params['Category']= catFilter;
		}
		$.get(
			"api/zen_data.php",
			params,
			function(data) {
				//alert(data);
				//data = '[ { "x": "1/3/2020 11:55", "y": 1} ]';
				var dataset = {
						label: user, 
						backgroundColor: color(color_str).alpha(0.5).rgbString(),
						borderColor: color_str,
						fill: false,
						data: JSON.parse(data),
				}
				//alert(dataset);
				myBarChart.data.datasets.push(dataset);
				myBarChart.update();
			}
		);
		
			
	}
	window.onload = function() {
		initChartFlags();
		loadChartFlags(20);
		initIUT();

	}

	function refreshChartFlags(nb) {
		l_00.data.datasets=[];
		l_00.update();
		loadChartFlags(nb, "");
	}

	var l_00=null;
	function initChartFlags() {
		var timeFormat = 'MM/DD/YYYY HH:mm';
		var config_00 = {
			type: 'line',
			data: {
				labels: [],
				
				datasets: [	 ]
			},
			options: {
				title: {
					text: 'Scoreboard'
				},
				scales: {
					xAxes: [{
						type: 'time',
						time: {
							parser: timeFormat,
							// round: 'day'
							tooltipFormat: 'll HH:mm'
						},
						scaleLabel: {
							display: true,
							labelString: 'Date'
						}
					}],
					yAxes: [{
						scaleLabel: {
							display: true,
							labelString: 'Flags'
						}
					}]
				},
			}
		};
		var ctx_00 = document.getElementById('canvas_00').getContext('2d');
		l_00 = new Chart(ctx_00, config_00);
	}

	function top20_table_start() {
		return ' \
		<table class="table table-striped">\
		<thead>\
			<tr>\
			<th scope="col">#</th>\
			<th scope="col">Team</th>\
			<th scope="col">Score</th>\
			<th scope="col">IUT</th>\
			<th scope="col">Lycee</th>\
			</tr>\
		</thead>\
		<tbody>\
		';
	}



function escapeHtml(unsafe) {
	if (unsafe === null) return "";
	unsafe = unsafe.toString();
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
 }



	function top20_table_entry(count, entry){
		return ' \
		<tr> \
			<th scope="row">'+count.toString()+'</th> \
			<td>'+escapeHtml(entry.login)+'</td> \
			<td>'+escapeHtml(entry.score)+'</td> \
			<td>'+escapeHtml(entry.etablissement)+'</td>  \
			<td>'+escapeHtml(entry.lycee)+'</td>  \
    	</tr>' ;	
	}
	function top20_table_stop() {
		return ' \
		</tbody> \
		</table> \
		';
	}

	function loadChartFlags(nb, category="") {
        var params ={Top20 : nb};
		var groupFilter = $("#GroupFilter option:selected").text();
		if (groupFilter!="") {
			params['group']= groupFilter;
		}
		$.get(
			"api/zen_data.php",
			params,
			function(data) {
				table = top20_table_start();
                document.getElementById('Synthese').innerHTML ="";
				//alert(data);

				classement = JSON.parse(data);
				count=1;
				for (const entry of classement) {
					table+=top20_table_entry(count, entry);
                    addSyntheseEntry(count, classement.length, entry.login);
					count=count+1;
					addFlagDataset(l_00, entry.login, entry.UID);
                }
				table += top20_table_stop();
				document.getElementById('Top20').innerHTML = table; 
			}
		);

	}

	function initIUT()
	{
		$.get(
			"api/zen_data.php", {IUTList : 0},
			function(data) {				
				iutlist="";
				for (const entry of data) {
					iutlist+='<button type="submit" class="btn btn-info" onclick="loadIUTFlags(\''+entry.etablissement+'\')">'+entry.etablissement+'</button>';
					//	+"<div id='iut_"+entry.etablissement+"'></div>";
				}
				document.getElementById('IUT').innerHTML = iutlist; 
			}
		);

	}

	function loadIUTFlags(iut)
	{
		l_00.data.datasets=[];
		l_00.update();
		$.get(
			"api/zen_data.php",
			{Top20 : 200, iut : iut},
			function(data) {
				table = top20_table_start();
				//alert(data);
    
				classement = JSON.parse(data);
				count=1;
				for (const entry of classement) {
					table+=top20_table_entry(count, entry);
					count=count+1;
					addFlagDataset(l_00, entry.login, entry.UID);
				}
				table += top20_table_stop();
				document.getElementById('Top20').innerHTML = table; 
			}
		);


	}


	function refreshMyFlags(){
		l_00.data.datasets=[];
		l_00.update();
		addFlagDataset(l_00, '<?php print htmlspecialchars($_SESSION['login']) ?>', '<?php echo  $_SESSION['uid'] ?>');
	}
	
    
    //
    // Synthse Table 
    //
    
	function synthese_table_start(entry) {
		ret =  ' \
		<table id = "mySyntheseTable" class="table table-striped">\
		<thead>\
			<tr>\
			<th scope="col">#</th>\
			<th scope="col">Login</th>\
			<th scope="col">Score total</th>';
            
            
        for (const cat of entry.categories) {     
            ret += '<th scope="col">'+cat.category+'</th>';
        }    
		ret += '\
			</tr>\
		</thead>\
		<tbody>\
		';
        return ret;
	}
    var csv_tab=";";
    var csv_endline="\n";
    function escapeCSV(str) {
        return str;
    }
    function synthese_csv_start(entry) {
		ret =  '#'
                +csv_tab+'Login'
                +csv_tab+'Score total';
        for (const cat of entry.categories) {     
            ret += csv_tab + cat.category +" solved"
                  +csv_tab + cat.category;
        }    
		ret += csv_endline;
        return ret;
	}

    function synthese_table_entry(count, entry){
		ret = ' \
		<tr> \
			<th scope="row">'+count.toString()+'</th> \
			<td>'+escapeHtml(entry.login)+'</td> \
			<td>'+escapeHtml(entry.totalScore)+'</td>';
        for (const cat of entry.categories) {     
            ret += '<td>'+cat.nbChallSolved+'/'+cat.nbChall+'</td>';
        }    
		ret += '</tr>' ;
        return ret;        
	}
    function synthese_csv_entry(count, entry){
		ret = count.toString()+csv_tab+escapeCSV(entry.login)+csv_tab+escapeCSV(entry.totalScore);
        for (const cat of entry.categories) {     
            ret += csv_tab+escapeCSV(cat.nbChallSolved);
            ret += csv_tab+escapeCSV(cat.nbChall);
        }    
		ret += csv_endline;
        return ret;        
	}
	function synthese_table_stop() {
		return ' \
		</tbody> \
		</table> \
		';
	}
    
    var synthese_table="";
    var synthese_table_csv="";
    function addSyntheseEntry(count, nbentry, user) {
		//var user_dataset_url = "https://yop-server1.home:8443/yoloctf/api/zen_data.php?UsersDetailedScore=sebastien.xxxx";
        /*
        {"login":"sebastien.josset",
        "totalScore":435,
        "categories":[
            {"category":"Premier Flag","score":0,"nbChall":2,"nbChallSolved":0},
            {"category":"Terminal","score":0,"nbChall":1,"nbChallSolved":0},
            {"category":"1erServeur","score":0,"nbChall":7,"nbChallSolved":0},
            {"category":"Password","score":0,"nbChall":5,"nbChallSolved":0},
            {"category":"Decode","score":0,"nbChall":9,"nbChallSolved":0},
            {"category":"Ghost in the Shell","score":0,"nbChall":9,"nbChallSolved":0},
            {"category":"TrainingHTTP","score":0,"nbChall":15,"nbChallSolved":0},
            {"category":"TrainingFileUpload","score":0,"nbChall":6,"nbChallSolved":0},
            {"category":"TrainingLFI","score":0,"nbChall":5,"nbChallSolved":0},
            {"category":"TrainSQLi","score":0,"nbChall":3,"nbChallSolved":0},
            {"category":"SQLi","score":0,"nbChall":4,"nbChallSolved":0},
            {"category":"File Upload","score":0,"nbChall":3,"nbChallSolved":0},{"category":"Network protocol","score":0,"nbChall":6,"nbChallSolved":0},{"category":"Privilege Escalation","score":0,"nbChall":3,"nbChallSolved":0},{"category":"Buffer overflows","score":0,"nbChall":12,"nbChallSolved":0},{"category":"Exploit","score":0,"nbChall":3,"nbChallSolved":0},{"category":"Demo","score":0,"nbChall":7,"nbChallSolved":0},{"category":"Mutillidae","score":0,"nbChall":1,"nbChallSolved":0},{"category":"DVWA","score":0,"nbChall":5,"nbChallSolved":0},{"category":"DC-1","score":0,"nbChall":17,"nbChallSolved":1},{"category":"DC-4","score":0,"nbChall":25,"nbChallSolved":0},{"category":"MrRobot","score":0,"nbChall":32,"nbChallSolved":23},{"category":"Docker","score":0,"nbChall":14,"nbChallSolved":1},{"category":"Eval01","score":0,"nbChall":28,"nbChallSolved":4},{"category":"PwnLab","score":0,"nbChall":24,"nbChallSolved":14}]}
        */
		var params = {UsersDetailedScore : user};
		$.get(
			"api/zen_data.php",
			params,
			function(data) {
				//alert(data);
				//data = '[ { "x": "1/3/2020 11:55", "y": 1} ]';
				var d= JSON.parse(data);
				//alert(dataset);
                if (count==1) { 
                    synthese_table = synthese_table_start(d); 
                    synthese_csv = synthese_csv_start(d); 
                    document.getElementById('Synthese').innerHTML = "Loading datas...";
                }
				synthese_table += synthese_table_entry(count, d);
                synthese_csv += synthese_csv_entry(count, d);
                if (count==nbentry) { 
                    synthese_table += synthese_table_stop(); 
                    synthese_table = '<button type="submit" class="btn btn-primary" onclick="onDownloadCSV()">Download CSV</button>' + synthese_table;
                    
                    document.getElementById('Synthese').innerHTML = synthese_table;
                    $('#mySyntheseTable').DataTable();
                }
			}
		);
	
	}
    function downloadFile(data, fileName, type="text/plain") {
        // Create an invisible A element
        const a = document.createElement("a");
        a.style.display = "none";
        document.body.appendChild(a);

        // Set the HREF to a Blob representation of the data to be downloaded
        a.href = window.URL.createObjectURL(
        new Blob([data], { type })
        );

        // Use download attribute to set set desired file name
        a.setAttribute("download", fileName);

        // Trigger the download by simulating click
        a.click();

        // Cleanup
        window.URL.revokeObjectURL(a.href);
        document.body.removeChild(a);
    }
    
    function onDownloadCSV(){
        downloadFile(synthese_csv, "synthese.csv");
    }
        
        
</script>




