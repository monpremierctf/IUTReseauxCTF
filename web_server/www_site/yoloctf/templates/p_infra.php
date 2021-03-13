<!---- Libraries  --->
<script src="/yoloctf/js/moment.min.js"></script>
<script src="/yoloctf/js/Chart.min.js"></script>
<script src="/yoloctf/js/Chart_utils.js"></script>

<!---- Page  --->
<div class="col text-center">
    <div class="col text-left"><h2>Infra</h2><br><br></div>
    <div id="servers" class="col text-center"> 
    </div>
</div>
<div class="form-group text-left  row "><hr></div>

<!---- Is conected ? --->
<?php
    if (!isset($_SESSION['login'])) { exit(); }
    if (!($_SESSION['login'] === $admin)) { exit(); }
?>



<!---- HTML templates  --->
<script>
    function html_yop_server_start(name) { 
        var html = `
            <div class="">
                <div class="row chall-titre bg-secondary text-white">
                    <div class="col-sm text-left">{NAME}</div>
                </div>
                <div id="server_{NAME}" style="border: 2px dotted grey;"></div>   
            </div>
          `;
        html = html.replace(/{NAME}/g, name)
        return (html);
    }  
    function html_yop_VM_start(name) { 
        var html = `
            <div class="">
                <div class="row chall-titre bg-secondary text-white">
                    <div class="col-sm text-left">{NAME}</div>
                </div>                
            </div>
          `;
        html = html.replace(/{NAME}/g, name)
        return (html);
    } 
    var color = Chart.helpers.color;
    var canvas_ctx={};
    var canvas_chart_cpu={};
    var canvas_chart_ram={};
    var canvas_chart_disk={};
    var canvas_chart_net={};
    var canvas_cpu_dataset = {};
    var canvas_dataset={};
    
    function setDataset(myBarChart, setname, val_x, val_y) {        
        canvas_cpu_dataset[setname] = [];  
        l = Math.min(val_x.length, val_y.length);
        for (let i = 0; i < l; i++) {
            var val = { x: val_x[i], y: val_y[i] };
            canvas_cpu_dataset[setname].push(val);
        }
        //console.log(canvas_cpu_dataset[setname]);
        //canvas_cpu_dataset[setname] =  [{ x: '01-03-2020 11:55:00', y: 1.2}, { x: '1-3-2020 11:55', y: 3}, { x: '1-3-2020 11:55', y: 8}, { x: '1-3-2020 11:56', y: 13}, { x: '1-3-2020 11:56', y: 18}, { x: '1-3-2020 11:56', y: 23}, { x: '1-3-2020 11:56', y: 28}, { x: '1-3-2020 11:56', y: 33}, { x: '1-3-2020 11:56', y: 38}, { x: '1-3-2020 11:56', y: 43}, { x: '1-3-2020 11:56', y: 48}, { x: '1-3-2020 11:56', y: 53}, { x: '1-3-2020 11:56', y: 58}, { x: '1-3-2020 11:56', y: 65}, { x: '1-3-2020 11:56', y: 72}, { x: '1-3-2020 11:56', y: 79}, { x: '1-3-2020 11:57', y: 89}];
        //console.log(canvas_cpu_dataset[setname]);
        
		var r=55+Math.floor(Math.random() * 200);
		var g=55+Math.floor(Math.random() * 200);
		var b=55+Math.floor(Math.random() * 200);
		var color_str = 'rgb('+r.toString()+', '+g.toString()+', '+b.toString()+')';

        canvas_dataset[setname] = {
                label: setname, 
                backgroundColor: color(color_str).alpha(0.5).rgbString(),
                borderColor: color_str,
                fill: false,
                data: canvas_cpu_dataset[setname], //JSON.parse(cpu_dataset),
                lineTension: 0.1,
        }   
        myBarChart.data.datasets.push(canvas_dataset[setname]);
        myBarChart.update();
	}
    
    function initChart(name, title, ylabel, canvas_id) {
		var timeFormat = 'DD-MM-YYYY HH:mm:ss';
		var config_00 = {
			type: 'line',
			data: {
				labels: [],				
				datasets: [	 ]
			},
			options: {
				title: {
					text: title
				},
                legend: {
                    position: 'right'
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
							labelString: ylabel
						},
                        ticks: {
                            suggestedMin: 0,
                            suggestedMax: 100
                        }
					}]
				},
			}
		};       
		canvas_ctx[name] = document.getElementById(canvas_id).getContext('2d');
		return new Chart(canvas_ctx[name], config_00);
	}   
    
    function html_yop_server_val(name, vals, isHost) {
        var html = `
        <!-- Server stats -->
        <div class="col-12" >
            <!-- CPU -->
            <div class="row">
                <div class="col-6">
                    <div class="form-group text-left row"> 
                      <label for="usr" class="col-4">CPU user</label>
                      <label for="usr" class="col-4" >`+vals["cpu_user"].slice(-1)[0]+` %</label>
                    </div>
                    <div class="form-group text-left row">
                      <label for="usr" class="col-4">CPU kernel</label>
                      <label for="usr" class="col-4" >`+vals["cpu_kernel"].slice(-1)[0]+` %</label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="col-12"><canvas id='canvas_`+name+`_cpu'></canvas></div>
                </div>
            </div>
             <!-- Ram -->
            <div class="row">
                <div class="col-6">
                    <div class="form-group text-left row">
                      <label for="usr" class="col-4">RAM Total</label>
                      <label for="usr" class="col-4" >`+Math.floor(vals["ram_total"].slice(-1)[0] /1024)+` MB</label>
                    </div>
                    <div class="form-group text-left row">
                      <label for="usr" class="col-4">RAM Free</label>
                      <label for="usr" class="col-4" >`+Math.floor(vals["ram_free"].slice(-1)[0] /1024) +` MB</label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="col-12"><canvas id='canvas_`+name+`_ram'></canvas></div>
                </div>
            </div>  `;  

        if ('disk_usage_total' in vals) html += `
             <!-- Disk -->        
            <div class="row">
                <div class="col-6">        
                    <div class="form-group text-left row">
                      <label for="usr" class="col-4">Disk / Total</label>
                      <label for="usr" class="col-4" >`+ Math.floor(vals["disk_usage_total"].slice(-1)[0] / 1024 /1024)+` Gb</label>
                    </div>
                    <div class="form-group text-left row">
                      <label for="usr" class="col-4">Disk / Free</label>
                      <label for="usr" class="col-4" >`+ Math.floor(vals["disk_usage_free"].slice(-1)[0] / 1024 /1024) +` Gb</label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="col-12"><canvas id='canvas_`+name+`_disk'></canvas></div>
                </div>
            </div>   
            `;  
        html += `            
             <!-- Net -->
            <div class="row">
                <div class="col-6">        
                    <div class="form-group text-left row">  
                      <label for="usr" class="col-4">Net eno1 Rx</label>
                      <label for="usr" class="col-4" >`+Math.floor(vals["net_rx_1"].slice(-1)[0] /1024)+` kB/s</label>
                    </div>
                    <div class="form-group text-left row">
                      <label for="usr" class="col-4">Net eno1 Tx</label>
                      <label for="usr" class="col-4" >`+Math.floor(vals["net_tx_1"].slice(-1)[0] /1024)+` kB/s</label>
                    </div>
                 </div>
                <div class="col-4">
                    <div class="col-12"><canvas id='canvas_`+name+`_net'></canvas></div>
                </div>
            </div>         
        </div>  
        `;
    if (isHost) {
        html += `   
        <!-- VM stats -->
        <div id="server_`+name+`_VMs" class="col-12" style="border: 2px dotted light-grey;>
        </div>  
        <button type="submit" class="btn btn-primary" onclick="refreshButton()">Refresh</button>  
        `;
        }
        return (html);
    } 
        

