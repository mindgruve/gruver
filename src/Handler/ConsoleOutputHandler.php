<?php

namespace Mindgruve\Gruver\Handler;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutputHandler implements HandlerInterface
{
    /**
     * @var FormatterInterface
     */
    protected $formatter;

    /**
     * @var array
     */
    protected $processors;

    /**
     * @var OutputInterface
     */
    protected $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
        $this->processors = array();
    }

    /**
     * Gets the formatter.
     *
     * @return FormatterInterface
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * Handles a record.
     *
     * All records may be passed to this method, and the handler should discard
     * those that it does not want to handle.
     *
     * The return value of this function controls the bubbling process of the handler stack.
     * Unless the bubbling is interrupted (by returning true), the Logger class will keep on
     * calling further handlers in the stack with a given log record.
     *
     * @param  array $record The record to handle
     * @return Boolean true means that this handler handled the record, and that bubbling is not permitted.
     *                        false means the record was either not processed or that this handler allows bubbling.
     */
    public function handle(array $record)
    {
        if ((int)$record['level'] >= Logger::ERROR) {
            $this->output->writeln('<error>' . (string)$record['message'] . '</error>');

            return false;
        } elseif ((int)$record['level'] >= Logger::INFO) {
            $this->output->writeln('<info>' . (string)$record['message'] . '</info>');

            return false;
        }

        $this->output->writeln((string)$record['message']);

        return false;
    }

    /**
     * Handles a set of records at once.
     *
     * @param array $records The records to handle (an array of record arrays)
     */
    public function handleBatch(array $records)
    {
        foreach ($records as $record) {
            $this->handle($record);
        }
    }

    /**
     * Checks whether the given record will be handled by this handler.
     *
     * This is mostly done for performance reasons, to avoid calling processors for nothing.
     *
     * Handlers should still check the record levels within handle(), returning false in isHandling()
     * is no guarantee that handle() will not be called, and isHandling() might not be called
     * for a given record.
     *
     * @param array $record Partial log record containing only a level key
     *
     * @return Boolean
     */
    public function isHandling(array $record)
    {
        return true;
    }

    /**
     * Removes the processor on top of the stack and returns it.
     *
     * @return callable
     */
    public function popProcessor()
    {
        return array_pop($this->processors);
    }

    /**
     * Adds a processor in the stack.
     *
     * @param  callable $callback
     * @return self
     */
    public function pushProcessor($callback)
    {
        $this->processors[] = $callback;

        return $this;
    }

    /**
     * Sets the formatter.
     *
     * @param  FormatterInterface $formatter
     * @return self
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }
}