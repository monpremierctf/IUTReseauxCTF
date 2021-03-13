<?php


include('p_adminlog_fct.php');

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
    function updateLogs(){
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
        t = $('#logTable').DataTable( {
            /*data: dataSet,*/
            /*"processing": true,*/
            "serverSide": true,
            "ajax": "api/api_log.php"
        } );
        //t.row.add( [ "Unity Butler", "Marketing Designer", "San Francisco", "5384", "2009/12/09" ] ).draw( false );

    } );


</script>



<div class="col text-center">
    <div class="col text-left">
        <h2>Admin Hack Out</h2><br><br>
    </div>

    <div class="col text-center">
        <div class="">
            <div class="row chall-titre bg-secondary text-white">
                <div class="col-sm text-left">Logs</div>
            </div>
            <div class="form-group text-left row">

                <table id="logTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>
                            <th>UserID</th>
                            <th>Type</th>
                            <th>Log</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>
                            <th>UserID</th>
                            <th>Type</th>
                            <th>Log</th>
                        </tr>
                    </tfoot>
                </table>


            </div>
            <button id="logUpdate" onClick="updateLogs()">Update</button>
        </div>



        <div class="form-group text-left  row ">
            <hr>
        </div>


    </div>

</div>
