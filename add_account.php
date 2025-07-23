#! /usr/bin/env php

<?php

require_once("panoseti.inc");

function add_account_old($name, $passwd) {
    $passwd_path = '/home/wei/Projects/web/passwd.json';
    $passwds = json_decode(file_get_contents($passwd_path));
    if (isset($passwds->$name)) {
        die("An account with that name already exists\n");
    }
    $u = new stdClass;
    $u->auth =  bin2hex(random_bytes(16));
    $u->passwd_hash = hash_passwd($passwd);
    $passwds->$name = $u;
    file_put_contents($passwd_path, json_encode($passwds, JSON_PRETTY_PRINT));

    echo "Added account with name '$name' and passwd '$passwd'\n";
}

function add_account($name, $passwd) {
    $passwd_path = '/home/wei/Projects/web/passwd.json';

    if (file_exists($passwd_path)) {
        $json = file_get_contents($passwd_path);
        $passwds = json_decode($json);
        if (!is_object($passwds)) {
            $passwds = new stdClass();
        }
    } else {
        $passwds = new stdClass();
    }

    if (isset($passwds->$name)) {
        echo "Error: An account with name '$name' already exists.\n";
        return false;
    }

    $u = new stdClass();
    $u->auth = bin2hex(random_bytes(16));
    $u->passwd_hash = hash_passwd($passwd);

    $passwds->$name = $u;

    $json_data = json_encode($passwds, JSON_PRETTY_PRINT);
    if (file_put_contents($passwd_path, $json_data) === false) {
        echo "Error: Failed to write to '$passwd_path'.\n";
        return false;
    }

    echo "✅ Added account: '$name'\n";
    return true;
}

$stdin = fopen('php://stdin', 'r');
echo "Create a PanoSETI account\n";
echo "User name: ";
$name = trim(fgets($stdin));
echo "Password: ";
$passwd = trim(fgets($stdin));

add_account($name, $passwd);

?>
