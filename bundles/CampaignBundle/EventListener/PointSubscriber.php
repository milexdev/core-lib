<?php

namespace Mautic\CampaignBundle\EventListener;

use Mautic\CampaignBundle\Form\Type\CampaignEventAddRemoveLeadType;
use Mautic\PointBundle\Event\TriggerBuilderEvent;
use Mautic\PointBundle\PointEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PointSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PointEvents::TRIGGER_ON_BUILD => ['onTriggerBuild', 0],
        ];
    }

    public function onTriggerBuild(TriggerBuilderEvent $event)
    {
        $changeLists = [
            'group'    => 'milex.campaign.point.trigger',
            'label'    => 'milex.campaign.point.trigger.changecampaigns',
            'callback' => ['\\Mautic\\CampaignBundle\\Helper\\CampaignEventHelper', 'addRemoveLead'],
            'formType' => CampaignEventAddRemoveLeadType::class,
        ];

        $event->addEvent('campaign.changecampaign', $changeLists);
    }
}
