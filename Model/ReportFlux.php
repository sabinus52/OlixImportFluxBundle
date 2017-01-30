<?php
namespace Olix\ImportFluxBundle\Model;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Psr\Log\LogLevel;

class ReportFlux
{
    protected $output;

    protected $logger;

    protected $file;

    public function __construct(OutputInterface $output)
    {
        $output->getFormatter()->setStyle('debug', new OutputFormatterStyle('cyan'));
        $output->getFormatter()->setStyle('warning', new OutputFormatterStyle('white', 'yellow'));
        $output->getFormatter()->setStyle('info', new OutputFormatterStyle('blue', null, array('bold')));

        $this->output = $output;
        $this->logger = new ConsoleLogger($output, array(), array(
            LogLevel::EMERGENCY => 'error',
            LogLevel::ALERT => 'error',
            LogLevel::CRITICAL => 'error',
            LogLevel::ERROR => 'error',
            LogLevel::WARNING => 'warning',
            LogLevel::NOTICE => 'debug',
            LogLevel::INFO => 'debug',
            LogLevel::DEBUG => 'debug',
        ));
    }

    public function getOutputInterface()
    {
        return $this->output;
    }

    public function printInfo($message, $info)
    {
        $this->output->writeln($message.' : <info>'.$info.'</info>');
    }

    public function writeln($messages)
    {
        $this->output->writeln($messages);
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        $this->logger->emergency($message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert($message, array $context = array())
    {
        $this->logger->alert($message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = array())
    {
        $this->logger->critical($message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = array())
    {
        $this->logger->error($message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = array())
    {
        $this->logger->warning($message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = array())
    {
        $this->logger->notice($message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = array())
    {
        $this->logger->info($message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = array())
    {
        $this->logger->debug($message, $context);
    }

}