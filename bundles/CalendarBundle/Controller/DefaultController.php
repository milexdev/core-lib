<?php

namespace Mautic\CalendarBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DefaultController.
 */
class DefaultController extends FormController
{
    /**
     * Generates the default view.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->delegateView([
            'contentTemplate' => 'MauticCalendarBundle:Default:index.html.php',
            'passthroughVars' => [
                'activeLink'    => '#milex_calendar_index',
                'milexContent' => 'calendar',
                'route'         => $this->generateUrl('milex_calendar_index'),
            ],
        ]);
    }

    /**
     * Generates the modal view.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction()
    {
        $source    = $this->request->query->get('source');
        $startDate = new \DateTime($this->request->query->get('startDate'));
        $entityId  = $this->request->query->get('objectId');

        /* @type \Mautic\CalendarBundle\Model\CalendarModel $model */
        $calendarModel = $this->getModel('calendar');
        $event         = $calendarModel->editCalendarEvent($source, $entityId);

        $model         = $event->getModel();
        $entity        = $event->getEntity();
        $session       = $this->get('session');
        $sourceSession = $this->get('session')->get('milex.calendar.'.$source, 1);

        //set the return URL
        $returnUrl = $this->generateUrl('milex_calendar_index', [$source => $sourceSession]);

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => [$source => $sourceSession],
            'contentTemplate' => $event->getContentTemplate(),
            'passthroughVars' => [
                'activeLink'    => 'milex_calendar_index',
                'milexContent' => $source,
            ],
        ];

        //not found
        if (null === $entity) {
            return $this->postActionRedirect(
                array_merge($postActionVars, [
                    'flashes' => [
                        [
                            'type'    => 'error',
                            'msg'     => 'milex.'.$source.'.error.notfound',
                            'msgVars' => ['%id%' => $entityId],
                        ],
                    ],
                ])
            );
        } elseif (!$event->hasAccess()) {
            return $this->accessDenied();
        } elseif ($model->isLocked($entity)) {
            //deny access if the entity is locked
            return $this->isLocked($postActionVars, $entity, $source.'.'.$source);
        }

        //Create the form
        $action = $this->generateUrl('milex_calendar_action', [
            'objectAction' => 'edit',
            'objectId'     => $entity->getId(),
            'source'       => $source,
            'startDate'    => $startDate->format('Y-m-d H:i:s'),
        ]);
        $form = $model->createForm($entity, $this->get('form.factory'), $action, ['formName' => $event->getFormName()]);

        ///Check for a submitted form and process it
        if ('POST' == $this->request->getMethod()) {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    $contentName     = 'milex.'.$source.'builder.'.$entity->getSessionId().'.content';
                    $existingContent = $entity->getContent();
                    $newContent      = $session->get($contentName, []);
                    $content         = array_merge($existingContent, $newContent);
                    $entity->setContent($content);

                    //form is valid so process the data
                    $model->saveEntity($entity, $form->get('buttons')->get('save')->isClicked());

                    //clear the session
                    $session->remove($contentName);

                    $this->addFlash('milex.core.notice.updated', [
                        '%name%'      => $entity->getTitle(),
                        '%menu_link%' => 'milex_'.$source.'_index',
                        '%url%'       => $this->generateUrl('milex_'.$source.'_action', [
                            'objectAction' => 'edit',
                            'objectId'     => $entity->getId(),
                        ]),
                    ]);
                }
            } else {
                //clear any modified content
                $session->remove('milex.'.$source.'builder.'.$entityId.'.content');
                //unlock the entity
                $model->unlockEntity($entity);
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                return new JsonResponse([
                    'milexContent' => 'calendarModal',
                    'closeModal'    => 1,
                ]);
            }
        } else {
            //lock the entity
            $model->lockEntity($entity);
        }

        $builderComponents = $model->getBuilderComponents($entity);

        return $this->delegateView([
            'viewParameters' => [
                'form'   => $this->setFormTheme($form, $event->getContentTemplate()),
                'tokens' => $builderComponents[$source.'Tokens'],
                'entity' => $entity,
                'model'  => $model,
            ],
            'contentTemplate' => $event->getContentTemplate(),
            'passthroughVars' => [
                'activeLink'    => '#milex_calendar_index',
                'milexContent' => 'calendarModal',
                'route'         => $this->generateUrl('milex_calendar_action', [
                    'objectAction' => 'edit',
                    'objectId'     => $entity->getId(),
                    'source'       => $source,
                    'startDate'    => $startDate->format('Y-m-d H:i:s'),
                ]),
            ],
        ]);
    }
}
