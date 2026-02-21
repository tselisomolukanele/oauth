<?php

phpinfo();

    $dbh = new PDO('pgsql:host=localhost;dbname=oauth', 'postgres', 'rO0tuser');

    $stmt = $dbh->prepare("SELECT * FROM oauth_client");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    print_r($result);   

?>