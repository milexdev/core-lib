<?php

namespace Milex\LeadBundle\Controller;

use Milex\CoreBundle\Controller\AbstractFormController;
use Milex\LeadBundle\Form\Type\BatchType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class BatchSegmentController extends AbstractFormController
{
    private $actionModel;

    private $segmentModel;

    /**
     * Initialize object props here to simulate constructor
     * and make the future controller refactoring easier.
     */
    public function initialize(FilterControllerEvent $event)
    {
        $this->actionModel  = $this->container->get('milex.lead.model.segment.action');
        $this->segmentModel = $this->container->get('milex.lead.model.list');
    }

    /**
     * API for batch action.
     *
     * @return JsonResponse
     */
    public function setAction()
    {
        $params     = $this->request->get('lead_batch', []);
        $contactIds = empty($params['ids']) ? [] : json_decode($params['ids']);

        if ($contactIds && is_array($contactIds)) {
            $segmentsToAdd    = $params['add'] ?? [];
            $segmentsToRemove = $params['remove'] ?? [];

            if ($segmentsToAdd) {
                $this->actionModel->addContacts($contactIds, $segmentsToAdd);
            }

            if ($segmentsToRemove) {
                $this->actionModel->removeContacts($contactIds, $segmentsToRemove);
            }

            $this->addFlash('milex.lead.batch_leads_affected', [
                '%count%' => count($contactIds),
            ]);
        } else {
            $this->addFlash('milex.core.error.ids.missing');
        }

        return new JsonResponse([
            'closeModal' => true,
            'flashes'    => $this->getFlashContent(),
        ]);
    }

    /**
     * View for batch action.
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $route = $this->generateUrl('milex_segment_batch_contact_set');
        $lists = $this->segmentModel->getUserLists();
        $items = [];

        foreach ($lists as $list) {
            $items[$list['name']] = $list['id'];
        }

        return $this->delegateView(
            [
                'viewParameters' => [
                    'form' => $this->createForm(
                        BatchType::class,
                        [],
                        [
                            'items'  => $items,
                            'action' => $route,
                        ]
                    )->createView(),
                ],
                'contentTemplate' => 'MilexLeadBundle:Batch:form.html.php',
                'passthroughVars' => [
                    'activeLink'    => '#milex_contact_index',
                    'milexContent' => 'leadBatch',
                    'route'         => $route,
                ],
            ]
        );
    }
}
