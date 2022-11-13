<?php

namespace Milex\LeadBundle\Controller;

use Milex\CoreBundle\Entity\AuditLogRepository;
use Milex\CoreBundle\Helper\Chart\ChartQuery;
use Milex\CoreBundle\Helper\Chart\LineChart;
use Milex\CoreBundle\Model\AuditLogModel;
use Milex\LeadBundle\Entity\Lead;
use Milex\LeadBundle\Model\LeadModel;

trait LeadDetailsTrait
{
    /**
     * @param int $page
     *
     * @return array
     */
    protected function getAllEngagements(array $leads, array $filters = null, array $orderBy = null, $page = 1, $limit = 25)
    {
        $session = $this->get('session');

        if (null == $filters) {
            $filters = $session->get(
                'milex.plugin.timeline.filters',
                [
                    'search'        => '',
                    'includeEvents' => [],
                    'excludeEvents' => [],
                ]
            );
        }

        if (null == $orderBy) {
            if (!$session->has('milex.plugin.timeline.orderby')) {
                $session->set('milex.plugin.timeline.orderby', 'timestamp');
                $session->set('milex.plugin.timeline.orderbydir', 'DESC');
            }

            $orderBy = [
                $session->get('milex.plugin.timeline.orderby'),
                $session->get('milex.plugin.timeline.orderbydir'),
            ];
        }

        // prepare result object
        $result = [
            'events'   => [],
            'filters'  => $filters,
            'order'    => $orderBy,
            'types'    => [],
            'total'    => 0,
            'page'     => $page,
            'limit'    => $limit,
            'maxPages' => 0,
        ];

        // get events for each contact
        foreach ($leads as $lead) {
            //  if (!$lead->getEmail()) continue; // discard contacts without email

            /** @var LeadModel $model */
            $model       = $this->getModel('lead');
            $engagements = $model->getEngagements($lead, $filters, $orderBy, $page, $limit);
            $events      = $engagements['events'];
            $types       = $engagements['types'];

            // inject lead into events
            foreach ($events as &$event) {
                $event['leadId']    = $lead->getId();
                $event['leadEmail'] = $lead->getEmail();
                $event['leadName']  = $lead->getName() ? $lead->getName() : $lead->getEmail();
            }

            $result['events'] = array_merge($result['events'], $events);
            $result['types']  = array_merge($result['types'], $types);
            $result['total'] += $engagements['total'];
        }

        $result['maxPages'] = ($limit <= 0) ? 1 : round(ceil($result['total'] / $limit));

        usort($result['events'], [$this, 'cmp']); // sort events by

        // now all events are merged, let's limit to   $limit
        array_splice($result['events'], $limit);

        $result['total'] = count($result['events']);

        return $result;
    }

    /**
     * Makes sure that the event filter array is in the right format.
     *
     * @param mixed $filters
     *
     * @return array
     *
     * @throws InvalidArgumentException if not an array
     */
    public function sanitizeEventFilter($filters)
    {
        if (!is_array($filters)) {
            throw new \InvalidArgumentException('filters parameter must be an array');
        }

        if (!isset($filters['search'])) {
            $filters['search'] = '';
        }

        if (!isset($filters['includeEvents'])) {
            $filters['includeEvents'] = [];
        }

        if (!isset($filters['excludeEvents'])) {
            $filters['excludeEvents'] = [];
        }

        return $filters;
    }

    /**
     * @param $a
     * @param $b
     *
     * @return int
     */
    private function cmp($a, $b)
    {
        if ($a['timestamp'] === $b['timestamp']) {
            return 0;
        }

        return ($a['timestamp'] < $b['timestamp']) ? +1 : -1;
    }

    /**
     * Get a list of places for the lead based on IP location.
     *
     * @return array
     */
    protected function getPlaces(Lead $lead)
    {
        // Get Places from IP addresses
        $places = [];
        if ($lead->getIpAddresses()) {
            foreach ($lead->getIpAddresses() as $ip) {
                if ($details = $ip->getIpDetails()) {
                    if (!empty($details['latitude']) && !empty($details['longitude'])) {
                        $name = 'N/A';
                        if (!empty($details['city'])) {
                            $name = $details['city'];
                        } elseif (!empty($details['region'])) {
                            $name = $details['region'];
                        }
                        $place = [
                            'latLng' => [$details['latitude'], $details['longitude']],
                            'name'   => $name,
                        ];
                        $places[] = $place;
                    }
                }
            }
        }

        return $places;
    }

