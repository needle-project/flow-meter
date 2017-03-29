<?php
namespace NeedleProject\FlowMeter\Builder;

use NeedleProject\FlowMeter\Factory\QueueFactory;
use NeedleProject\FlowMeter\Resource\AmqpQueue;
use NeedleProject\FlowMeter\Resource\BrokerConnectionInterface;

/**
 * Class QueueBuilder
 *
 * @package NeedleProject\FlowMeter\Builder
 */
class QueueBuilder
{
    /**
     * @param string                                                      $aliasName
     * @param array                                                       $queueParameters
     * @param \NeedleProject\FlowMeter\Resource\BrokerConnectionInterface $connection
     * @return \NeedleProject\FlowMeter\Resource\AmqpQueue
     */
    public function buildQueue(
        string $aliasName,
        array $queueParameters,
        BrokerConnectionInterface $connection
    ): AmqpQueue {
        return QueueFactory::create($aliasName, $queueParameters, $connection);
    }
}
