<?php
/**
 * Interface de l'import d'un flux
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package Olix
 * @subpackage ImportFluxBundle
 */

namespace Olix\ImportFluxBundle\Model;


interface ImportFluxInterface
{

    /**
     * Parse l'item
     *
     * @param array|\SimpleXMLElement $item
     */
    public function parse($item);

}
