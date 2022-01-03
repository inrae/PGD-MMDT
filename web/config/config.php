<?php
#db credentials
$username =  rawurlencode('userr-pgd');
$password =  rawurlencode('rrrrr');

#docker mode : 0 = FALSE; 1 = TRUE;
$docker_mode = 0;

#------------------------
$database = 'pgd-db';
$collection = 'metadata';

$uritarget = '127.0.0.1';
if ($docker_mode==1){$uritarget='pgd-mmdt-db';}
$uri = "mongodb://$username:$password@$uritarget:27017/$database";
$client = new MongoDB\Driver\Manager($uri);
?>
