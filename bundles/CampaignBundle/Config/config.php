<?php

return [
    'routes' => [
        'main' => [
            'milex_campaignevent_action'  => [
                'path'       => '/campaigns/events/{objectAction}/{objectId}',
                'controller' => 'MauticCampaignBundle:Event:execute',
            ],
            'milex_campaignsource_action' => [
                'path'       => '/campaigns/sources/{objectAction}/{objectId}',
                'controller' => 'MauticCampaignBundle:Source:execute',
            ],
            'milex_campaign_index'        => [
                'path'       => '/campaigns/{page}',
                'controller' => 'MauticCampaignBundle:Campaign:index',
            ],
            'milex_campaign_action'       => [
                'path'       => '/campaigns/{objectAction}/{objectId}',
                'controller' => 'MauticCampaignBundle:Campaign:execute',
            ],
            'milex_campaign_contacts'     => [
                'path'       => '/campaigns/view/{objectId}/contact/{page}',
                'controller' => 'MauticCampaignBundle:Campaign:contacts',
            ],
            'milex_campaign_preview'      => [
                'path'       => '/campaign/preview/{objectId}',
                'controller' => 'MauticEmailBundle:Public:preview',
            ],
        ],
        'api'  => [
            'milex_api_campaignsstandard'            => [
                'standard_entity' => true,
                'name'            => 'campaigns',
                'path'            => '/campaigns',
                'controller'      => 'MauticCampaignBundle:Api\CampaignApi',
            ],
            'milex_api_campaigneventsstandard'       => [
                'standard_entity'     => true,
                'supported_endpoints' => [
                    'getone',
                    'getall',
                ],
                'name'                => 'events',
                'path'                => '/campaigns/events',
                'controller'          => 'MauticCampaignBundle:Api\EventApi',
            ],
            'milex_api_campaigns_events_contact'     => [
                'path'       => '/campaigns/events/contact/{contactId}',
                'controller' => 'MauticCampaignBundle:Api\EventLogApi:getContactEvents',
                'method'     => 'GET',
            ],
            'milex_api_campaigns_edit_contact_event' => [
                'path'       => '/campaigns/events/{eventId}/contact/{contactId}/edit',
                'controller' => 'MauticCampaignBundle:Api\EventLogApi:editContactEvent',
                'method'     => 'PUT',
            ],
            'milex_api_campaigns_batchedit_events'   => [
                'path'       => '/campaigns/events/batch/edit',
                'controller' => 'MauticCampaignBundle:Api\EventLogApi:editEvents',
                'method'     => 'PUT',
            ],
            'milex_api_campaign_contact_events'      => [
                'path'       => '/campaigns/{campaignId}/events/contact/{contactId}',
                'controller' => 'MauticCampaignBundle:Api\EventLogApi:getContactEvents',
                'method'     => 'GET',
            ],
            'milex_api_campaigngetcontacts'          => [
                'path'       => '/campaigns/{id}/contacts',
                'controller' => 'MauticCampaignBundle:Api\CampaignApi:getContacts',
            ],
            'milex_api_campaignaddcontact'           => [
                'path'       => '/campaigns/{id}/contact/{leadId}/add',
                'controller' => 'MauticCampaignBundle:Api\CampaignApi:addLead',
                'method'     => 'POST',
            ],
            'milex_api_campaignremovecontact'        => [
                'path'       => '/campaigns/{id}/contact/{leadId}/remove',
                'controller' => 'MauticCampaignBundle:Api\CampaignApi:removeLead',
                'method'     => 'POST',
            ],
            'milex_api_contact_clone_campaign' => [
                'path'       => '/campaigns/clone/{campaignId}',
                'controller' => 'MauticCampaignBundle:Api\CampaignApi:cloneCampaign',
                'method'     => 'POST',
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'milex.campaign.menu.index' => [
                'iconClass' => 'fa-clock-o',
                'route'     => 'milex_campaign_index',
                'access'    => 'campaign:campaigns:view',
                'priority'  => 50,
            ],
        ],
    ],

    'categories' => [
        'campaign' => null,
    ],

    'services' => [
        'events' => [
            'milex.campaign.subscriber'                => [
                'class'     => \Mautic\CampaignBundle\EventListener\CampaignSubscriber::class,
                'arguments' => [
                    'milex.helper.ip_lookup',
                    'milex.core.model.auditlog',
                    'milex.campaign.service.campaign',
                    'milex.core.service.flashbag',
                ],
            ],
            'milex.campaign.leadbundle.subscriber'     => [
                'class'     => \Mautic\CampaignBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'milex.campaign.event_collector',
                    'translator',
                    'doctrine.orm.entity_manager',
                    'router',
                ],
            ],
            'milex.campaign.calendarbundle.subscriber' => [
                'class'     => \Mautic\CampaignBundle\EventListener\CalendarSubscriber::class,
                'arguments' => [
                    'doctrine.dbal.default_connection',
                    'translator',
                    'router',
                ],
            ],
            'milex.campaign.pointbundle.subscriber'    => [
                'class' => \Mautic\CampaignBundle\EventListener\PointSubscriber::class,
            ],
            'milex.campaign.search.subscriber'         => [
                'class'     => \Mautic\CampaignBundle\EventListener\SearchSubscriber::class,
                'arguments' => [
                    'milex.campaign.model.campaign',
                    'milex.security',
                    'milex.helper.templating',
                ],
            ],
            'milex.campaign.dashboard.subscriber'      => [
                'class'     => \Mautic\CampaignBundle\EventListener\DashboardSubscriber::class,
                'arguments' => [
                    'milex.campaign.model.campaign',
                    'milex.campaign.model.event',
                ],
            ],
            'milex.campaignconfigbundle.subscriber'    => [
                'class' => \Mautic\CampaignBundle\EventListener\ConfigSubscriber::class,
            ],
            'milex.campaign.stats.subscriber'          => [
                'class'     => \Mautic\CampaignBundle\EventListener\StatsSubscriber::class,
                'arguments' => [
                    'milex.security',
                    'doctrine.orm.entity_manager',
                ],
            ],
            'milex.campaign.report.subscriber'         => [
                'class'     => \Mautic\CampaignBundle\EventListener\ReportSubscriber::class,
                'arguments' => [
                    'milex.lead.model.company_report_data',
                ],
            ],
            'milex.campaign.action.change_membership.subscriber' => [
                'class'     => \Mautic\CampaignBundle\EventListener\CampaignActionChangeMembershipSubscriber::class,
                'arguments' => [
                    'milex.campaign.membership.manager',
                    'milex.campaign.model.campaign',
                ],
            ],
            'milex.campaign.action.jump_to_event.subscriber' => [
                'class'     => \Mautic\CampaignBundle\EventListener\CampaignActionJumpToEventSubscriber::class,
                'arguments' => [
                    'milex.campaign.repository.event',
                    'milex.campaign.event_executioner',
                    'translator',
                    'milex.campaign.repository.lead',
                ],
            ],
        ],
        'forms'        => [
            'milex.campaign.type.form'                 => [
                'class'     => 'Mautic\CampaignBundle\Form\Type\CampaignType',
                'arguments' => [
                    'milex.security',
                    'translator',
                ],
            ],
            'milex.campaignrange.type.action'          => [
                'class' => 'Mautic\CampaignBundle\Form\Type\EventType',
            ],
            'milex.campaign.type.campaignlist'         => [
                'class'     => 'Mautic\CampaignBundle\Form\Type\CampaignListType',
                'arguments' => [
                    'milex.campaign.model.campaign',
                    'translator',
                    'milex.security',
                ],
            ],
            'milex.campaign.type.trigger.leadchange'   => [
                'class' => 'Mautic\CampaignBundle\Form\Type\CampaignEventLeadChangeType',
            ],
            'milex.campaign.type.action.addremovelead' => [
                'class' => 'Mautic\CampaignBundle\Form\Type\CampaignEventAddRemoveLeadType',
            ],
            'milex.campaign.type.action.jump_to_event' => [
                'class' => \Mautic\CampaignBundle\Form\Type\CampaignEventJumpToEventType::class,
            ],
            'milex.campaign.type.canvassettings'       => [
                'class' => 'Mautic\CampaignBundle\Form\Type\EventCanvasSettingsType',
            ],
            'milex.campaign.type.leadsource'           => [
                'class'     => 'Mautic\CampaignBundle\Form\Type\CampaignLeadSourceType',
                'arguments' => 'milex.factory',
            ],
            'milex.form.type.campaignconfig'           => [
                'class'     => 'Mautic\CampaignBundle\Form\Type\ConfigType',
                'arguments' => 'translator',
            ],
        ],
        'models' => [
            'milex.campaign.model.campaign' => [
                'class'     => \Mautic\CampaignBundle\Model\CampaignModel::class,
                'arguments' => [
                    'milex.lead.model.list',
                    'milex.form.model.form',
                    'milex.campaign.event_collector',
                    'milex.campaign.membership.builder',
                    'milex.tracker.contact',
                ],
            ],
            'milex.campaign.model.event'     => [
                'class'     => \Mautic\CampaignBundle\Model\EventModel::class,
                'arguments' => [
                    'milex.user.model.user',
                    'milex.core.model.notification',
                    'milex.campaign.model.campaign',
                    'milex.lead.model.lead',
                    'milex.helper.ip_lookup',
                    'milex.campaign.executioner.realtime',
                    'milex.campaign.executioner.kickoff',
                    'milex.campaign.executioner.scheduled',
                    'milex.campaign.executioner.inactive',
                    'milex.campaign.event_executioner',
                    'milex.campaign.event_collector',
                    'milex.campaign.dispatcher.action',
                    'milex.campaign.dispatcher.condition',
                    'milex.campaign.dispatcher.decision',
                    'milex.campaign.repository.lead_event_log',
                ],
            ],
            'milex.campaign.model.event_log' => [
                'class'     => \Mautic\CampaignBundle\Model\EventLogModel::class,
                'arguments' => [
                    'milex.campaign.model.event',
                    'milex.campaign.model.campaign',
                    'milex.helper.ip_lookup',
                    'milex.campaign.scheduler',
                ],
            ],
            'milex.campaign.model.summary' => [
                'class'     => \Mautic\CampaignBundle\Model\SummaryModel::class,
                'arguments' => [
                    'milex.campaign.repository.lead_event_log',
                ],
            ],
        ],
        'repositories' => [
            'milex.campaign.repository.campaign' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Mautic\CampaignBundle\Entity\Campaign::class,
                ],
            ],
            'milex.campaign.repository.lead' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Mautic\CampaignBundle\Entity\Lead::class,
                ],
            ],
            'milex.campaign.repository.event' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Mautic\CampaignBundle\Entity\Event::class,
                ],
            ],
            'milex.campaign.repository.lead_event_log' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Mautic\CampaignBundle\Entity\LeadEventLog::class,
                ],
            ],
            'milex.campaign.repository.summary' => [
                'class'     => Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Mautic\CampaignBundle\Entity\Summary::class,
                ],
            ],
        ],
        'execution'    => [
            'milex.campaign.contact_finder.kickoff'  => [
                'class'     => \Mautic\CampaignBundle\Executioner\ContactFinder\KickoffContactFinder::class,
                'arguments' => [
                    'milex.lead.repository.lead',
                    'milex.campaign.repository.campaign',
                    'monolog.logger.milex',
                ],
            ],
            'milex.campaign.contact_finder.scheduled'  => [
                'class'     => \Mautic\CampaignBundle\Executioner\ContactFinder\ScheduledContactFinder::class,
                'arguments' => [
                    'milex.lead.repository.lead',
                    'monolog.logger.milex',
                ],
            ],
            'milex.campaign.contact_finder.inactive'     => [
                'class'     => \Mautic\CampaignBundle\Executioner\ContactFinder\InactiveContactFinder::class,
                'arguments' => [
                    'milex.lead.repository.lead',
                    'milex.campaign.repository.lead',
                    'monolog.logger.milex',
                ],
            ],
            'milex.campaign.dispatcher.action'        => [
                'class'     => \Mautic\CampaignBundle\Executioner\Dispatcher\ActionDispatcher::class,
                'arguments' => [
                    'event_dispatcher',
                    'monolog.logger.milex',
                    'milex.campaign.scheduler',
                    'milex.campaign.helper.notification',
                    'milex.campaign.legacy_event_dispatcher',
                ],
            ],
            'milex.campaign.dispatcher.condition'        => [
                'class'     => \Mautic\CampaignBundle\Executioner\Dispatcher\ConditionDispatcher::class,
                'arguments' => [
                    'event_dispatcher',
                ],
            ],
            'milex.campaign.dispatcher.decision'        => [
                'class'     => \Mautic\CampaignBundle\Executioner\Dispatcher\DecisionDispatcher::class,
                'arguments' => [
                    'event_dispatcher',
                    'milex.campaign.legacy_event_dispatcher',
                ],
            ],
            'milex.campaign.event_logger' => [
                'class'     => \Mautic\CampaignBundle\Executioner\Logger\EventLogger::class,
                'arguments' => [
                    'milex.helper.ip_lookup',
                    'milex.tracker.contact',
                    'milex.campaign.repository.lead_event_log',
                    'milex.campaign.repository.lead',
                    'milex.campaign.model.summary',
                ],
            ],
            'milex.campaign.event_collector' => [
                'class'     => \Mautic\CampaignBundle\EventCollector\EventCollector::class,
                'arguments' => [
                    'translator',
                    'event_dispatcher',
                ],
            ],
            'milex.campaign.scheduler.datetime'      => [
                'class'     => \Mautic\CampaignBundle\Executioner\Scheduler\Mode\DateTime::class,
                'arguments' => [
                    'monolog.logger.milex',
                ],
            ],
            'milex.campaign.scheduler.interval'      => [
                'class'     => \Mautic\CampaignBundle\Executioner\Scheduler\Mode\Interval::class,
                'arguments' => [
                    'monolog.logger.milex',
                    'milex.helper.core_parameters',
                ],
            ],
            'milex.campaign.scheduler'               => [
                'class'     => \Mautic\CampaignBundle\Executioner\Scheduler\EventScheduler::class,
                'arguments' => [
                    'monolog.logger.milex',
                    'milex.campaign.event_logger',
                    'milex.campaign.scheduler.interval',
                    'milex.campaign.scheduler.datetime',
                    'milex.campaign.event_collector',
                    'event_dispatcher',
                    'milex.helper.core_parameters',
                ],
            ],
            'milex.campaign.executioner.action' => [
                'class'     => \Mautic\CampaignBundle\Executioner\Event\ActionExecutioner::class,
                'arguments' => [
                    'milex.campaign.dispatcher.action',
                    'milex.campaign.event_logger',
                ],
            ],
            'milex.campaign.executioner.condition' => [
                'class'     => \Mautic\CampaignBundle\Executioner\Event\ConditionExecutioner::class,
                'arguments' => [
                    'milex.campaign.dispatcher.condition',
                ],
            ],
            'milex.campaign.executioner.decision' => [
                'class'     => \Mautic\CampaignBundle\Executioner\Event\DecisionExecutioner::class,
                'arguments' => [
                    'milex.campaign.event_logger',
                    'milex.campaign.dispatcher.decision',
                ],
            ],
            'milex.campaign.event_executioner' => [
                'class'     => \Mautic\CampaignBundle\Executioner\EventExecutioner::class,
                'arguments' => [
                    'milex.campaign.event_collector',
                    'milex.campaign.event_logger',
                    'milex.campaign.executioner.action',
                    'milex.campaign.executioner.condition',
                    'milex.campaign.executioner.decision',
                    'monolog.logger.milex',
                    'milex.campaign.scheduler',
                    'milex.campaign.helper.removed_contact_tracker',
                    'milex.campaign.repository.lead',
                ],
            ],
            'milex.campaign.executioner.kickoff'     => [
                'class'     => \Mautic\CampaignBundle\Executioner\KickoffExecutioner::class,
                'arguments' => [
                    'monolog.logger.milex',
                    'milex.campaign.contact_finder.kickoff',
                    'translator',
                    'milex.campaign.event_executioner',
                    'milex.campaign.scheduler',
                ],
            ],
            'milex.campaign.executioner.scheduled'     => [
                'class'     => \Mautic\CampaignBundle\Executioner\ScheduledExecutioner::class,
                'arguments' => [
                    'milex.campaign.repository.lead_event_log',
                    'monolog.logger.milex',
                    'translator',
                    'milex.campaign.event_executioner',
                    'milex.campaign.scheduler',
                    'milex.campaign.contact_finder.scheduled',
                ],
            ],
            'milex.campaign.executioner.realtime'     => [
                'class'     => \Mautic\CampaignBundle\Executioner\RealTimeExecutioner::class,
                'arguments' => [
                    'monolog.logger.milex',
                    'milex.lead.model.lead',
                    'milex.campaign.repository.event',
                    'milex.campaign.event_executioner',
                    'milex.campaign.executioner.decision',
                    'milex.campaign.event_collector',
                    'milex.campaign.scheduler',
                    'milex.tracker.contact',
                    'milex.campaign.helper.decision',
                ],
            ],
            'milex.campaign.executioner.inactive'     => [
                'class'     => \Mautic\CampaignBundle\Executioner\InactiveExecutioner::class,
                'arguments' => [
                    'milex.campaign.contact_finder.inactive',
                    'monolog.logger.milex',
                    'translator',
                    'milex.campaign.scheduler',
                    'milex.campaign.helper.inactivity',
                    'milex.campaign.event_executioner',
                ],
            ],
            'milex.campaign.helper.decision' => [
                'class'     => \Mautic\CampaignBundle\Executioner\Helper\DecisionHelper::class,
                'arguments' => [
                    'milex.campaign.repository.lead',
                ],
            ],
            'milex.campaign.helper.inactivity' => [
                'class'     => \Mautic\CampaignBundle\Executioner\Helper\InactiveHelper::class,
                'arguments' => [
                    'milex.campaign.scheduler',
                    'milex.campaign.contact_finder.inactive',
                    'milex.campaign.repository.lead_event_log',
                    'milex.campaign.repository.event',
                    'monolog.logger.milex',
                    'milex.campaign.helper.decision',
                ],
            ],
            'milex.campaign.helper.removed_contact_tracker' => [
                'class' => \Mautic\CampaignBundle\Helper\RemovedContactTracker::class,
            ],
            'milex.campaign.helper.notification' => [
                'class'     => \Mautic\CampaignBundle\Executioner\Helper\NotificationHelper::class,
                'arguments' => [
                    'milex.user.model.user',
                    'milex.core.model.notification',
                    'translator',
                    'router',
                    'milex.helper.core_parameters',
                ],
            ],
            // @deprecated 2.13.0 for BC support; to be removed in 3.0
            'milex.campaign.legacy_event_dispatcher' => [
                'class'     => \Mautic\CampaignBundle\Executioner\Dispatcher\LegacyEventDispatcher::class,
                'arguments' => [
                    'event_dispatcher',
                    'milex.campaign.scheduler',
                    'monolog.logger.milex',
                    'milex.campaign.helper.notification',
                    'milex.factory',
                    'milex.tracker.contact',
                ],
            ],
        ],
        'membership' => [
            'milex.campaign.membership.adder' => [
                'class'     => \Mautic\CampaignBundle\Membership\Action\Adder::class,
                'arguments' => [
                    'milex.campaign.repository.lead',
                    'milex.campaign.repository.lead_event_log',
                ],
            ],
            'milex.campaign.membership.remover' => [
                'class'     => \Mautic\CampaignBundle\Membership\Action\Remover::class,
                'arguments' => [
                    'milex.campaign.repository.lead',
                    'milex.campaign.repository.lead_event_log',
                    'translator',
                    'milex.helper.template.date',
                ],
            ],
            'milex.campaign.membership.event_dispatcher' => [
                'class'     => \Mautic\CampaignBundle\Membership\EventDispatcher::class,
                'arguments' => [
                    'event_dispatcher',
                ],
            ],
            'milex.campaign.membership.manager' => [
                'class'     => \Mautic\CampaignBundle\Membership\MembershipManager::class,
                'arguments' => [
                    'milex.campaign.membership.adder',
                    'milex.campaign.membership.remover',
                    'milex.campaign.membership.event_dispatcher',
                    'milex.campaign.repository.lead',
                    'monolog.logger.milex',
                ],
            ],
            'milex.campaign.membership.builder' => [
                'class'     => \Mautic\CampaignBundle\Membership\MembershipBuilder::class,
                'arguments' => [
                    'milex.campaign.membership.manager',
                    'milex.campaign.repository.lead',
                    'milex.lead.repository.lead',
                    'translator',
                ],
            ],
        ],
        'commands' => [
            'milex.campaign.command.trigger' => [
                'class'     => \Mautic\CampaignBundle\Command\TriggerCampaignCommand::class,
                'arguments' => [
                    'milex.campaign.repository.campaign',
                    'event_dispatcher',
                    'translator',
                    'milex.campaign.executioner.kickoff',
                    'milex.campaign.executioner.scheduled',
                    'milex.campaign.executioner.inactive',
                    'monolog.logger.milex',
                    'milex.helper.template.formatter',
                    'milex.lead.model.list',
                    'milex.helper.segment.count.cache',
                ],
                'tag' => 'console.command',
            ],
            'milex.campaign.command.execute' => [
                'class'     => \Mautic\CampaignBundle\Command\ExecuteEventCommand::class,
                'arguments' => [
                    'milex.campaign.executioner.scheduled',
                    'translator',
                    'milex.helper.template.formatter',
                ],
                'tag' => 'console.command',
            ],
            'milex.campaign.command.validate' => [
                'class'     => \Mautic\CampaignBundle\Command\ValidateEventCommand::class,
                'arguments' => [
                    'milex.campaign.executioner.inactive',
                    'translator',
                    'milex.helper.template.formatter',
                ],
                'tag' => 'console.command',
            ],
            'milex.campaign.command.update' => [
                'class'     => \Mautic\CampaignBundle\Command\UpdateLeadCampaignsCommand::class,
                'arguments' => [
                    'milex.campaign.repository.campaign',
                    'translator',
                    'milex.campaign.membership.builder',
                    'monolog.logger.milex',
                    'milex.helper.template.formatter',
                ],
                'tag' => 'console.command',
            ],
            'milex.campaign.command.summarize' => [
                'class'     => \Mautic\CampaignBundle\Command\SummarizeCommand::class,
                'arguments' => [
                    'translator',
                    'milex.campaign.model.summary',
                ],
                'tag' => 'console.command',
            ],
        ],
        'services' => [
            'milex.campaign.service.campaign'=> [
                'class'     => \Mautic\CampaignBundle\Service\Campaign::class,
                'arguments' => [
                    'milex.campaign.repository.campaign',
                    'milex.email.repository.email',
                ],
            ],
        ],
        'fixtures' => [
            'milex.campaign.fixture.campaign' => [
                'class'    => \Mautic\CampaignBundle\DataFixtures\ORM\CampaignData::class,
                'tag'      => \Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'optional' => true,
            ],
        ],
    ],
    'parameters' => [
        'campaign_time_wait_on_event_false' => 'PT1H',
        'campaign_use_summary'              => 0,
        'campaign_by_range'                 => 0,
    ],
];
