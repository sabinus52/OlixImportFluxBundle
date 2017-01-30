<?php
/**
 * Classe pour le téléchargement des fluxs
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package Olix
 * @subpackage ImportFluxBundle
 */
namespace Olix\ImportFluxBundle\Tools;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;



class DownloadFlux
{

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var \Symfony\Component\Console\Helper\ProgressBar
     */
    protected $progress;

    /**
     * Url source de téléchargement du flux
     *
     * @var string
     */
    protected $urlSource;

    /**
     * Chemin complet et nom du fichier cible téléchargé
     *
     * @var string
     */
    protected $fileTarget;

    /**
     * Si le flux est compressé ou pas
     *
     * @var boolean
     */
    protected $compressed;

    /**
     * Utilisateur pour l'authentification
     *
     * @var string
     */
    protected $authUser;

    /**
     * Mot de passe pour l'authentification
     *
     * @var string
     */
    protected $authPass;



    /**
     * Constructeur
     *
     * @param string $url    : Url du lien de téléchargement
     * @param string $target : Fichier cible téléchargé
     */
    public function __construct($url, $target)
    {
        $this->urlSource = $url;
        $this->fileTarget = $target;
        $this->compressed = false;
    }


    /**
     * Affecte la sortie écran
     *
     * @param OutputInterface $output
     * @return DownloadFlux
     */
    public function setOutputInterface(OutputInterface $output)
    {
        $this->output = $output;
        $this->progress = new ProgressBar($this->output);
        $this->progress->setBarWidth(50);
        $this->progress->setFormat('debug');
        return $this;
    }


    /**
     * Affecte si le flux est compréssé
     *
     * @param boolean $compressed
     * @return DownloadFlux
     */
    public function setCompressed($compressed)
    {
        $this->compressed = $compressed;
        return $this;
    }


    /**
     * Affecte les paramètres d'authentification
     *
     * @param string $user : Utilisateur
     * @param string $pass : Mot de passe
     * @return DownloadFlux
     */
    public function setUserPassword($user, $pass)
    {
        $this->authUser = $user;
        $this->authPass = $pass;
        return $this;
    }


    /**
     * Execute le téléchargement
     */
    public function execute()
    {
        if ($this->compressed) {

            $this->download($this->fileTarget.'.gz');
            $this->printOut('Telechargement du flux : <info>OK</info>');

            $this->printOut('Decompression du flux  : ', false);
            $this->unCompressZlib($this->fileTarget);
            $this->printOut('<info>OK</info>');

        } else {

            $this->download($this->fileTarget);
            $this->printOut('Telechargement du flux : <info>OK</info>');

        }
    }


    /**
     * Telecharge le flux
     *
     * @param string $fileName : Nom du fichier téléchargé
     * @throws \UnexpectedValueException
     */
    protected function download($fileName)
    {
        // Ouverture du fichier cible décompressé en écriture
        $targetFile = fopen($fileName, 'wb');
        if (! $targetFile) {
            throw new \UnexpectedValueException("Impossible d'ouvrir le fichier cible '$fileName'");
        }

        // Initialisation du téléchargement
        $handleCurl = curl_init();
        curl_setopt($handleCurl, CURLOPT_URL, $this->urlSource);
        if ($this->authPass) {
            curl_setopt($handleCurl, CURLOPT_USERPWD, $this->authUser.':'.$this->authPass);
            curl_setopt($handleCurl, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        }
        curl_setopt($handleCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($handleCurl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($handleCurl, CURLOPT_RETURNTRANSFER, false);
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) curl_setopt($handleCurl, CURLOPT_NOPROGRESS, false);
        curl_setopt( $handleCurl, CURLOPT_FILE, $targetFile);

        // Téléchargement
        $res = curl_exec($handleCurl);
        if ($res === false) {
            curl_close($handleCurl);
            fclose($targetFile);
            throw new \UnexpectedValueException("Impossible de télécharger le flux '{$this->urlSource}' vers '$fileName'");
        }

        // Fermeture des handles
        curl_close($handleCurl);
        fclose($targetFile);
    }


    /**
     * Décompresse au fichier au format GZ
     *
     * @param string $fileName : Nom du fichier
     * @throws \UnexpectedValueException
     */
    protected function unCompressZlib($fileName)
    {
        // Ouverture du fichier compressé en lecture
        $compressedFile = gzopen($fileName.'.gz', 'rb');
        if (! $compressedFile) {
            throw new \UnexpectedValueException("Impossible d'ouvrir le fichier '$fileName.gz' au format gzip");
        }

        // Ouverture du fichier cible décompressé en écriture
        $targetFile = fopen($fileName, 'wb');
        if (! $targetFile) {
            gzclose($compressedFile);
            throw new \UnexpectedValueException("Impossible d'ouvrir le fichier cible '$fileName' décompressé");
        }

        // Décompression
        while (!gzeof($compressedFile)) {
            $res = fwrite($targetFile, gzread($compressedFile, 4096));
            if (! $res) {
                gzclose($compressedFile);
                fclose($targetFile);
                throw new \UnexpectedValueException("Impossible d'écrire dans le fichier cible '$fileName' décompressé");
            }
        }

        // Fermeture des fichiers
        gzclose($compressedFile);
        fclose($targetFile);
        if (! unlink($fileName.'.gz'))
            throw new \UnexpectedValueException("Impossible de supprimer le fichier compressé '$fileName'");
    }


    /**
     * Affiche sur la sortie écran un message
     *
     * @param string  $message   : Message à afficher
     * @param boolean $isNewLine : Si nouvelle ligne après
     */
    protected function printOut($message, $isNewLine = true)
    {
        if ( !$this->output ) return;
        if ($isNewLine)
            $this->output->writeln($message);
        else
            $this->output->write($message);
    }

}
