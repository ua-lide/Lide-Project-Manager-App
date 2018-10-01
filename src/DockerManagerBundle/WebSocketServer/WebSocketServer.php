<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 24/09/18
 * Time: 21:31
 */

namespace DockerManagerBundle\WebSocketServer;

use DockerManagerBundle\WebSocketServer\MessageHandlers\ExecuteMessageHandler;
use Lide\CommonsBundle\Entity\Environment;
use Psr\Log\LoggerInterface;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class WebSocketServer implements MessageComponentInterface
{

    /**
     * @var \SplObjectStorage|ConnectionInterface[]
     */
    protected $clients;

    /**
     * @var UserManager[]
     */
    protected $users;


    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Environment[]
     */
    private $availableEnvironement;

    /**
     * @var MessageHandler[]
     */
    private $handlers;

    /**
     * WebSocketServer constructor.
     * @param LoggerInterface $logger
     * @param $availableEnvironment
     */
    public function __construct(LoggerInterface $logger, $availableEnvironment)
    {
        $this->clients = new \SplObjectStorage();
        $this->logger = $logger;
        $this->users = [];
        $this->availableEnvironement = $availableEnvironment;

        $this->handlers = [ //TODO inject this
            "execute" => new ExecuteMessageHandler(),
        ];
    }


    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        /** @noinspection PhpUndefinedFieldInspection */
        $this->logger->info("New connection " . $conn->resourceId);

        /** @noinspection PhpUndefinedFieldInspection */
        $userManager = new UserManager($conn); //TODO attach user entity
        /** @noinspection PhpUndefinedFieldInspection */
        $this->users[$conn->resourceId] = $userManager;

        $conn->send("Connected");
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        /** @noinspection PhpUndefinedFieldInspection */
        unset($this->users[$conn->resourceId]);

        /** @noinspection PhpUndefinedFieldInspection */
        $this->logger->info("Connection {$conn->resourceId} has disconnected\n");
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        // TODO: Implement onMessage() method.
        /** @noinspection PhpUndefinedFieldInspection */

        $messageData = json_decode($msg, true, 4);

        /** @noinspection PhpUndefinedFieldInspection */
        $userManager = $this->users[$from->resourceId];
        if (is_null($userManager)) {
            /** @noinspection PhpUndefinedFieldInspection */
            throw new \RuntimeException("No user manager for connection " . $from->resourceId);
        }

        if (!isset($messageData['type']) || !isset($messageData['data']) || !is_array($messageData['data'])) {
            return;
        }

        $type = (string)$messageData['type'];

        if (!array_key_exists($type, $this->handlers)) {
            return;
        }
        $handler = $this->handlers[$type];

        $handler->handle($userManager, $type, $messageData['data']);
    }

    public function retrieveDockerOutput()
    {
        // Dummy method
        foreach ($this->users as $user) {
            echo "Top\n";
            $out = $user->readOutput();

            $response = [];

            $response['stdout'] = $out;
            $response['stderr'] = $user->readStderr();
            $response['running'] = $user->isContainerRunning();

            if (!empty($response['stdout']) || !empty($response['stderr'] && !$response['running'])) {
                $user->getConnection()->send(json_encode($response, JSON_PRETTY_PRINT));
            }
        }
    }
}
