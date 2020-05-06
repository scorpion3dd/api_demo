<?php
/*
 * This file is part of the Simple REST Full API project.
 *
 * (c) Denis Puzik <scorpion3dd@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use api\RestFullAPI;

try {
    ob_start();
    ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_WARNING);
    spl_autoload_register();

    $api = new RestFullAPI();
    echo $api->run();
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}
