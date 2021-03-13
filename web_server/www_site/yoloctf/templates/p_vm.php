<script src="/yoloctf/js/moment.min.js"></script>
	<script src="/yoloctf/js/Chart.min.js"></script>
	<script src="/yoloctf/js/Chart_utils.js"></script>

<div class="col text-center">
<div class="col text-left"><h2>VMs</h2><br><br></div>
<div class="col text-center"> 

<!---- VMs  --->
<div class="">
      <div class="row chall-titre bg-secondary text-white">
        <div class="col-sm text-left">VM States</div>
      </div>
	  <div id="VM-states">

	  </div>
	  <button class="btn btn-default btn-warning" type="button" onClick="VMState()">Refresh</button>
    </div>

<div class="form-group text-left  row ">
<hr>
</div>

<!---- Logs  --->
<?php
if (isset($_SESSION['login'])) {
                        // $admin from ctf_env.php
                        if (($_SESSION['login'] === $admin)) { ?>


<div class="form-group text-left  row ">
<hr>
</div>

<?php }
 } ?>      


<script>

var VMEntry=`
		<div class="form-group text-left row">
			  <label for="usr" class="col-2">{name}</label>
			  <label for="usr" class="col-2" hidden='true'>{hostname}</label>
              <label for="usr" class="col-2">{ip}</label>
			  <label for="usr" class="col-1" id="id-VM-{name}-icon">{icon}</label>
			  <!--<label for="usr" class="col-2" id="id-VM-{name}-state">{state}</label>-->
              <div class="col-5">
                  <button class="btn btn-default btn-warning" type="button" onClick="VMStart('{name}')">Start</button>
                  <button class="btn btn-default btn-warning" type="button" onClick="VMStop('{name}')">Stop</button>
                  <button class="btn btn-default btn-warning" type="button" onClick="VMReboot('{name}')">Reboot</button>
              </div>
        </div>
        
		`;
/* https://icons.getbootstrap.com/#install */
var svg_square = `
	<svg class="bi bi-square" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
	  <path fill-rule="evenodd" d="M14 1H2a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V2a1 1 0 00-1-1zM2 0a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V2a2 2 0 00-2-2H2z" clip-rule="evenodd"/>
	</svg>
	`;
var svg_square_fill = `
	<svg class="bi bi-square-fill" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
	  <rect width="16" height="16" rx="2"/>
	</svg>
	`;	
var svg_play = `
	<svg class="bi bi-play" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
	  <path fill-rule="evenodd" d="M10.804 8L5 4.633v6.734L10.804 8zm.792-.696a.802.802 0 010 1.392l-6.363 3.692C4.713 12.69 4 12.345 4 11.692V4.308c0-.653.713-.998 1.233-.696l6.363 3.692z" clip-rule="evenodd"/>
	</svg>
	`;
var svg_play_fill = `
	<svg class="bi bi-play-fill" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
	  <path d="M11.596 8.697l-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 010 1.393z"/>
	</svg>
	`;
var svg_check_circle = `
	<svg class="bi bi-check-circle" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
	  <path fill-rule="evenodd" d="M15.354 2.646a.5.5 0 010 .708l-7 7a.5.5 0 01-.708 0l-3-3a.5.5 0 11.708-.708L8 9.293l6.646-6.647a.5.5 0 01.708 0z" clip-rule="evenodd"/>
	  <path fill-rule="evenodd" d="M8 2.5A5.5 5.5 0 1013.5 8a.5.5 0 011 0 6.5 6.5 0 11-3.25-5.63.5.5 0 11-.5.865A5.472 5.472 0 008 2.5z" clip-rule="evenodd"/>
	</svg>
	`;
var svg_check = `
	<svg class="bi bi-check" width="1em" height="1em" viewBox="0 0 16 16" fill="green" xmlns="http://www.w3.org/2000/svg">
	  <path fill-rule="evenodd" d="M13.854 3.646a.5.5 0 010 .708l-7 7a.5.5 0 01-.708 0l-3.5-3.5a.5.5 0 11.708-.708L6.5 10.293l6.646-6.647a.5.5 0 01.708 0z" clip-rule="evenodd"/>
	</svg>
	`;
