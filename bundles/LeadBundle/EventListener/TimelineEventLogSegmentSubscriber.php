<?php

namespace Milex\LeadBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Milex\CoreBundle\Helper\UserHelper;
use Milex\LeadBundle\Entity\Lead;
use Milex\LeadBundle\Entity\LeadEventLog;
use Milex\LeadBundle\Entity\LeadEventLogRepository;
use Milex\LeadBundle\Entity\LeadList;
use Milex\LeadBundle\Event\LeadTimelineEvent;
use Milex\LeadBundle\Event\ListChangeEvent;
use Milex\LeadBundle\LeadEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TimelineEventLogSegmentSubscriber implements EventSubscriberInterface
{
    use TimelineEventLogTrait;

    /**
     * @var UserHelper
     */
    private $userHelper;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * TimelineEventLogSegmentSubscriber constructor.
     */
    public function __construct(
        LeadEventLogRepository $eventLogRepository,
        UserHelper $userHelper,
        TranslatorInterface $translator,
        EntityManagerInterface $em
    ) {
        $this->eventLogRepository = $eventLogRepository;
        $this->userHelper         = $userHelper;
        $this->translator         = $translator;
        $this->em                 = $em;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::LEAD_LIST_CHANGE       => 'onChange',
            LeadEvents::LEAD_LIST_BATCH_CHANGE => 'onBatchChange',
            LeadEvents::TIMELINE_ON_GENERATE   => 'onTimelineGenerate',
        ];
    }

    public function onChange(ListChangeEvent $event)
    {
        if (!$contact = $event->getLead()) {
            return;
        }

        $this->writeEntries(
            [$contact],
            $event->getList(),
            $event->wasAdded() ? 'added' : 'removed'
        );
    }

    public function onTimelineGenerate(LeadTimelineEvent $event)
    {
        $this->addEvents(
            $event,
            'segment_membership',
            'milex.lead.timeline.segment_membership',
            'fa-pie-chart',
            'lead',
            'segment'
        );
    }

    public function onBatchChange(ListChangeEvent $event)
    {
        if (!$contacts = $event->getLeads()) {
            return;
        }

        $this->writeEntries(
            $contacts,
            $event->getList(),
            $event->wasAdded() ? 'added' : 'removed'
        );
    }

    /**
     * @param $action
     */
    private function writeEntries(array $contacts, LeadList $segment, $action)
    {
        $user                    = $this->userHelper->getUser();
        $logs                    = [];
        $detachContactReferences = false;

        foreach ($contacts as $key => $contact) {
            if (!$contact instanceof Lead) {
                $id                      = is_array($contact) ? $contact['id'] : $contact;
                $contact                 = $this->em->getReference('MilexLeadBundle:Lead', $id);
                $contacts[$key]          = $contact;
                $detachContactReferences = true;
            }

            $log = new LeadEventLog();
            $log->setUserId($user->getId())
                ->setUserName($user->getUsername() ?: $this->translator->trans('milex.core.system'))
                ->setLead($contact)
                ->setBundle('lead')
                ->setAction($action)
                ->setObject('segment')
                ->setObjectId($segment->getId())
                ->setProperties(
                    [
                        'object_description' => $segment->getName(),
                    ]
                );

            $logs[] = $log;
        }

        $this->eventLogRepository->saveEntities($logs);
        $this->eventLogRepository->clear();

        if ($detachContactReferences) {
            foreach ($contacts as $contact) {
                $this->em->detach($contact);
            }
        }
    }
}
