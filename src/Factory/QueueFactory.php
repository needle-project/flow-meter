<?php
namespace NeedleProject\FlowMeter\Factory;

use NeedleProject\FlowMeter\Resource\BrokerConnectionInterface;
use NeedleProject\FlowMeter\Resource\AmqpQueue;

/**
 * Class QueueFactory
 *
 * @package NeedleProject\FlowMeter\Factory
 */
class QueueFactory
{
    /**
     * @param string                                                      $aliasName
     * @param array                                                       $parameters
     * @param \NeedleProject\FlowMeter\Resource\BrokerConnectionInterface $connection
     * @return mixed
     */
    public static function create(string $aliasName, array $parameters, BrokerConnectionInterface $connection)
    {
        return (new AmqpQueue(
            $aliasName,
            $parameters['name'],
            $parameters
        ))->setConnection($connection);
    }
}
