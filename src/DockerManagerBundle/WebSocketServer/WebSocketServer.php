<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 24/09/18
 * Time: 21:31
 */

namespace DockerManagerBundle\WebSocketServer;

use DockerManagerBundle\Exceptions\ProcessStoppedException;
use DockerManagerBundle\WebSocketServer\MessageHandlers\AuthentificationMessageHandler;
use DockerManagerBundle\WebSocketServer\MessageHandlers\ExecuteMessageHandler;
use DockerManagerBundle\WebSocketServer\MessageHandlers\ForceStopMessageHandler;
use DockerManagerBundle\WebSocketServer\MessageHandlers\GetStatusMessageHandler;
use DockerManagerBundle\WebSocketServer\MessageHandlers\InputMessageHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Lide\CommonsBundle\Entity\Environment;
use Psr\Http\Message\RequestInterface;
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
    private $availableEnvironment;

    /**
     * @var MessageHandler[]
     */
    private $handlers;
    /**
     * @var UserManagerFactory
     */
    private $userManagerFactory;
    /**
     * @var string
     */
    private $projectsFolder;

    /**
     * WebSocketServer constructor.
     * @param LoggerInterface $logger
     * @param UserManagerFactory $userManagerFactory
     * @param string $projectsFolder
     * @param $availableEnvironment
     */
    public function __construct(LoggerInterface $logger, UserManagerFactory $userManagerFactory, string $projectsFolder, $availableEnvironment)
    {
        $this->clients = new \SplObjectStorage();
        $this->logger = $logger;
        $this->users = [];
        $this->availableEnvironment = $availableEnvironment;

        $this->handlers = [ //TODO inject this
            ExecuteMessageHandler::$Type => new ExecuteMessageHandler(),
            ForceStopMessageHandler::$Type => new ForceStopMessageHandler(),
            GetStatusMessageHandler::$Type => new GetStatusMessageHandler(),
            InputMessageHandler::$Type => new InputMessageHandler(),
            AuthentificationMessageHandler::$Type => new AuthentificationMessageHandler(),
        ];

        $this->userManagerFactory = $userManagerFactory;
        $this->projectsFolder = $projectsFolder;
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

        echo "User connected {$conn->resourceId}\n";

        /** @var RequestInterface $request */
        /** @noinspection PhpUndefinedFieldInspection */
        $request = $conn->httpRequest;


        /** @noinspection PhpUndefinedFieldInspection */
        $userManager = $this->userManagerFactory->create($conn, ""); //TODO attach user entity
        /** @noinspection PhpUndefinedFieldInspection */
        $this->users[$conn->resourceId] = $userManager;
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    public function onClose(ConnectionInterface $conn)
    {
        echo "Connection closes {$conn->resourceId}\n";
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
        echo "Message `$msg``\n";
        //Limit to 4 depth because message shouldn't have more
        $messageData = json_decode($msg, true, 4);

        //TODO erro handling

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

        if (! $userManager->isAuthentificated() && $type != AuthentificationMessageHandler::$Type) {
            $from->send(json_encode([
                'type' => 'error',
                'data' => [ 'message' => 'Need to authenticate first' ]
            ]));
            $from->close();
        }

        if (!array_key_exists($type, $this->handlers)) {
            echo "No handler for message of type \"${type}\"\n";
            return;
        }
        $handler = $this->handlers[$type];

        $ret = $handler->handle($userManager, $type, $messageData['data']);
        echo "Retour handler : $ret\n";
        if ($type === AuthentificationMessageHandler::$Type && !$ret) {
            $from->send(json_encode([
                'type' => 'error',
                'data' => [ 'message' => 'Auth error' ]
            ]));
            $from->close();
        }
    }

    public function retrieveDockerOutput()
    {
        foreach ($this->users as $user) {
            /** @var UserManager $user */

            /** @var ProcessManager $processManager */
            $processManager = $user->getProcessManager();
            if ($processManager != null) {
                try {
                    $out = $processManager->readOutput(1024);
                    $err = $processManager->readErrorOutput(1024);

                    var_dump($out);
                    var_dump($err);

                    $response = [];

                    $hasOut = false;
                    if (!empty($out)) {
                        $response['stdout'] = $out;
                        $hasOut = true;
                    }
                    if (!empty($err)) {
                        $response['stderr'] = $err;
                        $hasOut = true;
                    }

                    $running = $user->isContainerRunning();

                    if ($hasOut) {
                        $user->getConnection()->send(json_encode(['type' => 'out', 'data' => $response], JSON_PRETTY_PRINT));
                    }
                    if (!$running) {
                        $user->stopContainer();
                    }
                } catch (ProcessStoppedException $e) {
                    $user->stopContainer();
                }
            }
        }
    }
}
