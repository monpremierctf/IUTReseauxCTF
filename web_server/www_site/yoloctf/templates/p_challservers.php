<?php

//include('p_adminlog_fct.php');

?>

<style>
    .table-fixed {
        table-layout: fixed;
        width: 100%;
    }

    .table-container {
        overflow-x: scroll;
        max-width: 100%;
    }

    .table td,
    .table th {
        padding: .0rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }

    .dataTables_wrapper {
        width: 100%;
    }
</style>


<script>
    
    var t;
    function updateStatus(){
        t.ajax.reload();
        //t.ajax.url( 'api_log.php' ).load();
        t.draw(true);
    }
/*
    var dataSet = [
    [ "Tiger Nixon", "System Architect", "Edinburgh", "5421", "2011/04/25", "$320,800" ],
    [ "Martena Mccray", "Post-Sales support", "Edinburgh", "8240", "2011/03/09", "$324,050" ],
    [ "Unity Butler", "Marketing Designer", "San Francisco", "5384", "2009/12/09", "$85,675" ]
];*/


    $(document).ready(function() {
        t = $('#statusTable').DataTable( {
            /*data: dataSet,*/
            /*"processing": true,*/
            "serverSide": true,
            "ajax": "api/api_challserver_status.php?serverlist"
        } );
        //t.row.add( [ "Unity Butler", "Marketing Designer", "San Francisco", "5384", "2009/12/09" ] ).draw( false );

    } );


</script>


<div class="col text-center">
    <div class="col text-left">
        <h2>Challenges Servers</h2><br><br>
    </div>
    <div class="col text-center">
        <div class="">
            <div class="row chall-titre bg-secondary text-white">
                <div class="col-sm text-left">Servers</div>
            </div>
            <div class="form-group text-left row">
                <table id="statusTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>name</th>
                            <th>Mem total</th>
                            <th>Mem available</th>
                            <th>Env total</th>
                            <th>Env available</th> 
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                        <th>name</th>
                        <th>Mem total</th>
                        <th>Mem available</th>
                        <th>Env total</th>
                        <th>Env available</th> 
                        </tr>
                    </tfoot>
                </table>


            </div>
            <button id="updateStatus" onClick="updateStatus()">Update</button>
        </div>



        <div class="form-group text-left  row ">
            <hr>
        </div>


    </div>

</div>