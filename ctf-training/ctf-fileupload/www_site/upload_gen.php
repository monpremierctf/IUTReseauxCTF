<!DOCTYPE html>
<html lang="fr">

<head>
  <title>Web Form : File upload - Basic</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script defer src="js/fontawesome.all.js"></script>

  <link rel="stylesheet" href="login.css">
</head>



<body>


<div>
    <form class="login-form" action="" method="post" enctype="multipart/form-data">
        <h1>Upload a file</h1>
        <div><?php if (isset($desc)) { echo $desc; } ?></div>
        <div class="form-field">
            <i class="fas fa-file-upload"></i>
            <input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
            <input type="file" name="file"/>
        </div>
       
        <button type="submit" value="Login" class="btn">GO</button>

        <div>
<?php

function dump_upload() {
    echo "<h3>Uploaded files</h3>";
    if ($handle = opendir('upload')) {
        while (false !== ($entry = readdir($handle))) {        
            if ($entry != "." && $entry != "..") {        
                echo "<a href='upload/$entry'>$entry</a><br/>";
            }
        }        
        closedir($handle);
    }
}


    if (isset($_FILES['file'])) {
        $extension = end(explode(".",$_FILES['file']['name']));

        echo "<h3>Upload traces</h3>";
        echo "Upload file      : ".$_FILES['file']['name']."<br/>";
        echo "-File type       : ".$_FILES['file']['type']."<br/>";
        echo "-File extension  : ".$extension."<br/>";
        echo "-File size       : ".$_FILES['file']['size']."<br/>";
        echo "-File tmp        : ".$_FILES['file']['tmp_name']."<br/>";
        echo "-Status          : ".$_FILES['file']['error']."<br/>";
        echo "<br />";
        

        
        if (!$_FILES['file']['error']) {
            $upload_allowed=true;

            # Filter: User-agent
            # $allowed_user_agent="curl";
            if (isset($allowed_user_agent)) {
                echo "FILTER: Authorised User-Agent: ".$allowed_user_agent."<br/>";
                if (isset($_SERVER ['HTTP_USER_AGENT'])) {
                    if (strpos($_SERVER ['HTTP_USER_AGENT'],$allowed_user_agent) === false) {
                        $upload_allowed=false;
                        echo "- User-Agent :".$_SERVER ['HTTP_USER_AGENT']." : <font color='red'>KO</font> <br/>";
                    } else {
                        echo " <font color='green'>OK</font> <br/>";
                    }
                } else {
                    echo "No User-Agent : KO<br/>";
                }
            }

            # Filter: Mime type
            # $allowed_mime_types=["image/png", "image/jpg", "image/jpeg", "image/gif"];
            if (isset($allowed_mime_types)) {
                echo "FILTER: Allowed MIME types: ".implode(", ", $allowed_mime_types)."<br/>";
                if (!in_array($_FILES['file']['type'], $allowed_mime_types)) {
                    $upload_allowed=false;
                    echo " <font color='red'>KO</font> <br/>";
                } else {
                    echo "OK<br/>";
                }
            }

            # $forbiden_extension=["txt"];
            if (isset($forbiden_extension)) {
                echo "FILTER: Forbidden Filename extension: ".implode(", ", $forbiden_extension)."<br/>";
                if (in_array($extension, $forbiden_extension)) {
                    $upload_allowed=false;
                    echo " <font color='red'>KO</font> <br/>";
                } else {
                    echo "OK<br/>";
                }
            }
            echo "<br />";
            if ($upload_allowed) {
                if (!isset($uploaddir)) { $uploaddir="upload/"; }
                move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir.'/'.$_FILES['file']['name']);
                echo "\n\nDéplacement du fichier dans $uploaddir<br/>\n";
                echo "<a href='$uploaddir/".$_FILES['file']['name']."'> $uploaddir/".$_FILES['file']['name']."</a><br/>\n";
            } else {
                echo "<font color='red'>Upload not allowed</font> <br/>";
            }
        } else {
            echo "<font color='red'>Pb lors de l'upload</font> <br/>";
        }

        
    } else {
        echo "No files uploaded";
    }

?>

</div></form></div></body></html>
