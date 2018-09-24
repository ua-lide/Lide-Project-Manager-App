<?php

namespace DockerManagerBundle\Command;

use Psr\Log\LoggerInterface;
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
        // TODO : create ratchet websocket server
        $output->writeln("Hello World");
        $output->writeln("Port : " . $this->port);
    }

}