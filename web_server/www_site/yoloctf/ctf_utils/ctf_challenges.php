<?php

require_once(__SITEROOT__.'/ctf_lang/ctf_locale.php');

$string = file_get_contents(__SITEROOT__."/db/challenges.json");
$challenges = json_decode($string, true);
$string = file_get_contents(__SITEROOT__."/db/flags.json");
$flags = json_decode($string, true);
$string = file_get_contents(__SITEROOT__."/db/files.json");
$files = json_decode($string, true);
$string = file_get_contents(__SITEROOT__."/db/intros.json");
$intros = json_decode($string, true);
$string = file_get_contents(__SITEROOT__."/db/hints.json");
$hints = json_decode($string, true);



function getChallengeCount(){
  global $challenges;
  return $challenges['count'];
}

function dumpChallengesNames(){
  global $challenges;
  foreach ($challenges['results'] as $c) {
    print_r($c['id']);
    print("\n");
    print_r($c['name']);
    print("\n");
  }
}


function getFlagListForCategory($category) {
  global $challenges;
  $ret=[];
  foreach ($challenges['results'] as $c) {
	if ($c['category'] == $category) {
		$ret[] = $c['id'];
	}
  }
  return $ret;
}




function getIntro($cat){
  global $intros;
  foreach ($intros['results'] as $i) {
    if ($i['category']==$cat){
      return $i;
    }
  }
  return null;
}


function getIntroDocker($cat){
  global $intros;
  foreach ($intros['results'] as $i) {
    if ($i['category']==$cat){
      if (isset($i['docker']))
        return $i['docker'];
    }
  }
  return null;
}


function getChallengeById($challId){
  global $challenges;
  if (!$challenges) return null;
  foreach ($challenges['results'] as $c) {
    if ($c['id']==$challId){
      return $c;
    }
  }
  return null;
}

//

function getFirstFlag($id){
  global $flags;
  foreach ($flags['results'] as $f) {
    if ($f['challenge_id']==$id) {
       return $f['content'];
    }
  }  
  return false;
}
// 
function isChallWithFlag($id){
  global $flags;
  foreach ($flags['results'] as $f) {
    if ($f['challenge_id']==$id) {
       return true;
    }
  }  
  return false;
}

function mutateFlag($f) {
  preg_match_all("/_[^\]]*/", $f, $matches);
  $flag_content = ($matches[0][0]);
  $mutation = [
    'a' => ['a', 'A', '4'],
    'b' => ['b','B', '8','3'],
    'e' => ['e','E', '3'],
    'i' => ['i','I', '1'],
    'k' => ['k','K', 'X'],
    'o' => ['o','O', '0'],
    't' => ['t','t', '7'],
  ];
  $ret='flag';
  foreach (str_split($flag_content) as $c) {
    $char = $c;
    $c = strtolower($c);
    if (array_key_exists($c, $mutation)){
        $index = random_int(0, count($mutation[$c])-1);
        $char =  $mutation[$c][$index];
    }
    $ret=$ret.$char;
  }
  return $ret;
}

function getChallWithFlagType($id){
  global $flags;
  foreach ($flags['results'] as $f) {
    if ($f['challenge_id']==$id) {
       return $f['type'];
    }
  }  
  return '';
}

function qcm_get_entries($qcm_txt){
  $qcm_lines = explode("\n", $qcm_txt);
  $qcm_entries = [];
  foreach ($qcm_lines as $line) {
    $line = trim($line);
    if ((substr( $line, 0, 1 ) === "-") || (substr( $line, 0, 1 )=== "+")) {
        array_push($qcm_entries, $line);
    }
  }
  return $qcm_entries;
}


function qcm_get_labels($qcm_txt){
  $qcm_entries = qcm_get_entries($qcm_txt);
  $qcm_labels = [];
  foreach ($qcm_entries as $line) {
    $line = substr($line, 1);
    $line = trim($line);
    array_push($qcm_labels, $line);
  }
  return $qcm_labels;
}

function qcm_get_flag($qcm_txt){
  $qcm_entries = qcm_get_entries($qcm_txt);
  $qcm_flags = [];
  foreach ($qcm_entries as $index => $line) {
    $line = substr($line, 0, 1);
    if ($line==="+") {
    array_push($qcm_flags, $index);
    }
  }
  return json_encode($qcm_flags);
}


