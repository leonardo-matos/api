<?php
    $db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 10.10.1.13)(PORT = 1521)))(CONNECT_DATA=(SID=LASALLE)))" ;
    if($c = OCILogon("api", "123456", $db))
    {
        echo "Successfully connected to Oracle.\n";
        OCILogoff($c);
    }
    else
    {
        $err = OCIError();
        echo "Connection failed." . $err[text];
    }
?>
