<?php
/**
 * Controlleur de la gestion des partenaires
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package Olix
 * @subpackage ImportFluxBundle
 */

namespace Olix\ImportFluxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Olix\ImportFluxBundle\Form\PartnerFormType;



class PartnerController extends Controller
{

    /**
     * Retourne la classe des partenaires
     * @return string
     */
    protected function getClassPartner()
    {
        return '\\'.$this->container->getParameter('olix_import_flux.partner_class');
    }


    /**
     * Page de listing des partenaires
     */
    public function indexAction()
    {
        $datatable = $this->get('admin_datatables.partner');
        $datatable->setClassPartner($this->getClassPartner());
        $datatable->buildDatatable();

        return $this->container->get('olix.admin')->render('OlixImportFluxBundle:Partner:index.html.twig', 'importflux_partner', array(
            'datatable'     => $datatable,
            'rubric'        => call_user_func(array($this->getClassPartner(), 'getRubrics')),
            'states'        => call_user_func(array($this->getClassPartner(), 'getStates')),
        ));
    }


    /**
     * Retourne les partenaires en mode AJAX
     *
     * @return \Symfony\Component\HttpFoundation\Response : JSON
     */
    public function getResultsAction()
    {
        $datatable = $this->get('admin_datatables.partner');
        $datatable->setClassPartner($this->getClassPartner());
        $datatable->buildDatatable();

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);

        return $query->getResponse();
    }


    /**
     * Page du formulaire de saisie d'un nouveau partenaire
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Création du formulaire
        $classConfig = $this->getClassPartner();
        $partner = new $classConfig();
        $form = $this->createForm(new PartnerFormType($this->getClassPartner()), $partner);

        // Validation du formulaire
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($partner);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'Le partenaire <strong>'.$partner->getName().'</strong> a été ajoutée avec succès');
                return $this->redirect($this->generateUrl('olix_importflux_partner_list'));
            }
            $form->addError(new FormError('Tous les champs ne sont pas complètement remplis'));
        }

        // Affichage du formulaire
        return $this->container->get('olix.admin')->render('OlixImportFluxBundle:Partner:edit.html.twig', 'importflux_partner', array(
            'form' => $form->createView(),
        ));
    }


    /**
     * Page du formulaire de modification d'un partenaire
     *
     * @param integer $id : ID du partenaire
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $partner = $em->getRepository($this->getClassPartner())->find($id);

        // Création du formulaire
        $form = $this->createForm(new PartnerFormType($this->getClassPartner()), $partner);

        // Validation du formulaire
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($partner);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'Le partenaire <strong>'.$partner->getName().'</strong> a été modifiée avec succès');
                return $this->redirect($this->generateUrl('olix_importflux_partner_list'));
            }
            $form->addError(new FormError('Tous les champs ne sont pas complètement remplis'));
        }

        // Affichage du formulaire
        return $this->container->get('olix.admin')->render('OlixImportFluxBundle:Partner:edit.html.twig', 'importflux_partner', array(
            'form' => $form->createView(),
        ));
    }

}
