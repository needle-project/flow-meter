<?php
/**
 * This file is part of the NeedleProject\FlowMeter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\FlowMeter\Connector;

use NeedleProject\FlowMeter\Exception\ConnectorException;

/**
 * Class HttpApiConnector
 *
 * @package NeedleProject\FlowMeter\Connector
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 * @todo    Extend and interface, improve error handling, create a more flexible design
 */
class HttpApiConnector
{
    public static function getData(ConnectorRequestInterface $request): string
    {
        $url = $request->getPath();
        if (!empty($parameters)) {
            $url .= '?' . http_build_query($request->getParameters());
        }

        $call = curl_init($url);
        curl_setopt($call, CURLOPT_USERPWD, $request->getCredentials());
        curl_setopt($call, CURLOPT_TIMEOUT, 5);
        curl_setopt($call, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($call);
        curl_close($call);
        if (false === $result) {
            throw new ConnectorException(
                sprintf("Api call could not be completed, recevied %s", curl_error($call))
            );
        }
        return $result;
    }
}
