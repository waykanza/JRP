<?php

echo 'aaa';
$document_file = 'ass.doc';
$text_from_doc = shell_exec('/usr/local/bin/antiword '.$document_file);

?>
