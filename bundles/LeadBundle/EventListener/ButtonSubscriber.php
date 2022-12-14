<?php

namespace Milex\LeadBundle\EventListener;

use Milex\CoreBundle\CoreEvents;
use Milex\CoreBundle\Event\CustomButtonEvent;
use Milex\CoreBundle\Templating\Helper\ButtonHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ButtonSubscriber implements EventSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(TranslatorInterface $translator, RouterInterface $router)
    {
        $this->translator = $translator;
        $this->router     = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::VIEW_INJECT_CUSTOM_BUTTONS => ['injectViewButtons', 0],
        ];
    }

    public function injectViewButtons(CustomButtonEvent $event)
    {
        if (0 === strpos($event->getRoute(), 'milex_contact_index')) {
            $exportRoute = $this->router->generate(
                'milex_contact_action',
                ['objectAction' => 'batchExport']
            );

            $event->addButton(
                [
                    'attr' => [
                        'data-toggle'           => 'confirmation',
                        'href'                  => $exportRoute.'?filetype=xlsx',
                        'data-precheck'         => 'batchActionPrecheck',
                        'data-message'          => $this->translator->trans('milex.core.export.items', ['%items%' => 'contacts']),
                        'data-confirm-text'     => $this->translator->trans('milex.core.export.xlsx'),
                        'data-confirm-callback' => 'executeBatchAction',
                        'data-cancel-text'      => $this->translator->trans('milex.core.form.cancel'),
                        'data-cancel-callback'  => 'dismissConfirmation',
                    ],
                    'btnText'   => $this->translator->trans('milex.core.export.xlsx'),
                    'iconClass' => 'fa fa-file-excel-o',
                ],
                ButtonHelper::LOCATION_BULK_ACTIONS
            );

            $event->addButton(
                [
                    'attr' => [
                        'data-toggle'           => 'confirmation',
                        'href'                  => $exportRoute.'?filetype=csv',
                        'data-precheck'         => 'batchActionPrecheck',
                        'data-message'          => $this->translator->trans('milex.core.export.items', ['%items%' => 'contacts']),
                        'data-confirm-text'     => $this->translator->trans('milex.core.export.csv'),
                        'data-confirm-callback' => 'executeBatchAction',
                        'data-cancel-text'      => $this->translator->trans('milex.core.form.cancel'),
                        'data-cancel-callback'  => 'dismissConfirmation',
                    ],
                    'btnText'   => $this->translator->trans('milex.core.export.csv'),
                    'iconClass' => 'fa fa-file-text-o',
                ],
                ButtonHelper::LOCATION_BULK_ACTIONS
            );

            $event->addButton(
                [
                    'attr' => [
                        'href'        => $exportRoute.'?filetype=xlsx',
                        'data-toggle' => null,
                    ],
                    'btnText'   => $this->translator->trans('milex.core.export.xlsx'),
                    'iconClass' => 'fa fa-file-excel-o',
                ],
                ButtonHelper::LOCATION_PAGE_ACTIONS
            );

            $event->addButton(
                [
                    'attr' => [
                        'href'        => $exportRoute.'?filetype=csv',
                        'data-toggle' => null,
                    ],
                    'btnText'   => $this->translator->trans('milex.core.export.csv'),
                    'iconClass' => 'fa fa-file-text-o',
                ],
                ButtonHelper::LOCATION_PAGE_ACTIONS
            );
        }
    }
}
