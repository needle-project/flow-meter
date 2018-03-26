<?php
namespace NeedleProject\FlowMeter\Application;

/**
 * Class FlowReport
 *
 * @package NeedleProject\FlowMeter\Application
 */
class FlowReport
{
    /**
     * @var string
     */
    private $rawData;

    /**
     * FlowReport constructor.
     *
     * @param string $data
     */
    public function __construct(string $data)
    {
        $this->rawData = $data;
    }

    /**
     * @return array
     */
    public function fetchForConsole(): array
    {
        $result = explode("\n", $this->rawData);

        $data = [];
        foreach ($result as $item) {
            $temp = json_decode($item, true);
            if (empty($temp)) {
                continue;
            }
            $keys = array_keys($temp);
            foreach ($keys as $key) {
                if (!isset($data[$key])) {
                    $data[$key] = [];
                }
                foreach ($temp[$key] as $microTime => $value) {
                    $data[$key][$microTime] = $value;
                }
            }
        }
        $finalData = [];
        foreach ($data as $alias => $values) {
            $finalData[$alias] = $this->analyseData($values);
        }
        return $finalData;
    }

    public function analyseData($data)
    {
        $startConsumming = null;
        $endConsuming = null;
        $ack_list = [];
        $pub_list = [];
        $rdl_rate = [];
        $message_count = 0;
        foreach ($data as $microtime => $values) {
            if ($values['pub_rate'] === 0 && $values['ack_rate'] === 0) {
                if ($startConsumming !== null) {
                    $endConsuming = $microtime;
                }
                continue;
            }
            if ($startConsumming === null) {
                $startConsumming = $microtime;
            }
            $ack_list[] = $values['ack_rate'];
            $pub_list[] = $values['pub_rate'];
            $rdl_rate[] = $values['rdl_rate'];
        }
        if ($endConsuming === null) {
            $endConsuming = $microtime;
        }
        $result = [];
        $result['start'] = $startConsumming;
        $result['end'] = $endConsuming;
        $result['time'] = $endConsuming - $startConsumming;

        $result['ack_rate_max'] = !empty($ack_list) ? max($ack_list) : 0;
        $result['ack_rate_min'] = !empty($ack_list) ? max($ack_list) : 0;
        $result['ack_rate_avg'] = !empty($ack_list) ? array_sum($ack_list) / count($ack_list) : 0;

        $result['pub_rate_max'] = max($pub_list);
        $result['pub_rate_min'] = min($pub_list);
        $result['pub_rate_avg'] = array_sum($pub_list) / count($pub_list);

        return $result;
    }
}
