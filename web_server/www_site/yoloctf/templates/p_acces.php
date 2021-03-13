<div class="col text-center">
<div class="col text-left"><h2>Mon Accès</h2><br><br></div>
<div class="col text-center"> 

    <!---- Lab id  --->
    <div class="">
        <div class="row chall-titre bg-secondary text-white">
            <div class="col-sm text-left">HackLab</div>
        </div>
        <div class="form-group text-left row">
		  <label for="usr" class="col-2">HackLab</label>
		  <label for="usr" class="col-6" id="id" name="id">
              <?php echo isset($_SESSION['hacklab'])?htmlspecialchars($_SESSION['hacklab']):"Default"; ?>
          </label>
          <label for="usr" class="col-2"></label>
        </div>
        <div class="form-group text-left row">
		  <label for="usr" class="col-12">
          Le Hacklab est la machine sur laquelle sont instanciés: xterm, proxy, réseau privé et serveurs privés pour les challenges.
          </label>
        </div>
    </div>
    <div class="form-group text-left  row ">
    <hr>
    </div>


    <!---- OpenVPN  --->
    <div class="">
        <div class="row chall-titre bg-secondary text-white">
        <div class="col-sm text-left">OpenVPN</div>
        </div>

        <div class="form-group text-right row ">
          <label for="usr" class="col-2">Openvpn credentials</label>
          <button type="submit" class="btn btn-primary" onclick="return onDownloadOVPNFile()">Download .ovpn</button>      
        </div> 
        <div class="form-group text-left row">
		  <label for="usr" class="col-12">
          Si vous avez votre propre VM avec Kali sur votre PC, vous pouvez tirer un tunnel vers la plateforme.</br>
          Téléchargez le fichier et lancez:</br>
          <pre><code>
sudo openvpn <?php echo isset($_SESSION['login'])?htmlspecialchars($_SESSION['login']):"yolo"; ?>.ovpn
          </code></pre>
          </br>
          Vous obtenez une interface réseau de type tunnel qui porte le nom: tun0, avec une IP dynamique sur le réseau 10.70.0.xxx</br>
          Vous avez accès au réseau 10.71.0.xxx, sur lequel tournent les boxes à powner.</br>
          </label>
        </div>
    </div>
    <div class="form-group text-left  row ">
    <hr>
    </div>

    <!---- Guacamole  --->
    <div class="">
        <div class="row chall-titre bg-secondary text-white">
        <div class="col-sm text-left">Guacamole: Kali, Ubuntu</div>
        </div>

        <div class="form-group text-right row ">
          <label for="usr" class="col-2">Guacamole URL</label>
          <label for="usr" class="col-6">http://gui02.yoloctf.org/guacamole/</label>    
        </div> 
        <div class="form-group text-left row">
		  <label for="usr" class="col-12">
          Pour certains ateliers vous avez besoin d'un accès complet avec interface graphique à une Kali ou Ubuntu.</br>
          Guacamole est un portail vers des serveurs accessibles via le navigateur web.
          
          </label>
        </div>
    </div>
    <div class="form-group text-left  row ">
    <hr>
    </div>
</div>
</div>
<script>
    function onDownloadOVPNFile(){
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
        
        
        
        var getdata = {
            'OVPNFile': true,
        }
        $.get("api/api_downloadovpn.php", getdata)
            .done(function(data) {
                downloadFile(data, "<?php echo isset($_SESSION['login'])?htmlspecialchars($_SESSION['login']):"yolo"; ?>.ovpn");
            });
            
    }
//api/api_downloadovpn.php?OVPNFile
</script>