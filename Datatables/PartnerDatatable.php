<?php
/**
 * Datatables de la gestion des partenaires
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package Olix
 * @subpackage ImportFluxBundle
 */

namespace Olix\ImportFluxBundle\Datatables;

use Olix\DatatablesBootstrapBundle\Datatable\View\AbstractDatatableView;



/**
 * Class PartnerDatatable
 *
 * @package Back\ImportFluxBundle\Datatables
 */
class PartnerDatatable extends AbstractDatatableView
{

    /**
     * Classe de l'entité du partenaire
     *
     * @var \Olix\ImportFluxBundle\Entity\Partner
     */
    protected $classPartner;


    /**
     * Affecte la classe du partenaire
     *
     * @param \Olix\ImportFluxBundle\Entity\Partner $class
     */
    public function setClassPartner($class)
    {
        $this->classPartner = $class;
    }


    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {
        $this->ajax->set(array(
            'url' => $this->router->generate('olix_importflux_partner_results'),
        ));

        $this->columnBuilder
            ->add('id', 'column', array(
                'title' => '#',
                'searchable' => false,
            ))
            ->add('doStatus', 'column', array(
                'title' => 'Statut',
                'width' => '50px',
                'searchable' => false,
                'class' => 'text-center',
                'filter' => array('text', array(
                    'class' => 'form-control',
                )),
                'render' => 'render_column_state',
            ))
            ->add('doExecutedAt', 'datetime', array(
                'title' => 'Execution',
                'searchable' => false,
                'date_format' => 'L LT',
                'width' => '100px',
            ))
            ->add('priority', 'column', array(
                'title' => 'Priorité',
                'width' => '50px',
                'filter' => array('text', array(
                    'class' => 'form-control',
                )),
                'render' => 'render_column_priority',
            ))
            ->add('code', 'column', array(
                'title' => 'Code',
                'filter' => array('text', array(
                    'class' => 'form-control',
                )),
            ))
            ->add('name', 'column', array(
                'title' => 'Nom',
                'filter' => array('text', array(
                    'class' => 'form-control',
                )),
            ))
            ->add('rubric', 'column', array(
                'title' => 'Rubrique',
                'class' => 'text-center',
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => array('' => 'Tous') + call_user_func_array($this->classPartner.'::getRubrics', array('label')),
                    'class' => 'form-control',
                )),
                'render' => 'render_column_rubric',
            ))
            ->add('active', 'boolean', array(
                'title' => 'Actif',
                'class' => 'text-center',
                'true_label' => '',
                'false_label' => ' ',
                'true_icon' => 'fa fa-check-circle fa-lg text-success',
                'false_icon' => 'fa fa-times-circle fa-lg text-danger',
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => array('' => 'Tous', 'true' => 'Oui', 'false' => 'Non'),
                    'class' => 'form-control'
                )),
            ))

            ->add(null, 'action', array(
                'title' => '',
                'start_html' => '<div class="olix-actions">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'olix_importflux_partner_edit',
                        'route_parameters' => array('id' => 'id'),
                        'icon' => 'fa fa-edit fa-fw',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'Modifier ce partenaire',
                            'class' => 'btn btn-primary btn-xs btn-update',
                            'role' => 'button',
                        ),
                    )
                )
            ))
        ;
    }


    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return $this->classPartner;
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'olix_importflux_partner_datatable';
    }

}
