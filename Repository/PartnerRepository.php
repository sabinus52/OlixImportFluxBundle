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
use Olix\ImportFluxBundle\Entity\Partner;


class PartnerRepository extends EntityRepository
{

    /**
     * Retourne les partenaires actifs d'une rubrique donnée
     *
     * @param array $rubric : Rubrique dont fait partie les partenaires
     * @param array $list   : Liste des partenaires à retourner
     * @return array
     */
    public function findActiveByRubric(array $rubric, array $list = array())
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.active = :active')
            ->andWhere('p.rubric IN (:rubric)')
            ->setParameter(':active', true)
            ->setParameter(':rubric', $rubric);
        if ($list) $query = $query->andWhere($query->expr()->in('p.code', $list));
        $query = $query->orderBy('p.priority')
            ->getQuery();
        return $query->getResult();
    }


    /**
     * Active le mode "en attente d'execution" pour les partenaires actifs d'une rubrique donnée
     *
     * @param array $rubric : Rubrique dont fait partie les partenaires
     * @param array $list   : Liste des partenaires à retourner
     * @return array
     */
    public function updateStatusWaitForActiveByRubric(array $rubric, array $list = array())
    {
        $query = $this->createQueryBuilder('p')
            ->update()
            ->set('p.doStatus', Partner::STATUS_WAIT)
            ->where('p.active = :active')
            ->andWhere('p.rubric IN (:rubric)')
            ->setParameter(':active', true)
            ->setParameter(':rubric', $rubric);
        if ($list) $query = $query->andWhere($query->expr()->in('p.code', $list));
        return $query->getQuery()->execute();
    }

}
