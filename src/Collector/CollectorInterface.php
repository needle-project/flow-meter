<?php
/**
 * This file is part of the NeedleProject\FlowMeter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\FlowMeter\Collector;

/**
 * Class FileCollector
 *
 * @package NeedleProject\FlowMeter\Collector
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 * @todo    Implement data formatter
 */
interface CollectorInterface
{
    /**
     * Collect monitored data based on the monitor and identifier
     * @param string $identifier
     * @param array  $data
     */
    public function collectData(string $identifier, array $data);
}
