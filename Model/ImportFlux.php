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
use Olix\ImportFluxBundle\Tools\DownloadFlux;
use Olix\ImportFluxBundle\Entity\Partner;


abstract class ImportFlux
{

    /**
     * @var ReportFlux
     */
    protected $report;

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
     * @param EntityManager   $em
     * @param ReportFlux      $report
     * @param Partner         $partner
     */
    public function __construct(EntityManager $em, ReportFlux $report, $partner)
    {
        $this->em = $em;
        $this->report = $report;
        $this->partner = $partner;

        $this->isDryrun = false;
        $this->isError = false;

        // Initialise les compteurs
        $this->countInFile = $this->countFound = $this->countImported = 0;
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
            ->setOutputInterface($this->report->getOutputInterface());
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
     * Persist l'entité
     *
     * @param object $entity
     */
    public function persist($entity)
    {
        if ( ! $this->isDryrun ) $this->em->persist($entity);
    }



    ### CHARGEMENT DU FLUX #########################################################################


    /**
     * Chargement du flux
     *
     * @throws \Exception
     */
    public function loadFile()
    {
        if (!is_readable($this->getFileNameFlux())) {
            $this->report->alert('Le fichier '.$this->getFileNameFlux().' est absent');
            return false;
        }
        switch ($this->partner->getFluxType()) {
            case Partner::FLUX_TYPE_XML:
                return $this->loadFileXML();
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
     * @return \SimpleXMLElement
     */
    protected function loadFileXML()
    {
        $xml = simplexml_load_file($this->getFileNameFlux());
        $this->report->notice('Nom du fichier XML téléchargé = '.$this->filenameFlux);
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
        $this->report->notice('Nom du fichier CSV téléchargé = '.$this->filenameFlux);
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
            $this->report->notice('Nom du fichier téléchargé = '.$this->filenameFlux);
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
