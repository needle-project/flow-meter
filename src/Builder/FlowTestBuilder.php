<?php
namespace NeedleProject\FlowMeter\Builder;

use NeedleProject\FlowMeter\Application\FlowTest;
use NeedleProject\FlowMeter\Collector\FileCollector;
use NeedleProject\FlowMeter\Factory\ConnectionFactory;
use NeedleProject\FlowMeter\Factory\QueueFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FlowTestBuilder
 *
 * Build a TestFlow
 *
 * @package NeedleProject\FlowMeter\Builder
 */
class FlowTestBuilder
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param array $configData
     * @param array $testData
     * @return \NeedleProject\FlowMeter\Application\FlowTest
     */
    public function buildFlowTest(array $configData, array $testData): FlowTest
    {
        $flowTest = new FlowTest($this->input, $this->output);

        $queues = $this->createQueues(
            $configData['queues'],
            $this->createAmqpConnections($configData['connections'])
        );
        $flowTest->setQueueList($queues)
            ->setInputData($testData)
            ->setTestQueue($configData['test']['start']['name'])
            ->setEndQueue($configData['test']['end']['name'])
            ->setCollector(new FileCollector(
                getcwd() . DIRECTORY_SEPARATOR . 'test_' . date('y-m-d_H_i') . '.log'
            ));
        if (isset($configData['test']['end']['min_wait_time']) &&
            isset($configData['test']['end']['max_wait_time'])) {
            $flowTest->setTestTimings(
                $configData['test']['end']['min_wait_time'],
                $configData['test']['end']['max_wait_time']
            );
        }

        return $flowTest;
    }

    /**
     * @param $connections
     * @return array
     */
    private function createAmqpConnections($connections): array
    {
        $buildConnections = [];
        foreach ($connections as $alias => $connectionParameters) {
            $buildConnections[$alias] = ConnectionFactory::create(
                $alias,
                $connectionParameters
            );
        }
        return $buildConnections;
    }

    /**
     * @param array $queueList
     * @param array $connectionList
     * @return array
     */
    private function createQueues(array $queueList, array $connectionList)
    {
        $buildQueues = [];
        foreach ($queueList as $alias => $queueParameters) {
            $buildQueues[$alias] = QueueFactory::create(
                $alias,
                $queueParameters,
                $connectionList[$queueParameters['connection']]
            );
        }
        return $buildQueues;
    }
}
