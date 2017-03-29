<?php
namespace NeedleProject\FlowMeter\Resource;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class AmqpConnection implements BrokerConnectionInterface
{
    private $connectionName;
    private $host;
    private $port;
    private $http_port;
    private $user;
    private $password;
    private $vhost;

    public function __construct(
        string $connectionName,
        string $host,
        int $port,
        int $http_port,
        string $user,
        string $password,
        string $vhost
    ) {
        $this->connectionName = $connectionName;
        $this->host = $host;
        $this->port = $port;
        $this->http_port = $http_port;
        $this->user = $user;
        $this->password = $password;
        $this->vhost = $vhost;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->connectionName;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return int
     */
    public function getHttpPort(): int
    {
        return $this->http_port;
    }

    public function getHttpApi(): string
    {
        return sprintf(
            'http://%s:%d/api/',
            $this->getHost(),
            $this->getHttpPort()
        );
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getVhost(): string
    {
        return $this->vhost;
    }

    public function connect()
    {
        return new AMQPStreamConnection(
            $this->getHost(),
            $this->getPort(),
            $this->getUser(),
            $this->getPassword(),
            $this->getVhost()
        );
    }
}
