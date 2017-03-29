<?php
namespace NeedleProject\FlowMeter\Application;

use NeedleProject\FlowMeter\Collector\CollectorInterface;
use NeedleProject\FlowMeter\Resource\AmqpQueue;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FlowTest
 *
 * @package NeedleProject\FlowMeter\Application
 */
class FlowTest
{
    /**
     * @var array
     */
    private $queueList;

    /**
     * @var array
     */
    private $inputData;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $testQueue;

    /**
     * @var string
     */
    private $endQueue;

    /**
     * @var int
     */
    private $minWaitTime = 30;

    /**
     * @var int
     */
    private $maxWaitTime = 3600;

    /**
     * @var CollectorInterface
     */
    private $collector;

    /**
     * FlowTest constructor.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param array $queueList
     * @return \NeedleProject\FlowMeter\Application\FlowTest
     */
    public function setQueueList(array $queueList): FlowTest
    {
        $this->queueList = $queueList;
        return $this;
    }

    /**
     * @param array $data
     * @return \NeedleProject\FlowMeter\Application\FlowTest
     */
    public function setInputData(array $data): FlowTest
    {
        $this->inputData = $data;
        return $this;
    }

    /**
     * @param string $queueNameAlias
     * @return \NeedleProject\FlowMeter\Application\FlowTest
     */
    public function setTestQueue(string $queueNameAlias): FlowTest
    {
        $this->testQueue = $queueNameAlias;
        return $this;
    }

    /**
     * @param string $queueNameAlias
     * @return \NeedleProject\FlowMeter\Application\FlowTest
     */
    public function setEndQueue(string $queueNameAlias): FlowTest
    {
        $this->endQueue = $queueNameAlias;
        return $this;
    }

    /**
     * @param int|null $minWaitTime
     * @param int      $maxWaitTime
     * @return \NeedleProject\FlowMeter\Application\FlowTest
     */
    public function setTestTimings(int $minWaitTime, int $maxWaitTime): FlowTest
    {
        $this->minWaitTime = $minWaitTime;
        $this->maxWaitTime = $maxWaitTime;
        return $this;
    }

    /**
     * @param \NeedleProject\FlowMeter\Collector\CollectorInterface $collector
     * @return \NeedleProject\FlowMeter\Application\FlowTest
     */
    public function setCollector(CollectorInterface $collector): FlowTest
    {
        $this->collector = $collector;
        return $this;
    }

    /**
     * @param string $aliasName
     * @return \NeedleProject\FlowMeter\Resource\AmqpQueue
     */
    protected function getQueues(string $aliasName): AmqpQueue
    {
        if (!isset($this->queueList[$aliasName])) {
            throw new \RuntimeException(
                sprintf("Unknown queue %s", $aliasName)
            );
        }
        return $this->queueList[$aliasName];
    }

    /**
     * Start the test
     */
    public function start()
    {
        $startTiming = microtime(true);
        $this->output->writeln("<error>Started flow test</error>");
        $testQueue = $this->getQueues($this->testQueue);
        $testQueue->publishMany($this->inputData);

        $publishDurationTime = round(microtime(true) - $startTiming, 2);
        $this->output->writeln(
            sprintf(
                "Finished sending trigger data. Total of <error>%s</error> message send in %d sec.",
                count($this->inputData),
                $publishDurationTime
            )
        );
        // get publish queue data
        $this->collector->collectData(
            $testQueue->getAlias(),
            $testQueue->getStats([
                'msg_rates_age'  => $publishDurationTime,
                'msg_rates_incr' => 2
            ])
        );

        $this->monitorQueues($startTiming);
        $this->output->writeln(
            sprintf("<info>Finished test in %d sec.</info>", round(microtime(true) - $startTiming, 2))
        );
    }

    /**
     * Retrieve data from queue
     * @param $startTime
     */
    private function monitorQueues($startTime)
    {
        $progressBar = new ProgressBar($this->output);
        while (true) {
            $shouldStop = false;
            foreach ($this->queueList as $queueAliasName => $queue) {
                $progressBar->advance();
                $queueStats = $queue->getStats();
                $this->collector->collectData($queueAliasName, $queueStats);
                if ($queueAliasName === $this->endQueue &&
                    $this->shouldStop($startTime, $queueStats['idle_since'])) {
                    $shouldStop = true;
                }
            }
            if (true === $shouldStop) {
                break;
            }
            sleep(1);
        }
        $progressBar->finish();
        $this->output->writeln(''); // move buffer to the new line
    }

    /**
     * State if it's ok to stop monitoring
     * @param int $startTime
     * @param     $idleTime
     * @return bool
     */
    private function shouldStop(int $startTime, $idleTime): bool
    {
        $currentTime = microtime(true);
        $idleTimeSec = strtotime($idleTime);

        // if the queue is idle for more than 10 seconds
        $reachedExpectedIdleTime = false === is_null($idleTime) && ($currentTime - $idleTimeSec) > 15;
        // and we waiting at least a minimum time until we can expect queue activity
        $waitedMinTime = ($currentTime - $startTime) > $this->minWaitTime;
        // under the max time we want the test to run
        $underMaxTime = ($currentTime - $startTime) < $this->maxWaitTime;

        if ($reachedExpectedIdleTime && $waitedMinTime && $underMaxTime) {
            return true;
        }
        return false;
    }

    /**
     * show the results
     */
    public function showResult()
    {
        $file = $this->collector->getFilename();
        $file = getcwd() . DIRECTORY_SEPARATOR . 'test_17-03-29_01_10.log';
        $fileData = file_get_contents($file);
        #unlink($file);
        $flowReport = new FlowReport($fileData);
        $data = $flowReport->fetchForConsole();

        $table = new Table($this->output);
        $tableData = [];
        foreach ($data as $name => $values) {
            $tableData[] = [
                $name,
                $values['time'],
                $values['ack_rate_max'],
                $values['ack_rate_avg'],
                $values['pub_rate_max'],
                $values['pub_rate_avg']
            ];
        }
        $table
            ->setHeaders(array('Name', 'Duration', 'AckRate Max', 'AckRate Avg', 'PubRate Max', 'PubRate Avg'))
            ->setRows($tableData);
        $table->render();
    }
}
