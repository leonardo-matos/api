<?php
//conexao com o sql
    $host = '200.132.228.188';
    $user = 'sa';
    $pass = '$Administrador#Producao@2017';
    $banco = 'GVCollege_Oficial';
    $conexao = mssql_connect($host, $user, $pass) or die(mssql_get_last_message());
    mssql_select_db($banco) or die (mssql_get_last_message());
?>
 
<?php
//
    $sql = mssql_query("UPDATE ACD_TURMA 
    SET CODIGOSITUACAOTURMA = 7,
    DATAINICIO = DATAINICIO
    WHERE CODIGOTURMA = 1757");
    echo "Cadastro efetuado com sucesso";
?>