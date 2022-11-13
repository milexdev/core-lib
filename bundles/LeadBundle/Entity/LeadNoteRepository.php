<?php

namespace Milex\LeadBundle\Entity;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Milex\CoreBundle\Entity\CommonRepository;

/**
 * LeadNoteRepository.
 */
class LeadNoteRepository extends CommonRepository
{
    /**
     * {@inhertidoc}.
     *
     * @return Paginator
     */
    public function getEntities(array $args = [])
    {
        $q = $this
            ->createQueryBuilder('n')
            ->select('n');
        $args['qb'] = $q;

        return parent::getEntities($args);
    }

    /**
     * @param $leadId
     * @param $filter
     * @param $noteTypes
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNoteCount($leadId, $filter = null, $noteTypes = null)
    {
        $q = $this
            ->createQueryBuilder('n');
        $q->select('count(n.id) as note_count')
            ->where($q->expr()->eq('IDENTITY(n.lead)', ':lead'))
            ->setParameter('lead', $leadId);

        if (null != $filter) {
            $q->andWhere(
                $q->expr()->like('n.text', ':filter')
            )->setParameter('filter', '%'.$filter.'%');
        }

        if (null != $noteTypes) {
            $q->andWhere(
                $q->expr()->in('n.type', ':noteTypes')
            )->setParameter('noteTypes', $noteTypes);
        }

        $results = $q->getQuery()->getArrayResult();

        return $results[0]['note_count'];
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getTableAlias()
    {
        return 'n';
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder|\Doctrine\DBAL\Query\QueryBuilder $q
     * @param                                                              $filter
     *
     * @return array
     */
    protected function addCatchAllWhereClause($q, $filter)
    {
        return $this->addStandardCatchAllWhereClause(
            $q,
            $filter,
            [
                'n.text',
            ]
        );
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder|\Doctrine\DBAL\Query\QueryBuilder $q
     * @param                                                              $filter
     *
     * @return array
     */
    protected function addSearchCommandWhereClause($q, $filter)
    {
        $command                 = $filter->command;
        $string                  = $filter->string;
        $unique                  = $this->generateRandomParameterName();
        $returnParameter         = false; //returning a parameter that is not used will lead to a Doctrine error
        list($expr, $parameters) = parent::addSearchCommandWhereClause($q, $filter);

        switch ($command) {
            case $this->translator->trans('milex.lead.note.searchcommand.type'):
            case $this->translator->trans('milex.lead.note.searchcommand.type', [], null, 'en_US'):
                switch ($string) {
                    case $this->translator->trans('milex.lead.note.searchcommand.general'):
                    case $this->translator->trans('milex.lead.note.searchcommand.general', [], null, 'en_US'):
                        $filter->string  = 'general';
                        $returnParameter = true;
                        break;
                    case $this->translator->trans('milex.lead.note.searchcommand.call'):
                    case $this->translator->trans('milex.lead.note.searchcommand.call', [], null, 'en_US'):
                        $filter->string  = 'call';
                        $returnParameter = true;
                        break;
                    case $this->translator->trans('milex.lead.note.searchcommand.email'):
                    case $this->translator->trans('milex.lead.note.searchcommand.email', [], null, 'en_US'):
                        $filter->string  = 'email';
                        $returnParameter = true;
                        break;
                    case $this->translator->trans('milex.lead.note.searchcommand.meeting'):
                    case $this->translator->trans('milex.lead.note.searchcommand.meeting', [], null, 'en_US'):
                        $filter->string  = 'meeting';
                        $returnParameter = true;
                        break;
                }
                $expr           = $q->expr()->eq('n.type', ":$unique");
                $filter->strict = true;
                break;
        }

        if ($expr && $filter->not) {
            $expr = $q->expr()->not($expr);
        }

        if ($returnParameter) {
            $string     = ($filter->strict) ? $filter->string : "%{$filter->string}%";
            $parameters = ["$unique" => $string];
        }

        return [
            $expr,
            $parameters,
        ];
    }

    /**
     * @return array
     */
    public function getSearchCommands()
    {
        $commands = [
            'milex.lead.note.searchcommand.type' => [
                'milex.lead.note.searchcommand.general',
                'milex.lead.note.searchcommand.call',
                'milex.lead.note.searchcommand.email',
                'milex.lead.note.searchcommand.meeting',
            ],
        ];

        return array_merge($commands, parent::getSearchCommands());
    }

    /**
     * Updates lead ID (e.g. after a lead merge).
     *
     * @param $fromLeadId
     * @param $toLeadId
     */
    public function updateLead($fromLeadId, $toLeadId)
    {
        $this->_em->getConnection()->createQueryBuilder()
            ->update(MILEX_TABLE_PREFIX.'lead_notes')
            ->set('lead_id', (int) $toLeadId)
            ->where('lead_id = '.(int) $fromLeadId)
            ->execute();
    }
}