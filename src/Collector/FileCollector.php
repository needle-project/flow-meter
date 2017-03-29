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
 * @todo    Implement data formatter, implement FileIo library
 */
class FileCollector implements CollectorInterface
{
    private $file;

    /**
     * FileCollector constructor.
     *
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     * @param string $identifier
     * @param array  $data
     */
    public function collectData(string $identifier, array $data)
    {
        file_put_contents(
            $this->file,
            json_encode([
                $identifier => [microtime(true) => $data]
            ]) . "\n",
            FILE_APPEND
        );
    }
}
