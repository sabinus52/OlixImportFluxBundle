<?php
/**
 * Formulaire d'édition des partenaires
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package Back
 * @subpackage AdminBundle
 */

namespace Olix\ImportFluxBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;



class PartnerFormType extends AbstractType
{

    /**
     * Classe de l'entité du partenaire
     *
     * @var \Olix\ImportFluxBundle\Entity\Partner
     */
    protected $classPartner;


    /**
     * Constructeur
     * 
     * @param \Olix\ImportFluxBundle\Entity\Partner $class
     */
    public function __construct($class)
    {
        $this->classPartner = $class;
    }


    /**
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', 'text', array(
                'label' => 'Code du partenaire',
            ))
            ->add('className', 'text', array(
                'label' => 'Classe objet',
            ))
            ->add('name', 'text', array(
                'label' => 'Nom du partenaire',
            ))
            ->add('rubric', 'choice', array(
                'label' => 'Rubrique',
                'choices' => call_user_func_array($this->classPartner.'::getRubrics', array('label')),
            ))
            ->add('active', 'olix_switch', array(
                'label' => 'Actif ou pas',
                'attr' => array(
                    'data-on-color' => 'success',
                    'data-off-color' => 'danger',
                    'data-on-text' => 'Oui',
                    'data-off-text' => 'Non',
                )
            ))
            ->add('priority', 'integer', array(
                'label' => 'Priorité',
            ))
            ->add('fluxUrl', 'url', array(
                'label' => 'Url du flux',
            ))
            ->add('fluxUrlUser', 'text', array(
                'label' => 'Utilisateur de connexion',
            ))
            ->add('fluxUrlPass', 'text', array(
                'label' => 'Mot de passe de connexion',
            ))
            ->add('fluxCompressed', 'olix_switch', array(
                'label' => 'Compressé ?',
                'attr' => array(
                    'data-on-color' => 'success',
                    'data-off-color' => 'danger',
                    'data-on-text' => 'Oui',
                    'data-off-text' => 'Non',
                )
            ))
            ->add('fluxType', 'choice', array(
                'label' => 'Type du flux à télécharger',
                'choices' => array('CSV' => 'CSV', 'XML' => 'XML'),
            ))
        ;
    }


    /**
     * @see \Symfony\Component\Form\AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->classPartner,
        ));
    }


    /**
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'olix_importfluxbundle_partner';
    }

}
