<?php
// namespace OAuth2\Storagez;
// //metodo usado a partir do PHP 5.1.2
// $conexao = oci_connect('sunidba', 'SUNIuni2016oR@sGl', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=10.10.1.13)(PORT=1521)))(CONNECT_DATA=(SID = LASALLE)))' , 'WE8ISO8859P1');

// if (!$conexao) {
//     $erro = oci_error();
//     trigger_error(htmlentities($erro['message'], ENT_QUOTES), E_USER_ERROR);
// exit;
// }
// $SQL = "SELECT * from api.oauth_clients";
//   $stmt = oci_parse($conexao, $SQL);
//   oci_execute($stmt, OCI_DEFAULT);
//   echo " <BR>***- Recuperando da tabela DEMANDA -***<br>\n";
//   while (oci_fetch($stmt)) {
//       echo  oci_result($stmt, 'CLIENT_ID');
//   }
//   echo " ***- FIM DA EXECUCAO -***<br>\n"; 

// var_dump(json_encode($conexao));
// exit;
use OAuth2\Storage\Oracle;

$teste =  new Oracle; 

echo '"', __NAMESPACE__, '"';