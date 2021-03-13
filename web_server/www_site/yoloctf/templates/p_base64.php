<script src="/yoloctf/js/moment.min.js"></script>
	<script src="/yoloctf/js/Chart.min.js"></script>
	<script src="/yoloctf/js/Chart_utils.js"></script>


<div class="col text-center">
<div class="col text-left"><h2>Base64</h2><br><br></div>
<div class="col text-center"> 

<!---- Base64 Encode  --->

    <div class="">
      <div class="row chall-titre bg-secondary text-white">
        <div class="col-sm text-left">Base64 Encode</div>
      </div>
        <div class="form-group text-left row">
		  <label  for="usr" class="col-2">Texte à encoder</label>
		  <textarea id="base64EncodeIn" rows = "5" cols = "60" name = "description"></textarea>
        </div>
		<div class="form-group text-left row">
          <label for="usr" class="col-2">Texte encodé</label>
          <textarea id="base64EncodeOut" rows = "5" cols = "60" name = "description"></textarea>
        </div>
        <button type="submit" class="btn btn-primary" onclick="onBase64Encode()">Encode</button>      
		<script>
		function onBase64Encode(){
			document.getElementById("base64EncodeOut").value = window.btoa(document.getElementById("base64EncodeIn").value);
		}
		</script>
    </div>

<div class="form-group text-left  row ">
<hr>
</div>

<!---- Base64 Decode  --->

    <div class="">
      <div class="row chall-titre bg-secondary text-white">
        <div class="col-sm text-left">Base64 Decode</div>
      </div>
        <div class="form-group text-left row">
		  <label  for="usr" class="col-2">Texte à décoder</label>
		  <textarea id="base64DecodeIn" rows = "5" cols = "60" name = "description"></textarea>
        </div>
		<div class="form-group text-left row">
          <label for="usr" class="col-2">Texte décodé</label>
          <textarea id="base64DecodeOut" rows = "5" cols = "60" name = "description"></textarea>
        </div>
        <button type="submit" class="btn btn-primary" onclick="onBase64Decode()">Decode</button>      
		<script>
		function onBase64Decode(){
			document.getElementById("base64DecodeOut").value = window.atob(document.getElementById("base64DecodeIn").value);
		}
		</script>
    </div>

<div class="form-group text-left  row ">
<hr>
</div>
  