function qcm_is_multipleflag($qcm_txt){
  $qcm_entries = qcm_get_entries($qcm_txt);
  $qcm_flags = [];
  foreach ($qcm_entries as $index => $line) {
    $line = substr($line, 0, 1);
    if ($line==="+") {
    array_push($qcm_flags, $index);
    }
  }
  return count($qcm_flags)>1;
}

function isFlagValid($id, $flag){
  global $flags;
  $c = getChallengeById($id);
  if ($c['type']==="QCM") {
    $cf = qcm_get_flag($c['qcm']);
    if (strlen(trim($cf))==0) { return false; }
    if (strlen(trim($flag))==0) { return false; }
    if (strcmp($cf,$flag)==0) {
      return true;
    }
  } else {
    // Standard challenge
    foreach ($flags['results'] as $f) {
      if ($f['challenge_id']==$id) {
        //print "Found id";
        $a = trim($f['content']);
        $b = trim($flag);
        //var_dump ($a);
        //var_dump ($b);
        if (strcmp($a,$b)==0) {
          return true;
        }
        
      } 
    }  
  }
  
  return false;
}

function getChallValue($id){
  $chall = getChallengeById($id);  
  if (isset($chall['value'])) {
    return $chall['value'];
  } else {
    return 0;
  }
}


function getCategoryLabel($cat){
  $label="";
  $intro = getIntro($cat);

  if ($intro!=null) {
      $label = getLocalizedIndex($intro,'label');
  }
  return $label;
}

function getCategories(){
  global $challenges;
  $categories = array();
  foreach ($challenges['results'] as $c) {
    if (!in_array($c['category'], $categories)) {
      $categories[] = $c['category'];
    }    
  }
  return $categories;
}


function getCategory($cat){
  global $challenges;
  $category = array();
  foreach ($challenges['results'] as $c) {
    if ($c['category'] == $cat) {
      $category[] = $c;
    }    
  }
  return $category;
}


function debug() {
  global $challenges;
  global $flags;
  global $files;

  print_r($files);

  print_r($files['count']);
  print_r($files['results'][0]);
  foreach ($files['results'] as $f) {
      print_r($f['id']);
      print("\n");
      print_r($f['location']);
      print("\n");
  }
  print getChallengeCount();
}


function getChallengeFileLocation($fileId) {
  global $files;

  foreach ($files['results'] as $f) {
    if ($f['id']==$fileId) {
      return $f['location'];
    }    
  }
  return "";
}


function pre_process_desc_for_md($desc)
{
  // Remplacer \r\n et \r par \n et mettre des espaces autour de ```
  $desc =  str_replace ("\r\n", "\n", $desc);
  $desc =  str_replace ("\r", "\n", $desc);
  $desc =  str_replace ("\n\n", "\n \n", $desc);
  $desc =  str_replace ("\n```\n", "\n ``` \n", $desc);
  $desc_out="";

  $is_in_code=false; // Ne pas mettre de </br> dans un bloc de code ```
  foreach(preg_split('~[\n]~', $desc) as $line) {
    if (trim($line)=='.') { $line=" ";}
    if (strpos($line, "```") !== false) {
      $desc_out = $desc_out.$line." \n";
      $is_in_code = ! $is_in_code;
    } else {
      if ( $is_in_code) {
        if (trim($line)[0]=='|') { $line=substr($line,1);}
        $desc_out = $desc_out.$line." \n"; 
      } else {
        if (! ($desc_out=="" and $line=='')) {  // Si la première ligne est vide, on ne met pas de </br>
          $desc_out = $desc_out.$line."</br>\n "; 
        }
      }
    }
  } 
  return $desc_out;
}


