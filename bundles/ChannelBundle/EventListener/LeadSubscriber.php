<?php

namespace Mautic\ChannelBundle\EventListener;

use Mautic\ChannelBundle\Entity\MessageQueueRepository;
use Mautic\LeadBundle\Event\LeadTimelineEvent;
use Mautic\LeadBundle\LeadEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class LeadSubscriber implements EventSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var MessageQueueRepository
     */
    private $messageQueueRepository;

    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        MessageQueueRepository $messageQueueRepository
    ) {
        $this->translator             = $translator;
        $this->router                 = $router;
        $this->messageQueueRepository = $messageQueueRepository;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::TIMELINE_ON_GENERATE => ['onTimelineGenerate', 0],
        ];
    }

    /**
     * Compile events for the lead timeline.
     */
    public function onTimelineGenerate(LeadTimelineEvent $event)
    {
        $this->addChannelMessageEvents($event);
    }

    private function addChannelMessageEvents(LeadTimelineEvent $event)
    {
        $eventTypeKey  = 'message.queue';
        $eventTypeName = $this->translator->trans('milex.message.queue');

        $event->addEventType($eventTypeKey, $eventTypeName);
        $event->addSerializerGroup('messageQueueList');

        $label = $this->translator->trans('milex.queued.channel');

        // Decide if those events are filtered
        if (!$event->isApplicable($eventTypeKey)) {
            return;
        }

        $logs = $this->messageQueueRepository->getLeadTimelineEvents($event->getLeadId(), $event->getQueryOptions());

        // Add to counter
        $event->addToCounter($eventTypeKey, $logs);

        if (!$event->isEngagementCount()) {
            // Add the logs to the event array
            foreach ($logs['results'] as $log) {
                $eventName = [
                    'label' => $label.$log['channelName'].' '.$log['channelId'],
                    'href'  => $this->router->generate('milex_'.$log['channelName'].'_action', ['objectAction' => 'view', 'objectId' => $log['channelId']]),
                ];
                $event->addEvent(
                    [
                        'eventId'    => $eventTypeKey.$log['id'],
                        'event'      => $eventTypeKey,
                        'eventLabel' => $eventName,
                        'eventType'  => $eventTypeName,
                        'timestamp'  => $log['dateAdded'],
                        'extra'      => [
                            'log' => $log,
                        ],
                        'contentTemplate' => 'MauticChannelBundle:SubscribedEvents\Timeline:queued_messages.html.php',
                        'icon'            => 'fa-comments-o',
                        'contactId'       => $log['lead_id'],
                    ]
                );
            }
        }
    }
}
