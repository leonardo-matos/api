<?php

/*
Remove todos os arquivos da documentação antiga
*/
//shell_exec( glob("../../documents/classes/*"));
// Gera a nova documentação
shell_exec("php -f ". getcwd().'/../apigen.phar generate --source ../../v1/src  --destination ../../documents/classes --template-theme bootstrap --title "API deliver"');
?>