<?php
/**
 * This file is part of the NeedleProject\FlowMeter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\FlowMeter\Factory;

use NeedleProject\FlowMeter\Resource\AmqpConnection;
use NeedleProject\FlowMeter\Resource\BrokerConnectionInterface;

/**
 * Class ConnectionFactory
 *
 * @package NeedleProject\FlowMeter\Factory
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 */
class ConnectionFactory
{
    /**
     * @param string $aliasName
     * @param array  $parameters
     * @return \NeedleProject\FlowMeter\Resource\BrokerConnectionInterface
     */
    public static function create(string $aliasName, array $parameters): BrokerConnectionInterface
    {
        return new AmqpConnection(
            $aliasName,
            $parameters['host'],
            $parameters['port'],
            $parameters['http_port'],
            $parameters['user'],
            $parameters['pass'],
            $parameters['vhost']
        );
    }
}
