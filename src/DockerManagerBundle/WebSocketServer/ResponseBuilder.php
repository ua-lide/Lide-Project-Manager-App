<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 01/10/18
 * Time: 16:01
 */

namespace DockerManagerBundle\WebSocketServer;

class ResponseBuilder
{
    public static $STDOUT = 'stdout';
    public static $STDERR = 'stderr';
    public static $STATUS = 'status';
    public static $END = 'end';
    /**
     * @var string
     */
    private $type;

    private $data = [];

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function buildArray(): array
    {
        return [
            'type' => $this->type,
            'data' => $this->data
        ];
    }

    public function buildString(): string
    {
        return json_encode($this->buildArray());
    }

    /**
     * @param string $field
     * @param string|array|object $content
     */
    public function addDataField(string $field, $content)
    {
        $this->data[$field] = $content;
    }
}
