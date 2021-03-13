<?php

include('db_log_fct.php');


function table_log_begin()
{ ?>
<div class="container table-container">
    <table class="table is-fullwidth">
        <thead>
            <tr>
                <th>Id</th>
                <th>Date</th>
                <th>User Id</th>
                <th>Type</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
        <?php   }  ?>



        <?php function table_element_static($id, $val, $label)
        { ?>
            <td id="<?php echo $label . "_" . htmlspecialchars($id); ?>"><?php echo htmlspecialchars($val); ?></td>
        <?php      } ?>

        <?php function table_element_editable($id, $val, $label)
        { ?>
            <td><input type="text" id="<?php echo $label . "_" . htmlspecialchars($id); ?>" value="<?php echo htmlspecialchars($val); ?>"></td>
        <?php } ?>


        <?php function table_log_row($count, $row)
        {

        ?>
            <tr>
                <?php
                table_element_static($row['uid'], $row['id'], 'id');
                table_element_static($row['uid'], $row['fdate'], 'date');
                table_element_static($row['uid'], $row['UID'], 'uid');
                table_element_static($row['uid'], $row['type'], 'type');
                table_element_static($row['uid'], $row['txt'], 'txt');
                ?>
            </tr>

        <?php   }  ?>

        <?php function table_log_end()
        { ?>
        </tbody>
    </table></div>
    <div><hr><br /><br /></div>
<?php   }  ?>

<script>
    function onrowSave(uid) {
        var postdata = {
            'cmd': "saveEntry",
            'uid': uid,
            'etablissement': document.getElementById("etablissement_" + uid).value,
            'lycee': document.getElementById("lycee_" + uid).value,
            'teamname': document.getElementById("teamname_" + uid).value,
            'login': document.getElementById("login_" + uid).value,
            'pseudo': document.getElementById("pseudo_" + uid).value,
            'mail': document.getElementById("mail_" + uid).value,

            'uid1': document.getElementById("uid1_" + uid).value,
            'nom1': document.getElementById("nom1_" + uid).value,
            'prenom1': document.getElementById("prenom1_" + uid).value,
            'email1': document.getElementById("email1_" + uid).value,
            'ismail1confirmed': document.getElementById("ismail1confirmed_" + uid).value,

            'uid2': document.getElementById("uid2_" + uid).value,
            'nom2': document.getElementById("nom2_" + uid).value,
            'prenom2': document.getElementById("prenom2_" + uid).value,
            'email2': document.getElementById("email2_" + uid).value,
            'ismail2confirmed': document.getElementById("ismail2confirmed_" + uid).value,

            'state': document.getElementById("state_" + uid).value,
            'status': document.getElementById("status_" + uid).value,

        }
        $.post("p_adminiut_data.php", postdata)
            .done(function(data) {
                alert("Data Loaded: " + data);
        });
    }

    function onrowResetPassword(uid) {
        var passwd = prompt("Enter new password", "123456");
        if (passwd == null || passwd == "") {
            return;
        }
        var postdata = {
            'cmd': "resetPassword",
            'uid': uid,
            'password': passwd,
        }
        $.post("p_adminiut_data.php", postdata)
            .done(function(data) {
                alert("Data Loaded: " + data);
            });
    }
    function onrowDelete(uid) {
        if (! confirm("Delete entry ?")) {
            return;
        }
        var postdata = {
            'cmd': "deleteUID",
            'uid': uid,
        }
        $.post("p_adminiut_data.php", postdata)
            .done(function(data) {
                alert("Data Loaded: " + data);
            });
    }
</script>

<?php function dump_table()
{
    include "ctf_sql.php";
    $user_query = "SELECT * FROM participants p
            LEFT JOIN users u
            on p.uid = u.uid
            GROUP BY p.id
            ORDER BY etablissement, lycee;";
    if ($result = $mysqli->query($user_query)) {
        while ($row = $result->fetch_assoc()) {
            table_row("-", $row);
        }
        $result->close();
    } else {
        echo "pb";
    }
    $mysqli->close();
}

//CREATE TABLE logs (id INT NOT NULL AUTO_INCREMENT, fdate datetime, UID VARCHAR(45) NULL, type INT NULL, txt VARCHAR(2000) NULL, PRIMARY KEY (id));

function dump_log_table($table)
{
    table_log_begin();
    foreach ($table as $entry) {
        table_log_row("-", $entry);
    }
    table_log_end();
}

?>