</script> 





<script>
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
        
        
    function getMetricsFromServer(server)
    {
        var url = "https://"+ window.location.hostname+"/yoloctf/p_vm_api.php?Metrics&server="+server;
        $.get(url, function( data, status ) {
            try {
                    var txt = "";
                    txt += html_yop_server_val(server, data["host"],true);  
                    $("#server_"+server).html(txt); 
                    canvas_chart_cpu[server] = initChart(server, 'CPU', '%CPU', 'canvas_'+server+'_cpu');
                    setDataset(canvas_chart_cpu[server], 'CPU user', data["host"]['date'], data["host"]['cpu_user']);
                    setDataset(canvas_chart_cpu[server], 'CPU kernel', data["host"]['date'], data["host"]['cpu_kernel']);
                    
                    canvas_chart_ram[server] = initChart(server, 'RAM', 'RAM kB', 'canvas_'+server+'_ram');
                    setDataset(canvas_chart_ram[server], 'RAM Total', data["host"]['date'], data["host"]['ram_total']);
                    setDataset(canvas_chart_ram[server], 'RAM Free', data["host"]['date'], data["host"]['ram_free']);
                    
                    canvas_chart_disk[server] = initChart(server, 'Disk', 'Disk kB', 'canvas_'+server+'_disk');
                    setDataset(canvas_chart_disk[server], 'Disk Total', data["host"]['date'], data["host"]['disk_usage_total']);
                    setDataset(canvas_chart_disk[server], 'Disk Free', data["host"]['date'], data["host"]['disk_usage_free']);
                    
                    canvas_chart_net[server] = initChart(server, 'Net', 'Net kB/s', 'canvas_'+server+'_net');
                    setDataset(canvas_chart_net[server], 'Net Rx', data["host"]['date'], data["host"]['net_rx_1']);
                    setDataset(canvas_chart_net[server], 'Net Tx', data["host"]['date'], data["host"]['net_tx_1']);
                    
                    txt="";
                    for (let [key, value] of Object.entries(data)) {
                        if (key != 'host') {
                            //txt += key+'<br />'; 
                            txt += html_yop_VM_start(key);
                            txt += html_yop_server_val(key, data[key],false);  
                        }
                    }
                    $("#server_"+server+'_VMs').html(txt); 
                    for (let [vm, value] of Object.entries(data)) {
                        if (vm != 'host') {
                            canvas_chart_cpu[vm] = initChart(vm, 'CPU', '%CPU', 'canvas_'+vm+'_cpu');
                            setDataset(canvas_chart_cpu[vm], 'CPU user', data[vm]['date'], data[vm]['cpu_user']);
                            setDataset(canvas_chart_cpu[vm], 'CPU kernel', data[vm]['date'], data[vm]['cpu_kernel']);
                            
                            canvas_chart_ram[vm] = initChart(vm, 'RAM', 'RAM kB', 'canvas_'+vm+'_ram');
                            setDataset(canvas_chart_ram[vm], 'RAM Total', data[vm]['date'], data[vm]['ram_total']);
                            setDataset(canvas_chart_ram[vm], 'RAM Free', data[vm]['date'], data[vm]['ram_free']);

                            canvas_chart_net[vm] = initChart(vm, 'Net', 'Net kB/s', 'canvas_'+vm+'_net');
                            setDataset(canvas_chart_net[vm], 'Net Rx', data[vm]['date'], data[vm]['net_rx_1']);
                            setDataset(canvas_chart_net[vm], 'Net Tx', data[vm]['date'], data[vm]['net_tx_1']);
                        }
                    }
            }
            catch(error)   {
                $("#server_"+server).text(error+" "+data); 
            }
        })
        .fail(function(XMLHttpRequest, textStatus, errorThrown) {
            var ret = JSON.parse(XMLHttpRequest.responseText);
            $("#server_"+server).text(ret); 
        });        

    }
    
    
    
	window.onload = function() {
        getServerList();
	}

    function getServerList() 
    {
        var url = "https://"+ window.location.hostname+"/yoloctf/p_vm_api.php?Servers";
        $.get(url, function( data, status ) {
            try {
                    var txt = "";
                    for (const name of data) { 
                        txt += html_yop_server_start(name);
                    }
                    $("#servers").html(txt);  
                    for (const name of data) { 
                        getMetricsFromServer(name);
                    }                        
                    
                      
            }
            catch(error)   {
                $("#servers").text(error+" "+data); 
            }
        })
        .fail(function(XMLHttpRequest, textStatus, errorThrown) {
            var ret = JSON.parse(XMLHttpRequest.responseText);
            $("#servers").text(ret); 
        });        

    }
    
        
    function escapeHtml(unsafe) {
        return unsafe
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
     }


    </script>