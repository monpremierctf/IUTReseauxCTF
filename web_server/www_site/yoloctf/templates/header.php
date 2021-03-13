<?php
//
// If header.custom.php exist, use it instead
//
//
require_once(__SITEROOT__.'/ctf_utils/ctf_challenges.php');
require_once(__SITEROOT__.'/ctf_utils/ctf_env.php'); 
require_once(__SITEROOT__.'/ctf_lang/ctf_lang.php');

if(file_exists(__SITEROOT__.'/templates/header.custom.php')) {
    include (__SITEROOT__.'/templates/header.custom.php');
} else {


function ctf_get_name() {
    global $ctf_subtitle;


    // Current dynamic CTF
    if (isset($_SESSION['ctfname'])&&($_SESSION['ctfname']!=='')) {
        return $_SESSION['ctfname'];
    } 
    // Server config
    if (isset($ctf_subtitle)&&($ctf_subtitle!=='')) {
        return $ctf_subtitle;
    }
    // Default
    return "Mon premier CTF";
    
}


function ctf_insert_logo($logo, $url) {

    if ($logo!=='') {
        if ($url!=='') { echo "<a href='$url'  target='_blank'>"; };
        echo "<img src='$logo'  height='60' alt='' >";
        if ($url!=='') { echo "</a>"; };
    }
}

?>




<!---- Header container ---->  
<div class="container-fluid">

    <!---- Right box with User profile ---->

        <div class="col-md-1 float-right">
<!---- Is Logged ---->
<?php if (isset($_SESSION['login'] )) { ?>
        <p><img class="row-md-auto float-center" src="img/player_02_200.png" width="80" height="80" alt="Participant" ></p>
        <div class="row-md-auto float-center font-weight-bold"><?php print  htmlspecialchars($_SESSION['login']) ?> </div>
        <button type="button" class="btn btn-default float-center btn-warning" id="Logout" value="Logout">Logout</button>
<!---- Is NOT Logged ---->
<?php } else { ?>
        <p><img class="row-md-auto float-center" src="img/player_02_200.png" width="80" height="80" alt="Participant" ></p>
        <div class="row-md-auto float-center font-weight-bold">anonymous</div>
        <button type="button" class="btn btn-default float-center btn-warning" id="Login" value="Login">Login</button>
<?php } ?>  

    </div>

    <!---- English/French choice ---->
    <div class="col-md-1 float-right">
        
        <?php if ($ctf_locale_enabled==='true') { ?>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButtonLang" 
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php print getLangage() ?>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonLang" id="MyLangList">
                <a class="dropdown-item" href="#" id="lang-fr">Français</a>
                <a class="dropdown-item" href="#" id="lang-en">English</a>
            </div>
        </div>
        <?php } ?>
        
        </div>

</div>  
 

<div class="jumbotron ctf-title text-center">
<div class="row">
    <div class="col-md-1"><?php ctf_insert_logo($ctf_logo1, $ctf_url1); ?></div>
    <div class="col-md-1"><?php ctf_insert_logo($ctf_logo2, $ctf_url2); ?></div>
    <div class="col-md-8">
        <h1 class="row-md-8 ctf-title-size"><?php echo "$ctf_title" ?></h1>
        <p><div class='row-md-8 ctf-subtitle ctf-subtitle-size'> <?php echo htmlspecialchars(ctf_get_name()); ?> </div></p>  
    </div>
    <div class="col-md-2"><?php ctf_insert_logo($ctf_logo3, $ctf_url3); ?></div>
</div>
</div>
<script> 
     
    $(document).ready(function() {
        // Login
        $("#Login").click(function(){
            window.location.href = "index.php?p=Login";
        }); 

        $("#Logout").click(function(){
            alert("Deconnection");
            window.location.href = "index.php?p=Logout";
        }); 

        // Language
        $("#lang-fr").click(function(e){
            $("#dropdownMenuButtonLang").html("fr");
            $.get("api/cmd_lang.php?cmd=setLang&lang=fr", function( data, status ) {
                if (data=="fr") { 
                    window.location.hash ="";
                    window.location.reload(); 
                }
            });
        
        });
        $("#lang-en").click(function(e){
            $("#dropdownMenuButtonLang").html("en");
            $.get( "api/cmd_lang.php?cmd=setLang&lang=en", function( data, status ) {
                if (data=="en") { 
                    window.location.hash ="";
                    window.location.reload(); 
                }
            });
        });
    });
</script>


<?php
// If header.custom.php exist, use it instead
}
?>