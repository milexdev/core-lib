<?php

namespace Milex\PageBundle\Controller;

use Milex\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Milex\CoreBundle\Controller\VariantAjaxControllerTrait;
use Milex\CoreBundle\Helper\InputHelper;
use Milex\PageBundle\Form\Type\AbTestPropertiesType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AjaxController.
 */
class AjaxController extends CommonAjaxController
{
    use VariantAjaxControllerTrait;

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function getAbTestFormAction(Request $request)
    {
        return $this->getAbTestForm(
            $request,
            'page',
            AbTestPropertiesType::class,
            'page_abtest_settings',
            'page',
            'MilexPageBundle:AbTest:form.html.php',
            ['MilexPageBundle:AbTest:form.html.php', 'MilexPageBundle:FormTheme\Page']
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function pageListAction(Request $request)
    {
        $filter    = InputHelper::clean($request->query->get('filter'));
        $results   = $this->getModel('page.page')->getLookupResults('page', $filter);
        $dataArray = [];

        foreach ($results as $r) {
            $dataArray[] = [
                'label' => $r['title']." ({$r['id']}:{$r['alias']})",
                'value' => $r['id'],
            ];
        }

        return $this->sendJsonResponse($dataArray);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function setBuilderContentAction(Request $request)
    {
        $dataArray = ['success' => 0];
        $entityId  = InputHelper::clean($request->request->get('entity'));
        $session   = $this->get('session');

        if (!empty($entityId)) {
            $sessionVar = 'milex.pagebuilder.'.$entityId.'.content';

            // Check for an array of slots
            $slots   = InputHelper::_($request->request->get('slots', [], true), 'html');
            $content = $session->get($sessionVar, []);

            if (!is_array($content)) {
                $content = [];
            }

            if (!empty($slots)) {
                // Builder was closed so save each content
                foreach ($slots as $slot => $newContent) {
                    $content[$slot] = $newContent;
                }

                $session->set($sessionVar, $content);
                $dataArray['success'] = 1;
            } else {
                // Check for a single slot
                $newContent = InputHelper::html($request->request->get('content'));
                $slot       = InputHelper::clean($request->request->get('slot'));

                if (!empty($slot)) {
                    $content[$slot] = $newContent;
                    $session->set($sessionVar, $content);
                    $dataArray['success'] = 1;
                }
            }
        }

        return $this->sendJsonResponse($dataArray);
    }

    /**
     * Called by parent::getBuilderTokensAction().
     *
     * @param $query
     *
     * @return array
     */
    protected function getBuilderTokens($query)
    {
        /** @var \Milex\PageBundle\Model\PageModel $model */
        $model = $this->getModel('page');

        return $model->getBuilderComponents(null, ['tokens'], $query);
    }
}
