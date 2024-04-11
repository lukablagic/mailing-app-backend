<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Websocket\ThreadWebSocket;
use Config\Database;

require_once '../vendor/autoload.php';


    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new ThreadWebSocket()
            )
        ),
        8080
        ,
        '127.0.0.1'
    );

    $server->run();