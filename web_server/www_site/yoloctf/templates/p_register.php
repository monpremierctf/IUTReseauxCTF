<?php
/*
    INPUT: 
        $_GET['validate']
        $_POST['login']
        $_POST['password']
        $_POST['code']
        $_POST['mail']
        $_POST['pseudo']
    CMD: 
        none
	GLOBAL : $_SESSION

    */

?>

  
<div class="container-fluid">
    <div class="row">
        
        <div class="col">
        <div class="container">



    <div class="col-sm-10 text-center">
	  <form action=""  method="post">
		<div class="form-group text-left row">
		  <label for="usr" class="col-2">Login (*)</label>
		  <input type="text" class="col-6 form-control" id="login" name="login">
          <label for="usr" class="col-2">Votre identifiant de connection</label>
        </div>
        <div class="form-group text-left  row ">
		  <label for="usr" class="col-2">Password (*)</label>
		  <input type="password" class="col-6 form-control" id="password" name="password">
          <label for="usr" class="col-2"></label>
        </div>
        <div class="form-group text-left  row ">
		  <label for="usr" class="col-2">Mail</label>
		  <input type="text" class="col-6 form-control" id="mail" name="mail">
          <label for="usr" class="col-2"></label>
        </div>
        <!---
        <div class="form-group text-left  row ">
		  <label for="usr" class="col-2">Pseudo</label>
		  <input type="text" class="col-6 form-control" id="pseudo" name="pseudo">
          <label for="usr" class="col-2">Le Pseudo à afficher sur le tableau de score à la place du login.</label>
        </div>
        -->
        <?php if (isset($ctf_register_code)&&($ctf_register_code!='')) { ?>
        <div class="form-group text-left  row ">
		  <label for="usr" class="col-2">Code Invitation (*)</label>
		  <input type="text" class="col-6 form-control" id="code" name="code">
          <label for="usr" class="col-2"></label>
		</div>
        <?php } ?>

        <?php  if (isset($ReCaptchaEnabled)&&($ReCaptchaEnabled === "true")) { ?>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <div class="field">
               <div class="g-recaptcha" data-sitekey="6LdAw9EUAAAAAGLowra3GcVcI-gfCk7B1465Q0z3"></div>
        </div>
        <?php } ?>
        
		<button type="submit" class="btn btn-primary" onclick="return checkRegisterForm()">Register</button>
	  </form>
    </div>


    <script>
        function checkRegisterForm()
        {
            // Check name is available

            // Check fields are filled
            
            //alert("checkRegisterForm");
            return true;
        }
    </script>


        </div>
        </div>
    </div>
</div>


