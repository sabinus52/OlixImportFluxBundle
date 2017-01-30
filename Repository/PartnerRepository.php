<?php
/**
 * Repository des partenaires
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package Olix
 * @subpackage ImportFluxBundle
 */

namespace Olix\ImportFluxBundle\Repository;

use Doctrine\ORM\EntityRepository;


class PartnerRepository extends EntityRepository
{

    /**
     * Retourne les partenaires actifs d'une rubrique donnée
     *
     * @param integer $rubric : Rubrique dont fait partie les partenaires
     * @param array   $list   : Liste des partenaires à retourner
     * @return array
     */
    public function findActiveByRubric($rubric, array $list = array())
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.active = :active')
            ->andWhere('p.rubric = :rubric')
            ->setParameter(':active', true)
            ->setParameter(':rubric', $rubric);
        if ($list) $query = $query->andWhere($query->expr()->in('p.code', $list));
        $query = $query->orderBy('p.priority')
            ->getQuery();
        return $query->getResult();
    }

}
