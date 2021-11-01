<?php

namespace App\Websocket;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class MessageHandler implements MessageComponentInterface
{
    //Objet utilisé comme tableau pout toutes les connections
    private $connections;

    public function __construct()
    {
        //SplObjectStorage est pareil que pour un tableau mais il s'agit d'une collection d'objets
        $this->connections = new \SplObjectStorage();
    }

    //Fonction appelée lors d'une nouvelle connexion
    function onOpen(ConnectionInterface $conn)
    {
        //Une nouvelle connexion est ajoutée à la collection de connexion
        $this->connections->attach($conn);
    }

    //Fonction appelée lors de la fermetture d'une connexion
    function onClose(ConnectionInterface $conn)
    {
        //Une connexion fermée est détachée de la collection de messages
        $this->connections->detach($conn);
    }

    //Fonction exécutée à la survenue d'une erreur
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        //Lors de la survenue d'une erreur, on déconnecte la connexion responsable
        $this->connections->detach($conn);
        $conn->close();
    }

    //Fonction appelée à la réception d'un message
    function onMessage(ConnectionInterface $from, $msg)
    {
        //Un message reçu est envoyé à toutes les connexions à l'exception de l'expéditeur
        foreach($this->connections as $connection)
        {
            if($connection === $from)
            {
                continue;
            }
            $connection->send($msg);
        }
    }
}