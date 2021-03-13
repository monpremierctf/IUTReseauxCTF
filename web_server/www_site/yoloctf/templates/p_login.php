<?php
/*
    INPUT
      $_POST['login']
      $_POST['password']
    */


include(__SITEROOT__ . "/ctf_utils/ctf_env.php");
require_once(__SITEROOT__ . "/ctf_lang/ctf_locale.php");


?>

<div class="row">
  <div class="col-sm-1">

  </div>
  <div class="col-sm-4 text-center">
    <h3><?php print getLocalizedLabel("login_with_account") ?></h3>
    <p><img src="img/player_02_200.png" alt="Participant"></p>
    <form action="" method="post">
      <div class="form-group text-left">
        <label for="usr">Login</label>
        <input type="text" class="form-control" id="login" name="login">
      </div>
      <div class="form-group text-left">
        <label for="usr">Password</label>
        <input type="password" class="form-control" id="password" name="password">

        <?php if (isset($ReCaptchaEnabled) && ($ReCaptchaEnabled === 'true')) { ?>
          <script src="https://www.google.com/recaptcha/api.js" async defer></script>
          <div class="field">
            <label for="usr">Verification</label>
            <div class="g-recaptcha" data-sitekey="6LdAw9EUAAAAAGLowra3GcVcI-gfCk7B1465Q0z3"></div>
          </div>
        <?php } ?>

      </div>
      <button type="submit" class="btn btn-primary">Login</button>
    </form>
  </div>
  <div class="col-sm-4 text-center">
    <h3><?php print getLocalizedLabel("login_without_account") ?></h3>
    <p><img src="img/admin_02_200.png" alt="Anonymous"></p>
    <form action="index.php?p=Register" method="post">
      <div class="form-group text-center">
        <?php print getLocalizedLabel("login_create_account") ?>
      </div>
      <button type="submit" class="btn btn-primary">Register</button>
    </form>
  </div>
</div>
</div>