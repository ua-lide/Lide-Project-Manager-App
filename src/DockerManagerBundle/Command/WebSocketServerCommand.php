<?php

namespace DockerManagerBundle\Command;

use DockerManagerBundle\WebSocketServer\WebSocketServer;
use Psr\Log\LoggerInterface;
use Ratchet\Server\IoServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class WebSocketServerCommand
 * @package DockerManagerBundle\Command
 * @author Paulin Violette
 * @since 1.0
 */
class WebSocketServerCommand extends Command
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
     * WebSocketServerCommand constructor.
     * @param LoggerInterface $logger
     * @param int $port
     */
    public function __construct(LoggerInterface $logger, int $port)
    {
        parent::__construct();
        $this->logger = $logger;
        $this->port = $port;
    }

    protected function configure()
    {
        $this->setName('dockermanager:start-server')
            ->setDescription('Start the websocket server')
            ->setHelp('Start the websocket server handling the users programs execution');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $wsServer = new WebSocketServer($this->logger);

        $server = IoServer::factory(
            $wsServer, //Need to wrap this into a WsServer into an HttpServer. I let it like this so i can test from terminal with telnet
            $this->port,
            "192.168.10.11"
        );

        $server->loop->addPeriodicTimer(0.5, function () use ($wsServer){
            $wsServer->retrieveDockerOutput();
        });
        $server->loop->addTimer(0.1, function () use ($output){
            $output->writeln("Server running on port " . $this->port);
        });

        $server->run();
    }

}