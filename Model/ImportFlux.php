<?php
/**
 * Classe abstraite d'import d'un flux
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package Olix
 * @subpackage ImportFluxBundle
 */

namespace Olix\ImportFluxBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Olix\ImportFluxBundle\Tools\DownloadFlux;
use Olix\ImportFluxBundle\Entity\Partner;
use Olix\ImportFluxBundle\Helper\ReportFlux;
use Olix\ImportFluxBundle\Helper\ConsoleLogger;



abstract class ImportFlux
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var ReportFlux
     */
    protected $report;

    /**
     * @var ConsoleLogger
     */
    protected $logger;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Objet du partenaire
     *
     * @var \Olix\ImportFluxBundle\Entity\Partner
     */
    protected $partner;

    /**
     * Emplacement racine des fluxs
     *
     * @var string
     */
    protected $rootPathFlux;

    /**
     * Chemin complet du fichier du flux
     *
     * @var string
     */
    protected $filenameFlux;

    /**
     * Nombre d'item total contenue dans le fichier
     * Nombre d'item trouvés
     * Nombre d'item réellement importés
     *
     * @var integer
     */
    protected $countInFile;
    protected $countFound;
    protected $countImported;

    /**
     * Mode DRY-RUN
     * @var boolean
     */
    protected $isDryrun;

    /**
     * Si une erreur est rencontrée
     * @var boolean
     */
    protected $isError;



    /**
     * Constructeur
     *
     * @param Container       $container
     * @param ConsoleLogger   $logger
     * @param Partner         $partner
     */
    public function __construct(Container $container, ConsoleLogger $logger, $partner)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->logger = $logger;
        $this->partner = $partner;

        $this->isDryrun = false;
        $this->isError = true;

        // Initialise les compteurs
        $this->countInFile = $this->countFound = $this->countImported = 0;

        $this->beginExecution();
    }


    /**
     * Desctructeur
     */
    public function __destruct()
    {
        $this->endExecution();
    }


    /**
     * Début d'execution de l'import
     */
    protected function beginExecution()
    {
        // Marque le flux comme commencé
        $this->partner->setDoStatus(Partner::STATUS_ERROR);
        $this->partner->setDoExecutedAt(new \DateTime());
        $this->em->flush($this->partner);

        // Nom du fichier du rapport à créer TODO revoir si on peut paramétrer l'emplacement des logs
        $this->report = new ReportFlux($this->partner->getCode(), $this->container->getParameter('kernel.logs_dir').'/importflux', $this->logger);
        $this->report->write('Import du partenaire <info>'.$this->partner->getName().'</info>');
        $this->report->write('---------------------------------------------------------------------');
    }


    /**
     * Fin d'execution de l'import
     */
    protected function endExecution()
    {
        if ( ! $this->isError ) $this->partner->setDoStatus(Partner::STATUS_OK);
        $this->partner->setDoFinishedAt(new \DateTime());
        $this->em->flush($this->partner);
    }


    /**
     * Téléchargement du flux
     *
     * @return bool
     */
    public function download()
    {
        $dwl = new DownloadFlux($this->partner->getFluxUrl(), $this->getFileNameFlux());
        $dwl->setCompressed($this->partner->getFluxCompressed())
            ->setUserPassword($this->partner->getFluxUrlUser(), $this->partner->getFluxUrlPass())
            ->setOutputInterface($this->logger->getOutputInterface());
        return $dwl->execute();
    }


    /**
     * Execute l'import du flux
     *
     * @return bool
     * @throws \Exception
     */
    public function execute()
    {
        throw new \Exception('OlixImportFluxBundle::ImportFlux::execute() : Cette fonction doit être implémentée dans la classe héritée.');
    }


    /**
     * Active le mode DRYRUN ou pas
     *
     * @param boolean $value
     */
    public function setDryrun($value = true)
    {
        $this->isDryrun = ($value) ?  true : false;
        return $this;
    }


    /**
     * Affecte une erreur
     *
     * @param string $error
     * @return \Olix\ImportFluxBundle\Model\ImportFlux
     */
    public function setError($error = true)
    {
        $this->isError = $error;
        return $this;
    }


    /**
     * Retourne si erreur ou pas
     *
     * @return boolean
     */
    public function isError()
    {
        return $this->isError;
    }


    /**
     * Persist l'entité
     *
     * @param object $entity
     */
    protected function persist($entity)
    {
        if ( ! $this->isDryrun ) $this->em->persist($entity);
    }



    ### CHARGEMENT DU FLUX #########################################################################


    /**
     * Chargement du flux
     *
     * @param string $method : Methode d'ouverture du fichier
     * @throws \Exception
     */
    public function loadFile($method = 'SimpleXMLElement')
    {
        if (!is_readable($this->getFileNameFlux())) {
            $this->report->error('Le fichier '.$this->getFileNameFlux().' est absent');
            $this->setError();
            return false;
        }
        switch ($this->partner->getFluxType()) {
            case Partner::FLUX_TYPE_XML:
                return $this->loadFileXML($method);
                break;
            case Partner::FLUX_TYPE_CSV:
                return $this->loadFileCSV();
                break;
            default:
                throw new \Exception('Le type du fichier "'.$this->type.'" est inconnu : attendu (csv, xml)');
                break;
        }
    }


    /**
     * Chargement d'un flux au format XML
     *
     * @param string $method : Methode d'ouverture du fichier
     * @return \SimpleXMLElement|XMLReader
     */
    protected function loadFileXML($method)
    {
        switch ($method) {
            case 'SimpleXMLElement' :
                $xml = new \SimpleXMLElement($this->getFileNameFlux(), 0, true);
                $result = ($xml) ? true : false;
                break;
            case 'XMLReader' :
                $xml = new \XMLReader();
                $result = $xml->open($this->getFileNameFlux());
                break;
            default :
                throw new \Exception('La méthode "'.$$method.'" d\'ouverture XML est inconnu : attendu (SimpleXMLElement, XMLReader)');
        }
        $this->logger->notice('Nom du fichier XML téléchargé = '.$this->filenameFlux);

        // Retour si erreur
        if (!$result) {
            $this->report->error('Impossible de lire le fichier '.$this->getFileNameFlux().'');
            $this->setError();
            return false;
        }
        return $xml;
    }


    /**
     * Chargement d'un flux au format CSV
     *
     * @return \SplFileObject
     */
    protected function loadFileCSV()
    {
        $csv = new \SplFileObject($this->getFileNameFlux());
        $csv->setFlags(\SplFileObject::READ_CSV);
        $this->logger->notice('Nom du fichier CSV téléchargé = '.$this->filenameFlux);
        return $csv;
    }



    ### GESTION DU CHEMIN DU FLUX ##################################################################


    /**
     * Affecte le chemin de l'emplacement du flux
     *
     * @param string $path
     * @return ImportFlux
     */
    public function setRootPathFlux($path)
    {
        $this->rootPathFlux = $path;
        return $this;
    }


    /**
     * Retourne le chemin de l'empacement du flux
     *
     * @return string
     */
    public function getRootPathFlux()
    {
        return ($this->rootPathFlux) ? $this->rootPathFlux : sys_get_temp_dir();
    }


    /**
     * Retourne le chemin complet + nom du fichier flux et le génère au besoin
     *
     * @return string
     */
    public function getFileNameFlux()
    {
        if (! $this->filenameFlux) {
            $this->filenameFlux = $this->generateFileNameFlux();
            $this->logger->notice('Nom du fichier téléchargé = '.$this->filenameFlux);
        }
        return $this->filenameFlux;
    }


    /**
     * Genère le chemin complet du fichier qui sera la cible du téléchargement et le fichier à importer
     *
     * @return string
     * @throws \UnexpectedValueException
     */
    protected function generateFileNameFlux()
    {
        // Si dossier existant
        $destination = realpath($this->getRootPathFlux());
        if (! $destination)
            throw new \UnexpectedValueException("Le dossier '{$this->destination} est inéxistant");

        $destination.= '/'.date('Y-m-d');
        // Création du dossier inexistant
        if (!file_exists($destination)) {
            if(! mkdir($destination, 0755, true))
                throw new \UnexpectedValueException("Impossible de créer le dossier '$destination'");
        }

        return $destination.'/'.strtolower($this->partner->getCode().'.'.$this->partner->getFluxType());
    }

}