var svg_check_box = `
	<svg class="bi bi-check-box" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
	  <path fill-rule="evenodd" d="M15.354 2.646a.5.5 0 010 .708l-7 7a.5.5 0 01-.708 0l-3-3a.5.5 0 11.708-.708L8 9.293l6.646-6.647a.5.5 0 01.708 0z" clip-rule="evenodd"/>
	  <path fill-rule="evenodd" d="M1.5 13A1.5 1.5 0 003 14.5h10a1.5 1.5 0 001.5-1.5V8a.5.5 0 00-1 0v5a.5.5 0 01-.5.5H3a.5.5 0 01-.5-.5V3a.5.5 0 01.5-.5h8a.5.5 0 000-1H3A1.5 1.5 0 001.5 3v10z" clip-rule="evenodd"/>
	</svg>
	`;
var svg_x = `
	<svg class="bi bi-x" width="1em" height="1em" viewBox="0 0 16 16" fill="red" xmlns="http://www.w3.org/2000/svg">
	<path fill-rule="evenodd" d="M11.854 4.146a.5.5 0 010 .708l-7 7a.5.5 0 01-.708-.708l7-7a.5.5 0 01.708 0z" clip-rule="evenodd"/>
	<path fill-rule="evenodd" d="M4.146 4.146a.5.5 0 000 .708l7 7a.5.5 0 00.708-.708l-7-7a.5.5 0 00-.708 0z" clip-rule="evenodd"/>
	</svg>
	`;
	
function VMGetHTMLEntry(vm) {
	ret = VMEntry;
	ret = ret.replace(/{name}/g, vm['name']);
	ret = ret.replace(/{state}/g, vm['state']);
	ret = ret.replace(/{hostname}/g, vm['hostname']);
    ret = ret.replace(/{ip}/g, vm['ip']);
	if (vm['state'].search('running')>=0) {
		ret = ret.replace("{icon}", svg_check);
	} else {
		ret = ret.replace("{icon}", svg_x);
	}
	return ret;
}

function VMState(){
	var url = "https://"+ window.location.host+"/yoloctf/p_vm_api.php?State";
	$("#VM-states").html("Waiting...");
	$.get(url, function( jsondata, status ) {
		try {
			var servers = jsondata;
			$("#VM-states").html("");
			servers.forEach((vm) => {
			  $("#VM-states").html($("#VM-states").html()+VMGetHTMLEntry(vm));
			})
			var id = "#id-VM-"+vm['name']+"-icon";

		 

		}
		catch(error)   {
			//$("#VM-states").html(error+" "+error);
		}
	})
	.fail(function(XMLHttpRequest, textStatus, errorThrown) {
		var ret = XMLHttpRequest.responseText;
		$("#VM-states").html(ret); 
	}); 
	
}


function VMStop(vm){
	var url = "https://"+ window.location.host+"/yoloctf/p_vm_api.php?Stop="+vm;
	$.get(url, function( jsondata, status ) {
		try {
			var servers = jsondata;
			alert(jsondata);
			VMState();
		}
		catch(error)   {
			alert(jsondata);
		}
	})
	.fail(function(XMLHttpRequest, textStatus, errorThrown) {
		var ret = XMLHttpRequest.responseText;
		alert(ret); 
	}); 	
}


function VMReboot(vm){
	var url = "https://"+ window.location.host+"/yoloctf/p_vm_api.php?Reboot="+vm;
	$.get(url, function( jsondata, status ) {
		try {
			var servers = jsondata;
			alert(jsondata);
			VMState();
		}
		catch(error)   {
			alert(jsondata);
		}
	})
	.fail(function(XMLHttpRequest, textStatus, errorThrown) {
		var ret = XMLHttpRequest.responseText;
		alert(ret); 
	}); 	
}



function VMStart(vm){
	var url = "https://"+ window.location.host+"/yoloctf/p_vm_api.php?Start="+vm;
	$.get(url, function( jsondata, status ) {
		try {
			var servers = jsondata;
			alert(jsondata);
			VMState();
		}
		catch(error)   {
			alert(jsondata);
		}
	})
	.fail(function(XMLHttpRequest, textStatus, errorThrown) {
		var ret = XMLHttpRequest.responseText;
		alert(ret); 
	}); 	
}



