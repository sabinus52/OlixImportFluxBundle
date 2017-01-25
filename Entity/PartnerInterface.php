<?php
/**
 * Interface de l'entité des partenaires
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package Olix
 * @subpackage ImportFluxBundle
 */

namespace Olix\ImportFluxBundle\Entity;


interface PartnerInterface
{

    /**
     * Retourne la liste des rubriques
     *
     * @param string $field : Champ à retourne
     * @return multitype:string
     */
    static public function getRubrics($field = null);

}
