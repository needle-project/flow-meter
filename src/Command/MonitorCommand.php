<?php
namespace NeedleProject\FlowMeter\Command;

use NeedleProject\FileIo\File;
use NeedleProject\FileIo\Helper\PathHelper;
use NeedleProject\FlowMeter\Builder\FlowTestBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MonitorCommand extends Command
{
    const OPTION_CONFIG_FILE = 'config_file';
    const OPTION_INPUT_FILE = 'input_data';

    /**
     * {@parentDoc}
     */
    protected function configure()
    {
        $this->setName('meter:start')
            ->addOption(
                static::OPTION_CONFIG_FILE,
                "c",
                InputOption::VALUE_REQUIRED,
                "Path to the config file (yaml) - relative to execution path"
            )
            ->addOption(
                static::OPTION_INPUT_FILE,
                "i",
                InputOption::VALUE_REQUIRED,
                "Path to the input file (json array) - relative to execution path"
            )
            ->setDescription('Start metering the flow');
    }

    /**
     * Execute the battle of the year
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list (
            $configData,
            $inputData
        ) = $this->getInputData($input);

        $testBuilder = new FlowTestBuilder($input, $output);
        $flowTest = $testBuilder->buildFlowTest($configData, $inputData);
        unset($configData, $inputData);
        $flowTest->start();
        $flowTest->showResult();
        return null;
    }

    /**
     * Get input data - config and test data
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return array
     */
    private function getInputData(InputInterface $input): array
    {
        $inputDataFile = $input->getOption(static::OPTION_INPUT_FILE);
        $inputConfigFile = $input->getOption(static::OPTION_CONFIG_FILE);

        $pathHelper = new PathHelper();
        $configData = $this->getFileData(
            $pathHelper->normalizePathSeparator(getcwd() . DIRECTORY_SEPARATOR . $inputConfigFile)
        );
        $inputData = $this->getFileData(
            $pathHelper->normalizePathSeparator(getcwd() . DIRECTORY_SEPARATOR . $inputDataFile)
        );
        return [$configData, $inputData];
    }

    /**
     * @param string $filename
     * @return array
     */
    public function getFileData(string $filename): array
    {
        $filename = new File($filename);
        return $filename->getContent()->getArray();
    }
}
