<?php
/**
 * Classe des logs de la console
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package Olix
 * @subpackage ImportFluxBundle
 */

namespace Olix\ImportFluxBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Psr\Log\LogLevel;



class ConsoleLogger
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ConsoleLogger
     */
    protected $logger;

    /**
     * @var OutputInterface
     */
    protected $output;


    /**
     * Constructeur
     *
     * @param OutputInterface $output
     */
    public function __construct(ContainerInterface $container, OutputInterface $output)
    {
        $this->output = $output;
        $this->container = $container;

        $this->setStyleFormatter();
        $this->logger = new \Symfony\Component\Console\Logger\ConsoleLogger($output, array(), array(
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


    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutputInterface()
    {
        return $this->output;
    }


    /**
     * Affecte les différents styles
     */
    protected function setStyleFormatter()
    {
        $this->output->getFormatter()->setStyle('debug', new OutputFormatterStyle('cyan'));
        $this->output->getFormatter()->setStyle('warning', new OutputFormatterStyle('white', 'yellow'));

        $this->output->getFormatter()->setStyle('info', new OutputFormatterStyle('blue', null, array('bold')));
        $this->output->getFormatter()->setStyle('success', new OutputFormatterStyle('green', null, array('bold')));
        $this->output->getFormatter()->setStyle('danger', new OutputFormatterStyle('red', null, array('bold')));
    }


    /**
     * Envoi d'un mail en cas d'erreur
     *
     * @param string $message
     */
    public function sendMailError($message)
    {
        $mail = \Swift_Message::newInstance()
            ->setSubject('Une erreur est survenue lors de l\'import du partenaire')
            ->setFrom($this->container->getParameter('mailer_sendfrom'))
            ->setTo($this->container->getParameter('mailer_senderror'))
            ->setBody($message);

        $this->container->get('mailer')->send($mail);
    }


    /**
     * Affiche un message quelconque
     *
     * @param string $message
     */
    public function writeln($message)
    {
        $this->output->writeln($message);
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
        $this->sendMailError($message);
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