/* 
        "/containerCount":  getContainerCount,
        "/containerSummary": getcontainerSummary,
        "/hostMem": getHostMem,
            {"total": 10352635904, 
            "available": 4982751232, 
            "percent": 51.9, 
            "used": 4847341568, "free": 2265042944, "active": 5633941504, "inactive": 1526718464, "buffers": 128135168, "cached": 3112116224, "shared": 245706752, "slab": 642584576}
        "/hostCPU": getHostCPU
        */


function escapeHtml(unsafe) {
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
 }



        function getStatFromServer(elementId, urlParam, jsonParam, replaceCRLF=false, insertHTML=false, escapeHTML=false)
        {
            var url = "https://"+ window.location.host+"/stats/"+urlParam;
            $.get(url, function( data, status ) {
                try {
                    var jsondata = data; //$.parseJSON(data);
                    if (jsondata.hasOwnProperty(jsonParam)) { 
                        var txt = jsondata[jsonParam];
                        if (escapeHTML) { txt = escapeHtml(txt); }
                        if (replaceCRLF) { txt = txt.replace(/(?:\r\n|\r|\n)/g, '<br>'); }
                        if (insertHTML) {
                            $(elementId).html(txt);
                        } else {
                            $(elementId).text(txt);
                        }
                    } else {
                        $(elementId).text(data);       
                    }       
                }
                catch(error)   {
                    $(elementId).text(error+" "+data); 
                }
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                var ret = JSON.parse(XMLHttpRequest.responseText);
                $("#idCPU").text(ret); 
            });        

        }

        function toMB(val) {
            return ((val)/(1024*1024)).toFixed(0);
        }
        function getHostMem()
        {
            var url = "https://"+ window.location.host+"/stats/hostMem";
            $.get(url, function( data, status ) {
                var jsondata = data;  
                $("#idMem_total").text("Total: "+toMB(jsondata["total"])+" MB");     
                $("#idMem_available").text("Available: "+toMB(jsondata["available"])+" MB");    
                $("#idMem_percent").text(jsondata["percent"]+" % Used");    
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                var ret = JSON.parse(XMLHttpRequest.responseText);
                $("#idMem_total").text(ret); 
            });        
        }
        function getHostDisk()
        {
            var url = "https://"+ window.location.host+"/stats/hostDisk";
            $.get(url, function( data, status ) {
                var jsondata = data;  
                var ret = jsondata['df'];
                ret = ret.replace(/(?:\r\n|\r|\n)/g, '<br>');
                $("#idDisk").html(ret);     
  
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                var ret = JSON.parse(XMLHttpRequest.responseText);
                $("#idMem_total").text(ret); 
            });        
        }
        function getContainerSummary()
        {
            var url = "https://"+ window.location.host+"/stats/containerSummary";
            $.get(url, function( data, status ) {
                var jsondata =data;
                var infra="";
                $.each(data["infra"],function(index, value) {
                    infra +=index+": "+value+"<br />";
                });
                $("#idContainer_infra").html(infra); 
                var sharedchalls="";
                $.each(data["sharedChalls"],function(index, value) {
                    sharedchalls +=index+": "+value+"<br />";
                });
                $("#idContainer_shared").html(sharedchalls);    
                var challs="";
                $.each(data["challs"],function(index, value) {
                    challs +=index+": "+value+"<br />";
                });   
                $("#idContainer_challs").html(challs);       
                          
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                var ret = JSON.parse(XMLHttpRequest.responseText);
                $("#idCPU").text(ret); 
            });        
        }

        

    ///////////////////////////////////////
    // CPU chart



	var color = Chart.helpers.color;

    var l_00=null;

	function initCPUChart() {
		var timeFormat = 'MM/DD/YYYY HH:mm:ss';
		var config_00 = {
			type: 'line',
			data: {
				labels: [],				
				datasets: [	 ]
			},
			options: {
				title: {
					text: 'CPU Load'
				},
				scales: {
					xAxes: [{
						type: 'time',
						time: {
							parser: timeFormat,
							// round: 'day'
							tooltipFormat: 'll HH:mm:ss'
						},
						scaleLabel: {
							display: true,
							labelString: 'Date'
						}
					}],
					yAxes: [{
						scaleLabel: {
							display: true,
							labelString: '%CPU'
						},
                        ticks: {
                            suggestedMin: 0,
                            suggestedMax: 100
                        }
					}]
				},
			}
		};
		var ctx_00 = document.getElementById('canvas_00').getContext('2d');
		l_00 = new Chart(ctx_00, config_00);
	}

    //var cpu_dataset = [{ x: '1/3/2020 11:55', y: 1}, { x: '1/3/2020 11:55', y: 3}, { x: '1/3/2020 11:55', y: 8}, { x: '1/3/2020 11:56', y: 13}, { x: '1/3/2020 11:56', y: 18}, { x: '1/3/2020 11:56', y: 23}, { x: '1/3/2020 11:56', y: 28}, { x: '1/3/2020 11:56', y: 33}, { x: '1/3/2020 11:56', y: 38}, { x: '1/3/2020 11:56', y: 43}, { x: '1/3/2020 11:56', y: 48}, { x: '1/3/2020 11:56', y: 53}, { x: '1/3/2020 11:56', y: 58}, { x: '1/3/2020 11:56', y: 65}, { x: '1/3/2020 11:56', y: 72}, { x: '1/3/2020 11:56', y: 79}, { x: '1/3/2020 11:57', y: 89}];
    var cpu_dataset = [];//[{ x: '1/3/2020 11:55:01', y: 1}, { x: '1/3/2020 11:55:33', y: 3} ];
	var dataset ;
    function addCPUDataset(myBarChart) {
		var r=55+Math.floor(Math.random() * 200);
		var g=55+Math.floor(Math.random() * 200);
		var b=55+Math.floor(Math.random() * 200);
		var color_str = 'rgb('+r.toString()+', '+g.toString()+', '+b.toString()+')';

        //data = '[ { "x": "1/3/2020 11:55", "y": 1} ]';
        dataset = {
                label: "CPU", 
                backgroundColor: color(color_str).alpha(0.5).rgbString(),
                borderColor: color_str,
                fill: false,
                data: cpu_dataset, //JSON.parse(cpu_dataset),
                lineTension: 0.1,
        }   
        //alert(dataset);
        myBarChart.data.datasets.push(dataset);
        myBarChart.update();

			
	}
	window.onload = function() {
        VMState();
	}

    function refreshButtonChallProvider() {
        getStatFromServer("#idLogs", "challengeProviderLogs","logs", true, true);
    }
    function refreshButtonMySQL() {
        getStatFromServer("#idLogs", "challengeProviderLogs","logs");
    }
    function refreshButton(){
        getHostMem();
        getHostDisk();

        getStatFromServer("#idContainer_count", "containerCount","count");
        getContainerSummary();
    }
    var optionsAnimation = {
        //Boolean - If we want to override with a hard coded scale
        scaleOverride : true,
        //** Required if scaleOverride is true **
        //Number - The number of steps in a hard coded scale
        scaleSteps : 10,
        //Number - The value jump in the hard coded scale
        scaleStepWidth : 10,
        //Number - The scale starting value
        scaleStartValue : 0
    }
    function getRandomInt(max) {
       return Math.floor(Math.random() * Math.floor(max));
    }   
	function addCPUChartEntry(val) {
		var currentdate = new Date(); 
        var datetime = 
                (currentdate.getMonth()+1)  + "/" 
                + currentdate.getDate() + "/"
                + currentdate.getFullYear() + " "  
                + currentdate.getHours() + ":"  
                + currentdate.getMinutes() + ":" 
                + currentdate.getSeconds();
        var entry = { x: datetime, y: val};
        cpu_dataset.push(entry); 
        if (cpu_dataset.length>200) { cpu_dataset.shift();}
        l_00.data.datasets.shift();
        l_00.data.datasets.push(dataset);
		l_00.update();

	}
    function refreshCPUChart() {
        var url = "https://"+ window.location.host+"/stats/hostCPU";
            $.get(url, function( data, status ) {
                var jsondata = data; 
                addCPUChartEntry(jsondata['cpu_percent']);
                $("#idCPU").text(jsondata['cpu_percent']); 
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                //var ret = JSON.parse(XMLHttpRequest.responseText);
                //$("#idCPU").text(ret); 
            });   
		//addCPUChartEntry(getRandomInt(10));
        setTimeout(function(){
            refreshCPUChart();
        }, 5000
    );
	}

    

    </script>