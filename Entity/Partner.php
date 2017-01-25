<?php
/**
 * Entité des partenaires
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package Olix
 * @subpackage ImportFluxBundle
 */

namespace Olix\ImportFluxBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Partner
 *
 * @ORM\MappedSuperclass
 * @UniqueEntity(fields="code", message="Ce code est déjà utilisé, merci d'en choisir un autre")
 */
abstract class Partner implements PartnerInterface
{

    /**
     * Constantes des différents types de fluxs
     */
    const FLUX_TYPE_XML = 'XML';
    const FLUX_TYPE_CSV = 'CSV';


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Code unique pour identifier le partenaire
     *
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=20, unique=true)
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^[a-z][a-z\-0-9]*$/", message="Le code doit comporter que des lettres ou chiffres en minuscules")
     * @Assert\Length(min=2,max=20)
     */
    private $code;

    /**
     * Classe objet du partenaire
     *
     * @var string
     *
     * @ORM\Column(name="classname", type="string", length=20, unique=true, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^[A-Z][A-Za-z\-0-9]*$/", message="La classe doit comporter que des lettres ou chiffres")
     * @Assert\Length(min=2,max=20)
     */
    private $className;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="rubric", type="smallint")
     */
    private $rubric;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="smallint")
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     */
    private $priority;

    /**
     * @var string
     *
     * @ORM\Column(name="flux_url", type="string", length=1000, nullable=true)
     * @Assert\Url()
     */
    private $fluxUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="flux_user", type="string", length=20, nullable=true)
     */
    private $fluxUrlUser;

    /**
     * @var string
     *
     * @ORM\Column(name="flux_pass", type="string", length=20, nullable=true)
     */
    private $fluxUrlPass;

    /**
     * @var bool
     *
     * @ORM\Column(name="flux_compressed", type="boolean")
     */
    private $fluxCompressed;

    /**
     * @var string
     *
     * @ORM\Column(name="flux_type", type="string", length=3)
     */
    private $fluxType;



    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set code
     *
     * @param string $code
     *
     * @return Partner
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }


    /**
     * Set className
     *
     * @param string $className
     *
     * @return Partner
     */
    public function setClassName($className)
    {
        $this->className = $className;
        return $this;
    }

    /**
     * Get className
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return Partner
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Set rubric
     *
     * @param integer $rubric
     *
     * @return Partner
     */
    public function setRubric($rubric)
    {
        $this->rubric = $rubric;
        return $this;
    }

    /**
     * Get rubric
     *
     * @return int
     */
    public function getRubric()
    {
        return $this->rubric;
    }


    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Partner
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }


    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return Partner
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set fluxUrl
     *
     * @param string $fluxUrl
     *
     * @return Partner
     */
    public function setFluxUrl($fluxUrl)
    {
        $this->fluxUrl = $fluxUrl;
        return $this;
    }

    /**
     * Get fluxUrl
     *
     * @return string
     */
    public function getFluxUrl()
    {
        return $this->fluxUrl;
    }

    /**
     * Set fluxUrlUser
     *
     * @param string $fluxUrlUser
     *
     * @return Partner
     */
    public function setFluxUrlUser($fluxUrlUser)
    {
        $this->fluxUrlUser = $fluxUrlUser;
        return $this;
    }

    /**
     * Get fluxUrlUser
     *
     * @return string
     */
    public function getFluxUrlUser()
    {
        return $this->fluxUrlUser;
    }

    /**
     * Set fluxUrlPass
     *
     * @param string $fluxUrlPass
     *
     * @return Partner
     */
    public function setFluxUrlPass($fluxUrlPass)
    {
        $this->fluxUrlPass = $fluxUrlPass;
        return $this;
    }

    /**
     * Get fluxUrlPass
     *
     * @return string
     */
    public function getFluxUrlPass()
    {
        return $this->fluxUrlPass;
    }

    /**
     * Set fluxCompressed
     *
     * @param boolean $fluxCompressed
     *
     * @return Partner
     */
    public function setFluxCompressed($fluxCompressed)
    {
        $this->fluxCompressed = $fluxCompressed;
        return $this;
    }

    /**
     * Get fluxCompressed
     *
     * @return bool
     */
    public function getFluxCompressed()
    {
        return $this->fluxCompressed;
    }

    /**
     * Set fluxType
     *
     * @param string $fluxType
     *
     * @return Partner
     */
    public function setFluxType($fluxType)
    {
        $this->fluxType = $fluxType;
        return $this;
    }

    /**
     * Get fluxType
     *
     * @return string
     */
    public function getFluxType()
    {
        return $this->fluxType;
    }

}
