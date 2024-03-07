<?php
declare(strict_types=1);

namespace Xpify\Core\Console;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends \Symfony\Component\Console\Command\Command
{
    const INPUT_ARG = "my_arg";
    const DEFAULT_BAR_CHAR = '<fg=green>⚬</>';
    const DEFAULT_PROGRESS_CHAR = "\xF0\x9F\x8D\xBA";
    const DEFAULT_REDRAW_FREQ = 1;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * BaseCommand constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\State $state
     * @param string|null $name
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\State $state,
        string $name = null
    ) {
        $this->logger = $logger;
        $this->state = $state;
        parent::__construct($name);
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setDefinition($this->getInputList());
        parent::configure();
    }

    /**
     * Init progress
     *
     * @param OutputInterface $output
     * @param string|null $format
     * @return ProgressBar
     */
    protected function getProgress(OutputInterface $output, string $format = null): ProgressBar
    {
        $progressBar = new ProgressBar($output);
        $progressBar->setBarCharacter(static::DEFAULT_BAR_CHAR);
        $progressBar->setProgressCharacter(static::DEFAULT_PROGRESS_CHAR);
        $progressBar->setRedrawFrequency(static::DEFAULT_REDRAW_FREQ);
        if ($format === null) {
            $format = sprintf(
                "Processing Entity: <comment>%%entity_id%%</comment>" .
                "%s%%current%%/%%max%% [%%bar%%] %%percent:3s%%%% - Estimated %%estimated:-6s%%",
                // phpcs:disable Magento2.Functions.DiscouragedFunction
                chr(10)
            );
        }
        $progressBar->setFormat($format);

        return $progressBar;
    }

    /**
     * Get list of options and arguments for the command
     *
     * @return InputArgument[]
     */
    public function getInputList(): array
    {
        return [
            new InputArgument(
                static::INPUT_ARG,
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'Space-separated list of ' . static::INPUT_ARG
            ),
        ];
    }
}
