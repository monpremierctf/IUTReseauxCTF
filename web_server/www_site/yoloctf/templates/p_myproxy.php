
<script>
// Status at startup
$(document).ready(function() {
      var timer = null;
      tt = 60;
      $.get("containers_cmd.php?status=ctf-proxy", function(data) { 
          if (data=="Ko") {
           
            txt = "Votre proxy s\'initialise... Ca prend du temps... </br> Dans "+tt+" secondes vous cliquerez sur le lien ci-dessous... Pas avant hein ?</br>Un refresh de cette page va vous freezer le butineur..</br></br>Proxy status: En cours de creation.. Patience... ";
            $("#ProxyStatus").html(txt);

            timer1 = setInterval(function() {
                tt = tt-1;
                $('.Timer').text(tt + " Seconds");
                if (tt<30) {clearInterval(timer1);}
            }, 1000);

            $.get("containers_cmd.php?create=ctf-proxy", function(data) { 
                if (data=="ko") {
                    $("#ProxyStatus").html("Proxy status: Pb at creation");
                } else  {
                    var resp = JSON.parse(data);
                    //$("#ServerStatusctf-shell").html(resp.Name); 
                    var txt = "Proxy status: Running as "+resp.Name;
                    //txt = '<a href="https://'.$_SERVER['HTTP_HOST'].'/ctf-tool-xterm_'.$_SESSION['uid'].'/" ><pre>[Mon terminal]</pre></a>';
                    if (resp.Port  !== undefined ) { 
                    if (resp.Port  !=0) {
                        txt = txt + "</br>Host="+window.location.host; 
                        txt = txt + "</br>Port="+resp.Port; 
                        //txt = txt + "</br></br>Accès avancé possible en : IP="+window.location.host+" -PORT="+resp.Port;
                    }
                    }
                    $("#ProxyStatus").html(txt);
                }
            }); 
          } else  {
             var resp = JSON.parse(data);
             var txt = "Proxy status: Running as "+resp.Name;
             $("#ProxyStatus").html(txt);
          }
      }); 
      $.get("containers_cmd.php?status=ctf-mitmweb", function(data) { 
          if (data=="Ko") {
           
            txt = "L'interface de votre proxy s\'initialise... Ca prend du temps... </br>  ";
            $("#ProxyGuiStatus").html(txt);

            timer2 = setInterval(function() {
                tt = tt-1;
                $('.Timer').text(tt + " Seconds");
                if (tt<30) {clearInterval(timer2);}
            }, 1000);

            $.get("containers_cmd.php?create=ctf-mitmweb", function(data) { 
                if (data=="ko") {
                    $("#ProxyGuiStatus").html("Web interface proxy status: Pb at creation");
                } else  {
                    var resp = JSON.parse(data);
                    //$("#ServerStatusctf-shell").html(resp.Name); 
                    var txt = "Web Interface proxy status: Running as "+resp.Name;
                    //txt = '<a href="https://'.$_SERVER['HTTP_HOST'].'/ctf-tool-xterm_'.$_SESSION['uid'].'/" ><pre>[Mon terminal]</pre></a>';
                    if (resp.Port  !== undefined ) { 
                    if (resp.Port  !=0) {
                        txt = txt + "</br>Host="+window.location.host; 
                        txt = txt + "</br>Port="+resp.Port; 
                        //txt = txt + "</br></br>Accès avancé possible en : IP="+window.location.host+" -PORT="+resp.Port;
                    }
                    }
                    $("#ProxyGuiStatus").html(txt);
                }
            }); 
          } else  {
             var resp = JSON.parse(data);
             var txt = "Web Interface proxy status: Running as "+resp.Name;
             $("#ProxyGuiStatus").html(txt);
          }
      }); 


      
});


</script>


<?php
    
    if (isset($_SESSION['login'] )) {
        echo '<p id="ProxyStatus">Proxy status         : stopped</p></br>';
        echo '<p id="ProxyGuiStatus">Web interface status : stopped</p></br>';

        echo 'Proxy Web interface : <a href="https://'.$_SERVER['HTTP_HOST'].'/ctf-mitmweb_'.$_SESSION['uid'].'/" target="_blank"><pre>[Cliquer ICI pour ouvrir mon proxy dans un nouvel onglet]</pre></a>';
        echo "</br>";
        
        echo "Pour envoyer une requête via le proxy, ajouter le proxy en début de l'url :</br>";
        echo 'Navigateur web : http://'.$_SERVER['HTTP_HOST'].'/ctf-fileupload_'.$_SESSION['uid'].'/<br />';
        echo 'Proxy      web : http://'.$_SERVER['HTTP_HOST'].'<b>/proxy</b>/ctf-fileupload_'.$_SESSION['uid'].'/<br />';

        echo "</br>";
        echo "Sur le nouvel onglet : </br>";
        echo "- Si vous avez un '404 not found', l'instance n'est pas encore up. Atendez 60 secondes et faites un Refresh de la page.</br>";
        echo "- Si vous avez un 'Bad Gateway', l'instance est up et s'initialise. Attendez 10 secondes, et faites un Refresh de la page.</br></br>";


        
        
    } else {
        echo "Veuillez vous Identifier. Merci";
    }
    
?>
        

  