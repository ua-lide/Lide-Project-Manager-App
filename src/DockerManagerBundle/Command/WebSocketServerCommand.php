<?php

namespace DockerManagerBundle\Command;

use DockerManagerBundle\WebSocketServer\UserManagerFactory;
use DockerManagerBundle\WebSocketServer\WebSocketServer;
use Psr\Log\LoggerInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class WebSocketServerCommand
 * @package DockerManagerBundle\Command
 * @author Paulin Violette
 * @since 1.0
 */
class WebSocketServerCommand extends ContainerAwareCommand
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $port;

    /**
     * @var UserManagerFactory
     */
    private $userManagerFactory;
    /**
     * @var string
     */
    private $projectFolder;


    /**
     * WebSocketServerCommand constructor.
     * @param LoggerInterface $logger
     * @param int $port
     * @param UserManagerFactory $userManagerFactory
     * @param string $projectFolder
     */
    public function __construct(LoggerInterface $logger, int $port, UserManagerFactory $userManagerFactory, string $projectFolder)
    {
        parent::__construct();
        $this->logger = $logger;
        $this->port = $port;
        $this->userManagerFactory = $userManagerFactory;
        $this->projectFolder = $projectFolder;
    }

    protected function configure()
    {
        $this->setName('lide:start-server')
            ->setDescription('Start the websocket server')
            ->setHelp('Start the websocket server handling the users programs execution');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $environmentRepository = $this->getContainer()
//            ->get('doctrine')
//            ->getRepository("LideCommonsBundle:Environment");
//
//        $availableEnvironments = $environmentRepository->findBy([
//            ["activated" => true]
//        ]);
        $availableEnvironments = [];

        $wsServer = new WebSocketServer($this->logger, $this->userManagerFactory, $this->projectFolder, $availableEnvironments);

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    $wsServer //Need to wrap this into a WsServer into an HttpServer. I let it like this so i can test from terminal with telnet
                )
            ),
            $this->port,
            "192.168.10.11" //TODO inject this
        );

        //Set the timer to retrieve output of running dockers
        $server->loop->addPeriodicTimer(0.5, function () use ($wsServer) {
            $wsServer->retrieveDockerOutput();
        });
        $server->loop->futureTick(function () use ($output) {
            $output->writeln("Server running on port " . $this->port);
        });

        $server->run();
    }
}
