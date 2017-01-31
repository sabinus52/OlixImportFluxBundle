<?php
/**
 * Listener au moment de déclenchement d'une exception
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package Olix
 * @subpackage ImportFluxBundle
 */

namespace Olix\ImportFluxBundle\Listener;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;



class ConsoleExceptionListener
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * Constructeur
     *
     * @param ContainerInterface $container
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }


    /**
     * Déclenchement de l'exception
     *
     * @param ConsoleExceptionEvent $event
     */
    public function onConsoleException(ConsoleExceptionEvent $event)
    {
        $command = $event->getCommand();
        $exception = $event->getException();

        $message = sprintf(
            '%s: %s (uncaught exception) at %s line %s while running console command `%s`',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $command->getName()
        );

        $this->logger->error($message, array('exception' => $exception));
        //$this->sendMailError('Une exception est survenue lors de la commande : '.$command->getName(), $message."\n\n".$exception->getTraceAsString());
    }


    /**
     * Envoi d'un mail au moment d'une erreur
     *
     * @param unknown $subjet
     * @param unknown $message
     */
    private function sendMailError($subjet, $message)
    {
        $mail = \Swift_Message::newInstance()
            ->setSubject($subjet)
            ->setFrom($this->container->getParameter('mailer_sendfrom'))
            ->setTo($this->container->getParameter('mailer_senderror'))
            ->setBody($message, 'text/plain')
        ;
        $this->container->get('mailer')->send($mail);
    }

}