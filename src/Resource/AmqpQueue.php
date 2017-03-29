<?php
namespace NeedleProject\FlowMeter\Resource;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use NeedleProject\FlowMeter\Connector\HttpApiConnector;
use NeedleProject\FlowMeter\Connector\ApiRequest;

class AmqpQueue
{
    private $name;

    private $arguments = [
        'passive' => false,
        'durable' => false,
        'exclusive' => false,
        'auto_delete' => false,
        'nowait' => false
    ];
    private $connection;

    public function __construct(string $queueAlias, string $queueName, $parameters)
    {
        $this->name = $queueAlias;
        $this->arguments['name'] = $parameters['name'];
        $this->arguments['passive'] = $parameters['passive'];
        $this->arguments['durable'] = $parameters['durable'];
        $this->arguments['exclusive'] = $parameters['exclusive'];
        $this->arguments['auto_delete'] = $parameters['auto_delete'];
        $this->arguments['nowait'] = $parameters['nowait'];
    }

    public function getAlias(): string
    {
        return $this->name;
    }

    public function getName(): string
    {
        return $this->arguments['name'];
    }

    public function getQueueName(): string
    {
        return $this->arguments['name'];
    }

    public function setConnection(BrokerConnectionInterface $connection): AmqpQueue
    {
        $this->connection = $connection;
        return $this;
    }

    public function getConnection(): BrokerConnectionInterface
    {
        if (is_null($this->connection)) {
            throw new \RuntimeException(
                sprintf("%s queue does not have a connection association!", $this->connection)
            );
        }
        return $this->connection;
    }

    public function publish($message)
    {
        // @todo - implement
    }

    public function publishMany(array $messages)
    {
        /** @var AMQPStreamConnection $connection */
        $connection = $this->getConnection()->connect();
        $channel = new AMQPChannel($connection);

        $tempexchage = md5('temp');
        $channel->exchange_declare($tempexchage, 'fanout', false, false);
        try {
            $channel->queue_bind($this->getName(), $tempexchage);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        foreach ($messages as $message) {
            $message = new AMQPMessage(json_encode($message));
            $channel->basic_publish(
                $message,
                $tempexchage,
                $this->getName()
            );
        }
        $channel->exchange_delete($tempexchage);
        $channel->close();
        $connection->close();
    }

    /**
     * @param array $extraParameters
     * @return mixed
     */
    public function getStats($extraParameters = [])
    {
        $url = $this->getConnection()->getHttpApi() .
            "queues/{$this->getConnection()->getVhost()}/{$this->getQueueName()}";
        $content = HttpApiConnector::getData(
            new ApiRequest(
                $url,
                $this->getConnection()->getUser(),
                $this->getConnection()->getPassword(),
                $extraParameters
            )
        );
        return $this->extractData($content);
    }

    /**
     * Extract only relevant data from stats
     * @param string $stats
     * @return array
     */
    private function extractData(string $stats): array
    {
        $stats = json_decode($stats, true);

        $return['consumers'] = $stats['consumers'];
        $return['message_count'] = $stats['messages'];
        $return['ack_rate'] = $stats['message_stats']['ack_details']['rate'];
        $return['pub_rate'] = $stats['message_stats']['publish_details']['rate'];
        $return['rdl_rate'] = $stats['message_stats']['redeliver_details']['rate'];
        $return['idle_since'] = $stats['idle_since'];
        $return['status'] = $stats['status'];
        return $return;
    }
}
