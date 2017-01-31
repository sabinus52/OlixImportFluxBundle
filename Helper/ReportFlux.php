<?php
/**
 * Classe d'un rapport d'une commande en mode console
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package Olix
 * @subpackage ImportFluxBundle
 */

namespace Olix\ImportFluxBundle\Helper;



class ReportFlux
{

    /**
     * @var \SplFileObject
     */
    private $file;

    /**
     * @var ConsoleLogger
     */
    private $logger;


    /**
     * Constructeur
     *
     * @param string $basename Nom du fichier du rapport
     * @param string $path     Chemin du rapport
     * @param LoggerInterface $logger
     */
    public function __construct($basename, $path, ConsoleLogger $logger)
    {
        $this->logger = $logger;
        $this->createDirectoryIfNotExist($path);
        $this->file = new \SplFileObject($this->getFileName($path, $basename), 'w+');
    }


    /**
     * Ecrit le message dans le fichier et sur le Logger
     *
     * @param string $message
     */
    public function write($message)
    {
        $this->fwrite($message);
        $this->logger->writeln($message);
    }

    /**
     * Ecrit le message d'erreur dans le fichier et sur le Logger
     *
     * @param string $message
     */
    public function error($message)
    {
        $this->fwrite($message);
        $this->logger->alert($message);
    }


    /**
     * Ecrit le message dans le fichier
     *
     * @param string $str
     */
    private function fwrite($str)
    {
        $this->file->fwrite($str."\n");
    }

    /**
     * Retourne le nom complet du fichier
     *
     * @param string $path
     * @param string $basename
     * @return string
     */
    private function getFileName($path, $basename)
    {
        return $path.'/'.$basename.'-'.date('Y-m-d').'.log';
    }


    /**
     * Création du dossier des logs si il n'existe pas
     *
     * @param string $directory
     * @throws \UnexpectedValueException
     */
    private function createDirectoryIfNotExist($directory)
    {
        if (!file_exists($directory)) {
            if (! mkdir($directory, 0755, true))
                throw new \UnexpectedValueException("Impossible de créer le dossier de logs '$directory'");
        }
    }

}
