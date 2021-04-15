<?php

//metodo usado a partir do PHP 5.1.2
$conexao = oci_connect('api', '123456', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=10.10.1.13)(PORT=1521)))(CONNECT_DATA=(SID = LASALLE)))' , 'WE8ISO8859P1');

if (!$conexao) {
    $erro = oci_error();
    trigger_error(htmlentities($erro['message'], ENT_QUOTES), E_USER_ERROR);
exit;
}
$SQL = "SELECT * from oauth_users where username='201320442'";
  $stmt = oci_parse($conexao, $SQL);

// $client_id = '201603281037app.deliver.com.br';

//   $stmt = oci_parse($conexao, sprintf('SELECT * from %s where client_id = :client_id','oauth_clients'));
//         oci_bind_by_name($stmt,':client_id',$client_id);



        // oci_execute($stmt);



  oci_execute($stmt, OCI_DEFAULT);
  echo " <BR>***- Recuperando da tabela DEMANDA -***<br>\n";
  $result = @array_change_key_case(oci_fetch_assoc($stmt),CASE_LOWER);
  echo json_encode($result);

  while (oci_fetch($stmt)) {
      echo  oci_result($stmt, 'USERNAME');
  }
  echo " ***- FIM DA EXECUCAO -***<br>\n"; 

var_dump(json_encode($conexao));
exit;