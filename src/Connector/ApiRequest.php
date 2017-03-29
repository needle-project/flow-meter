<?php
/**
 * This file is part of the NeedleProject\FlowMeter package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NeedleProject\FlowMeter\Connector;

/**
 * Class ApiRequest
 *
 * @package NeedleProject\FlowMeter\Connector
 * @author Adrian Tilita <adrian@tilita.ro>
 * @copyright 2017 Adrian Tilita
 * @license https://opensource.org/licenses/MIT MIT Licence
 * @todo    Refactor design based on the interface
 */
class ApiRequest implements ConnectorRequestInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $credentials;

    /**
     * @var array
     */
    private $parameters;

    /**
     * ApiRequest constructor.
     *
     * @param string $url
     * @param string $username
     * @param string $password
     * @param array  $parameters
     */
    public function __construct(string $url, string $username, string $password, array $parameters = [])
    {
        $this->path = $url;
        $this->credentials = $username . ':' . $password;
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getCredentials()
    {
        return $this->credentials;
    }
}