function icon_gear() {
  print ('<svg class="bi bi-gear" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" d="M8.837 1.626c-.246-.835-1.428-.835-1.674 0l-.094.319A1.873 1.873 0 014.377 3.06l-.292-.16c-.764-.415-1.6.42-1.184 1.185l.159.292a1.873 1.873 0 01-1.115 2.692l-.319.094c-.835.246-.835 1.428 0 1.674l.319.094a1.873 1.873 0 011.115 2.693l-.16.291c-.415.764.42 1.6 1.185 1.184l.292-.159a1.873 1.873 0 012.692 1.116l.094.318c.246.835 1.428.835 1.674 0l.094-.319a1.873 1.873 0 012.693-1.115l.291.16c.764.415 1.6-.42 1.184-1.185l-.159-.291a1.873 1.873 0 011.116-2.693l.318-.094c.835-.246.835-1.428 0-1.674l-.319-.094a1.873 1.873 0 01-1.115-2.692l.16-.292c.415-.764-.42-1.6-1.185-1.184l-.291.159A1.873 1.873 0 018.93 1.945l-.094-.319zm-2.633-.283c.527-1.79 3.065-1.79 3.592 0l.094.319a.873.873 0 001.255.52l.292-.16c1.64-.892 3.434.901 2.54 2.541l-.159.292a.873.873 0 00.52 1.255l.319.094c1.79.527 1.79 3.065 0 3.592l-.319.094a.873.873 0 00-.52 1.255l.16.292c.893 1.64-.902 3.434-2.541 2.54l-.292-.159a.873.873 0 00-1.255.52l-.094.319c-.527 1.79-3.065 1.79-3.592 0l-.094-.319a.873.873 0 00-1.255-.52l-.292.16c-1.64.893-3.433-.902-2.54-2.541l.159-.292a.873.873 0 00-.52-1.255l-.319-.094c-1.79-.527-1.79-3.065 0-3.592l.319-.094a.873.873 0 00.52-1.255l-.16-.292c-.892-1.64.902-3.433 2.541-2.54l.292.159a.873.873 0 001.255-.52l.094-.319z" clip-rule="evenodd"/>
  <path fill-rule="evenodd" d="M8 5.754a2.246 2.246 0 100 4.492 2.246 2.246 0 000-4.492zM4.754 8a3.246 3.246 0 116.492 0 3.246 3.246 0 01-6.492 0z" clip-rule="evenodd"/>
  </svg>');
}


function getAllMyGroups(){
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


function count_challs_in_cat($cat) {
  global $challenges;
  $count=0;
  foreach ($challenges['results'] as $c) {
    if (($c['category']==$cat)&&($c['status']!='draft')) {
      $count+=1;
    }
  }
  return $count;
}

function html_dump_cat($cat) {
  global $challenges;
  global $files;
  global $hints;
  global $Parsedown;
  $printAuteur=true;
  
  // Avancement d'un groupe
  if (isset($_SESSION['login'])&&($_SESSION['login']==='sebastien.josset')) {
    print '<div><select class="btn btn-primary dropdown-toggle" id="GroupFilter" name = "dropdown">
                    <option value = "" selected></option>';
    $mygroups = getAllMyGroups();
    foreach ($mygroups as $c) {
        echo '					<option value = "'.$c.'">'.$c.'</option>';
    }
    echo '</select><button type="submit" class="btn btn-primary" onclick="groupStatus(\''.$cat.'\')">Avancement</button> ';            
    echo '</select><button type="submit" class="btn btn-primary" onclick="onDownloadCSV(\''.$cat.'\')">Download CSV</button> </div>';            
?>
<script>
var resultat=[];
function groupStatus(cat) {
		var groupFilter = $("#GroupFilter option:selected").text();
		if (groupFilter=="") {
			return;
		}
        var params ={CategoryStatusByGroup : cat};
        params['group']= groupFilter;
		$.get(
			"api/zen_data.php",
			params,
			function(data) {
                for (const chall of data) {   
                    console.log(chall.CHALLID+' '+chall.login+' '+chall.isvalid);   
                    // Add entry
                    if ( ! resultat[chall.CHALLID] ) { 
                        resultat[chall.CHALLID] = [];
                    }; 
                    if ( ! resultat[chall.CHALLID][chall.login]) { 
                        (resultat[chall.CHALLID])[chall.login] = {isvalid:0, tries:0 }
                    };  
                    
                    // Set value
                    if (chall.isvalid) { 
                        (resultat[chall.CHALLID])[chall.login].isvalid=1;
                        document.getElementById('user_done_'+chall.CHALLID).innerHTML += " "+chall.login;
                    } else {
                        if ((resultat[chall.CHALLID])[chall.login].isvalid !=1) {
                            (resultat[chall.CHALLID])[chall.login].tries+=1;
                        }
                    }
                }               

			}
		);
	}
    synthese_csv="";
    function dict_to_synthese_csv(){
        synthese_csv="";
        for (const chall in resultat) { 
            for (const user in resultat[chall]) { 
                synthese_csv += chall+";"+user+";"+resultat[chall][user].isvalid+";"+resultat[chall][user].tries+"\n";
            }
        }
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
    
    function onDownloadCSV(cat){
        dict_to_synthese_csv();
        downloadFile(synthese_csv, "synthese.csv");
    }
</script>
<?php
  } 
  
  
  // Dump challs
  $cat_challs = getCategory($cat);
  $nb_cat_challs = count_challs_in_cat($cat);  // Remove draft
  $index_cat_challs = 0;
  foreach ($challenges['results'] as $c) {
    if (($c['category']==$cat)&&($c['status']!='draft')) {
      $index_cat_challs = $index_cat_challs +1;
      print '<div class="ctf-chall-container">';
        
        // titre
        print '<div class="row chall-titre bg-secondary text-white">';
          print '<div class="col-sm-9 text-left">';
          print $index_cat_challs.' / '.$nb_cat_challs." - ";
          print getLocalizedIndex($c, 'name');
          print "</div>";
          print '<div class="col-sm-2 text-right">';
          if ($printAuteur) { if ($c['auteur'] ) { print ("Auteur : ".$c['auteur']." - "); }}
          print ($c['value']);

          print "</div>";
          print '<div class="dropdown">
            <button class="btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown" style="padding-top: 0px;
            padding-bottom: 0px;">';
            icon_gear(); 
          print '  <span class="caret"></span></button>
            <ul class="dropdown-menu" >
              <li><div class="dropdown-item" onClick="chall_edit('.$c['id'].');">Edit challenge</div></li>
              <li><div class="dropdown-item" onClick="chall_edit_save('.$c['id'].');">Save</div></li>
              <li><div class="dropdown-item" onClick="chall_edit_reset('.$c['id'].');">Reset</div></li>
              <li><div class="dropdown-item" onClick="chall_edit_submit('.$c['id'].');">Submit</div></li>
            </ul>
          </div>';

          print "<div></div>";
        print "</div>";


        // Description
        print '<div class="ctf-chall-container chall-desc">';
        $desc = getLocalizedIndex($c, 'description');
      
        
        $server="";
        // YOP : FIX : Get from Intro
        /*
        if ($cat==="Terminal") {$server="ctf-shell"."_".$_SESSION['uid'];}
        if ($cat==="Ghost in the Shell") {$server="ctf-shell"."_".$_SESSION['uid'];}
        if ($cat==="Privilege Escalation") {$server="ctf-escalation"."_".$_SESSION['uid'];}
        if ($cat==="SQLi") {$server="ctf-sqli"."_".$_SESSION['uid'];}
        if ($cat==="Buffer overflows") {$server="ctf-buffer"."_".$_SESSION['uid'];}
        if ($cat==="File Upload") {$server="ctf-transfert"."_".$_SESSION['uid'];}
        if ($cat==="Exploit") {$server="ctf-exploit"."_".$_SESSION['uid'];}
        if ($cat==="Python") {$server="ctf-python"."_".$_SESSION['uid'];}
        */
        // Global docker from [Intro]
        $docker = getIntroDocker($cat);
        if ($docker!==null) {$server=$docker."_".$_SESSION['uid'];}

        // Challenge specific docker
        if (isset($c['docker'])){
          if (($c['docker'])!="") {
            $server=$c['docker']."_".$_SESSION['uid'];
            
          }
        }        

        $urlproxy = "proxy"; //_".$_SESSION['uid'];
        $desc = str_replace("IPSERVER", $server, $desc);
        $desc = str_replace("CTF_UID", $_SESSION['uid']?$_SESSION['uid']:"yolo", $desc);
        $desc = str_replace("URLPROXY", $urlproxy, $desc);
		$desc = str_replace("HACKERSGUIDE", "toolbox/toolbox.php", $desc);
        if (isset($_SERVER['HTTP_HOST'])) {
            $IP_SERVER = $_SERVER['HTTP_HOST'];
            if (isset($_SESSION['hacklab'])) {
                $IP_SERVER = "".$_SESSION['hacklab'].".yoloctf.org";
            }
            $desc = str_replace("{IP_SERVER}", $IP_SERVER, $desc);
            
        }






        $desc_out = pre_process_desc_for_md($desc);
        //print $desc_out;
        print $Parsedown->text($desc_out);
        print "</div>";

        // Hints   
        foreach ($hints['results'] as $h) {
          if ($h['challenge_id']===$c['id']) {
            $desc = getLocalizedIndex($h, 'content');
            $desc = $Parsedown->text($desc);

            $desc = str_replace("IPSERVER", $server, $desc);
            $desc = str_replace("CTF_UID", $_SESSION['uid'], $desc);
            if (isset($_SERVER['HTTP_HOST'])) {
              $desc = str_replace("{IP_SERVER}", $_SERVER['HTTP_HOST'], $desc);
            }

            print '<div class="row chall-desc bg-light">';
            print '<div class="col-md-auto text-left">  <label for="usr">Indice:</label>  </div>
            <div class="col text-left"><label id="hint_'.$h['id'].'"  style="display: none;" >'.$desc.'</label></div>
            <div class="col-2 text-right"><button type="Button" class="btn btn-primary" onclick="ctf_toggle_hide(\'#hint_'.$h['id'].'\')">Afficher</button></div>';
            print "</div>";
            

          }
        }


        // Files
        foreach ($files['results'] as $f) {
          if ($f['challenge_id']===$c['id']) {
            print '<div class="row chall-desc bg-light">';
            print '
            <a href="downloadfile.php?id='.$f['id'].'" download> 
            <button  class="btn btn-primary">Download '.basename($f['location']).'</button>
            </a>';
            print "</div>";
          }
        }
        // Server
        if (isset($c['docker'])){
          if (($c['docker'])!="") {
            //echo $c['docker'];
            ctf_div_server_status($c['docker']);
          }
        }

        // QCM
        if (isset($c['qcm'])&&$c['qcm']!=""){
          echo "<table>";
          $index=0;
          foreach (qcm_get_labels($c['qcm']) as $qcm_entry) {
            $id = $c['id'];
            $isMultipleChoice = qcm_is_multipleflag($c['qcm']);
            echo "<tr><td> 
              <input type='checkbox' 
                class='qcm'
                name='$id' 
                value='$qcm_entry' 
                id='qcm_".$id."_".$index."'
                onClick='qcm_cb_clicked(this, \"$id\", \"#flag_$id\", $isMultipleChoice)'
            >$qcm_entry</td></tr>";
            $index = $index+1;
          }
          echo "</table>";
          


          print '<div class="row chall-desc bg-light">';
          print '
              <div class="col-md-auto text-left"><label for="usr">Flag:</label></div>
              <div class="col text-left">
                <input type="text" 
                  class="form-control" 
                  hidden="true"
                  id="flag_'.$c['id'].'" 
                  name="flag_'.$c['id'].'" 
                  onLoad="ctf_onload(\''.$c['id'].'\', \'#flag_'.$c['id'].'\')"
                  >
              </div>
              <script>$("#flag_'.$c['id'].'").ready(function(){ ctf_onload(\''.$c['id'].'\', \'#flag_'.$c['id'].'\') })</script>
              <div class="col-2 text-right"><button type="Submit" class="btn btn-primary" onclick="ctf_validate(\''.$c['id'].'\', \'#flag_'.$c['id'].'\')">Submit</button></div>';
          print "</div>";
          print '<div class="row chall-spacer">  </div>';

        }

        // Flag

		if (isChallWithFlag($c['id'])!=false ){
        if ((getChallWithFlagType($c['id'])=='mutation')) {
          print(mutateFlag(getFirstFlag($c['id'])));
        }
        print '<div class="row chall-desc bg-light">';
        print '
            <div class="col-md-auto text-left">
              <label for="usr">Flag:</label>
            </div>
            <div class="col text-left"><input type="text" class="form-control" id="flag_'.$c['id'].'" name="code" onLoad="ctf_onload(\''.$c['id'].'\', \'#flag_'.$c['id'].'\')"></div>
            <script>$("#flag_'.$c['id'].'").ready(function(){ ctf_onload(\''.$c['id'].'\', \'#flag_'.$c['id'].'\') })</script>
            <div class="col-2 text-right"><button type="Submit" class="btn btn-primary" onclick="ctf_validate(\''.$c['id'].'\', \'#flag_'.$c['id'].'\')">Submit</button></div>';
        print "</div>";
        print '<div class="row chall-spacer">  </div>';
		}
        
        // Done by
        print '<div id="user_done_'.$c['id'].'">  </div>';
		
        print "</div>";

    }
  }     
}


function get_active_users(){
  $sp = ini_get("session.save_path");
  if ($sp=="") { $sp = "/tmp";}
  $h = opendir($sp);
  $nb_users = 0;
  if ($h== false) return 1;
  while (($file = readdir($h))!=false){
      if (preg_match("/^sess/", $file)) $nb_users++;
  }
  //$nb_users = count(scandir($sp))-2;
  return $nb_users;
}


function ctf_div_server_status($id) {

//echo '</br>';
//echo 'HTTP_CLIENT_IP='.$_SERVER['HTTP_CLIENT_IP'].'</br>';
//echo 'HTTP_X_FORWARDED_FOR='.$_SERVER['HTTP_X_FORWARDED_FOR'].'</br>';  // Ok IP src, placé par traefik
//echo 'REMOTE_ADDR='.$_SERVER['REMOTE_ADDR'].'</br>';
//echo 'HTTP_HOST='.$_SERVER['HTTP_HOST'].'</br>';
echo '     
<p></p>
<p>Démarrez votre serveur dédié en cliquant sur [Start server].</p>
</br>
<p id="ServerStatus'.$id.'">Server status : stopped</p>
</br>
<p><button type="button" class="btn btn-default btn-warning" id="StartServer'.$id.'" value="StartServer">Start Server</button>
<button type="button" class="btn btn-default btn-warning" id="StopServer'.$id.'" value="StopServer">Stop Server</button></p>

<script>
// Status at startup
$(document).ready(function() {


      $.get("containers_cmd.php?status='.$id.'",function(data) { 
          if (data=="ko_not_logged" || data =="Merci de vous connecter.") {
              $("#ServerStatus'.$id.'").html("Server status: Please log in..");
          } else if (data=="ko") {
              $("#ServerStatus'.$id.'").html("Server status: Problem... Cant start");
          } else  {
             var resp = JSON.parse(data);
             //$("#ServerStatus'.$id.'").html(resp.Name); 
             var txt = "Server status: Running as "+resp.Name;
             if (resp.Port  !== undefined ) { 
               if (resp.Port  !=0) {
                 txt = txt + "</br>Host="+window.location.host; 
                 txt = txt + "</br>Port="+resp.Port; 
                 //txt = txt + "</br></br>Accès avancé possible en : IP="+window.location.host+" -PORT="+resp.Port;
               }
             }
             $("#ServerStatus'.$id.'").html(txt);
          }
      });



});

// Start button
$(document).ready(function() {

    $("#StartServer'.$id.'").click(function(){
        $("#ServerStatus'.$id.'").html("Server status: Starting...");
        $.get("containers_cmd.php?create='.$id.'",function(data) { 
            if (data=="ko") {
                $("#ServerStatus'.$id.'").html("Server status: Problem... Cant start");
            } else  {
               $("#ServerStatus'.$id.'").html(data);
               var resp = JSON.parse(data);
               //$("#ServerStatus'.$id.'").html(resp.Name); 

                var txt = "Server status: Running as "+resp.Name;
                /*
                if (resp.Port  !== undefined ) { 
                  if (resp.Port  !=0) {
                    txt = txt + "</br>Host="+window.location.host; 
                    txt = txt + "</br>Port="+resp.Port; 
                    //txt = txt + "</br></br>Accès avancé possible en : IP="+window.location.host+" -PORT="+resp.Port;
                  }
                }
                */
                $("#ServerStatus'.$id.'").html(txt);

            }
        });

    }); 

});
$(document).ready(function() {
    // Stop button
    $("#StopServer'.$id.'").click(function(){
      $("#ServerStatus'.$id.'").html("Server status: Stopping...");
      $.get("containers_cmd.php?terminate='.$id.'",function(data) { 
          if (data=="ko") {
              $("#ServerStatus'.$id.'").html("Server status: Problem... Cant stop");
          } else  {
             $("#ServerStatus'.$id.'").html("Server status: "+data);
          }
      });
    }); 
});

</script>
';

}
?>
