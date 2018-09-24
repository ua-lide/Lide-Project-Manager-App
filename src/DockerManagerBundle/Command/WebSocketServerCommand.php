<?php

namespace DockerManagerBundle\Command;

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
     * @var int
     */
    private $port;


    /**
     * WebSocketServerCommand constructor.
     * @param int $port the port the server will listen to
     */
    public function __construct(int $port = 8000)
    {
        $this->port = $port;
        parent::__construct();
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
    }

}