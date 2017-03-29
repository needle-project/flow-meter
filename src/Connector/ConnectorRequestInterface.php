<?php
/**
 * This file is part of the NeedleProject\FlowMeter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\FlowMeter\Connector;

/**
 * Interface ConnectorRequestInterface
 *
 * @package NeedleProject\FlowMeter\Connector
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 * @todo    Refactor namespace, establish interface contract
 */
interface ConnectorRequestInterface
{
    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @return string
     */
    public function getCredentials();

    /**
     * @return array
     */
    public function getParameters(): array;
}