    /**
     * @return mixed
     */
    protected function getEngagementData(Lead $lead, \DateTime $fromDate = null, \DateTime $toDate = null)
    {
        $translator = $this->get('translator');

        if (null == $fromDate) {
            $fromDate = new \DateTime('first day of this month 00:00:00');
            $fromDate->modify('-6 months');
        }
        if (null == $toDate) {
            $toDate = new \DateTime();
        }

        $lineChart  = new LineChart(null, $fromDate, $toDate);
        $chartQuery = new ChartQuery($this->getDoctrine()->getConnection(), $fromDate, $toDate);

        /** @var LeadModel $model */
        $model       = $this->getModel('lead');
        $engagements = $model->getEngagementCount($lead, $fromDate, $toDate, 'm', $chartQuery);
        $lineChart->setDataset($translator->trans('milex.lead.graph.line.all_engagements'), $engagements['byUnit']);

        $pointStats = $chartQuery->fetchSumTimeData('lead_points_change_log', 'date_added', ['lead_id' => $lead->getId()], 'delta');
        $lineChart->setDataset($translator->trans('milex.lead.graph.line.points'), $pointStats);

        return $lineChart->render();
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    protected function getAuditlogs(Lead $lead, array $filters = null, array $orderBy = null, $page = 1, $limit = 25)
    {
        $session = $this->get('session');

        if (null == $filters) {
            $filters = $session->get(
                'milex.lead.'.$lead->getId().'.auditlog.filters',
                [
                    'search'        => '',
                    'includeEvents' => [],
                    'excludeEvents' => [],
                ]
            );
        }

        if (null == $orderBy) {
            if (!$session->has('milex.lead.'.$lead->getId().'.auditlog.orderby')) {
                $session->set('milex.lead.'.$lead->getId().'.auditlog.orderby', 'al.dateAdded');
                $session->set('milex.lead.'.$lead->getId().'.auditlog.orderbydir', 'DESC');
            }

            $orderBy = [
                $session->get('milex.lead.'.$lead->getId().'.auditlog.orderby'),
                $session->get('milex.lead.'.$lead->getId().'.auditlog.orderbydir'),
            ];
        }

        // Audit Log
        /** @var AuditLogModel $auditlogModel */
        $auditlogModel = $this->getModel('core.auditlog');
        /** @var AuditLogRepository $repo */
        $repo     = $auditlogModel->getRepository();
        $logCount = $repo->getAuditLogsCount($lead, $filters);
        $logs     = $repo->getAuditLogs($lead, $filters, $orderBy, $page, $limit);

        $logEvents = array_map(function ($l) {
            return [
                'eventType'       => $l['action'],
                'eventLabel'      => $l['userName'],
                'timestamp'       => $l['dateAdded'],
                'details'         => $l['details'],
                'contentTemplate' => 'MilexLeadBundle:Auditlog:details.html.php',
            ];
        }, $logs);

        $types = [
            'delete'     => $this->translator->trans('milex.lead.event.delete'),
            'create'     => $this->translator->trans('milex.lead.event.create'),
            'identified' => $this->translator->trans('milex.lead.event.identified'),
            'ipadded'    => $this->translator->trans('milex.lead.event.ipadded'),
            'merge'      => $this->translator->trans('milex.lead.event.merge'),
            'update'     => $this->translator->trans('milex.lead.event.update'),
        ];

        return [
            'events'   => $logEvents,
            'filters'  => $filters,
            'order'    => $orderBy,
            'types'    => $types,
            'total'    => $logCount,
            'page'     => $page,
            'limit'    => $limit,
            'maxPages' => ceil($logCount / $limit),
        ];
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    protected function getEngagements(Lead $lead, array $filters = null, array $orderBy = null, $page = 1, $limit = 25)
    {
        $session = $this->get('session');

        if (null == $filters) {
            $filters = $session->get(
                'milex.lead.'.$lead->getId().'.timeline.filters',
                [
                    'search'        => '',
                    'includeEvents' => [],
                    'excludeEvents' => [],
                ]
            );
        }

        if (null == $orderBy) {
            if (!$session->has('milex.lead.'.$lead->getId().'.timeline.orderby')) {
                $session->set('milex.lead.'.$lead->getId().'.timeline.orderby', 'timestamp');
                $session->set('milex.lead.'.$lead->getId().'.timeline.orderbydir', 'DESC');
            }

            $orderBy = [
                $session->get('milex.lead.'.$lead->getId().'.timeline.orderby'),
                $session->get('milex.lead.'.$lead->getId().'.timeline.orderbydir'),
            ];
        }
        /** @var LeadModel $model */
        $model = $this->getModel('lead');

        return $model->getEngagements($lead, $filters, $orderBy, $page, $limit);
    }

    /**
     * Get an array with engagements and points of a contact.
     *
     * @return array
     */
    protected function getStatsCount(Lead $lead, \DateTime $fromDate = null, \DateTime $toDate = null)
    {
        if (null == $fromDate) {
            $fromDate = new \DateTime('first day of this month 00:00:00');
            $fromDate->modify('-6 months');
        }
        if (null == $toDate) {
            $toDate = new \DateTime();
        }

        /** @var LeadModel $model */
        $model       = $this->getModel('lead');
        $chartQuery  = new ChartQuery($this->getDoctrine()->getConnection(), $fromDate, $toDate);

        $engagements = $model->getEngagementCount($lead, $fromDate, $toDate, 'm', $chartQuery);
        $pointStats  = $chartQuery->fetchSumTimeData('lead_points_change_log', 'date_added', ['lead_id' => $lead->getId()], 'delta');

        return [
            'engagements' => $engagements,
            'points'      => $pointStats,
        ];
    }

    /**
     * Get an array to create company's engagements graph.
     *
     * @param array $contacts
     *
     * @return array
     */
    protected function getCompanyEngagementData($contacts)
    {
        $engagements = [0, 0, 0, 0, 0, 0];
        $points      = [0, 0, 0, 0, 0, 0];
        foreach ($contacts as $contact) {
            $model = $this->getModel('lead.lead');
            // When we change lead data these changes get cached
            // so we need to clear the entity manager
            $model->getRepository()->clear();

            /** @var \Milex\LeadBundle\Entity\Lead $lead */
            if (!isset($contact['lead_id'])) {
                continue;
            }
            $lead            = $model->getEntity($contact['lead_id']);
            if (!$lead instanceof Lead) {
                continue;
            }
            $engagementsData = $this->getStatsCount($lead);

            $engagements = array_map(function ($a, $b) {
                return $a + $b;
            }, $engagementsData['engagements']['byUnit'], $engagements);
            $points = array_map(function ($points_first_user, $points_second_user) {
                return $points_first_user + $points_second_user;
            }, $engagementsData['points'], $points);
        }

        return [
            'engagements' => $engagements,
            'points'      => $points,
        ];
    }

    /**
     * Get company graph for points and engagements.
     *
     * @param $contacts
     *
     * @return mixed
     */
    protected function getCompanyEngagementsForGraph($contacts)
    {
        $graphData  = $this->getCompanyEngagementData($contacts);
        $translator = $this->get('translator');

        $fromDate = new \DateTime('first day of this month 00:00:00');
        $fromDate->modify('-6 months');

        $toDate = new \DateTime();

        $lineChart  = new LineChart(null, $fromDate, $toDate);

        $lineChart->setDataset($translator->trans('milex.lead.graph.line.all_engagements'), $graphData['engagements']);

        $lineChart->setDataset($translator->trans('milex.lead.graph.line.points'), $graphData['points']);

        return $lineChart->render();
    }

    /**
     * @return array
     */
    protected function getScheduledCampaignEvents(Lead $lead)
    {
        // Upcoming events from Campaign Bundle
        /** @var \Milex\CampaignBundle\Entity\LeadEventLogRepository $leadEventLogRepository */
        $leadEventLogRepository = $this->getDoctrine()->getManager()->getRepository('MilexCampaignBundle:LeadEventLog');

        return $leadEventLogRepository->getUpcomingEvents(
            [
                'lead'      => $lead,
                'eventType' => ['action', 'condition'],
            ]
        );
    }
}