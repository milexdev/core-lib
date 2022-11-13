<?php

namespace Milex\DashboardBundle;

/**
 * Class LeadEvents
 * Events available for DashboardBundle.
 */
final class DashboardEvents
{
    /**
     * The milex.dashboard_on_widget_list_generate event is dispatched when generating a list of available widget types.
     *
     * The event listener receives a
     * Milex\DashbardBundle\Event\WidgetTypeListEvent instance.
     *
     * @var string
     */
    public const DASHBOARD_ON_MODULE_LIST_GENERATE = 'milex.dashboard_on_widget_list_generate';

    /**
     * The milex.dashboard_on_widget_form_generate event is dispatched when generating the form of a widget type.
     *
     * The event listener receives a
     * Milex\DashbardBundle\Event\WidgetFormEvent instance.
     *
     * @var string
     */
    public const DASHBOARD_ON_MODULE_FORM_GENERATE = 'milex.dashboard_on_widget_form_generate';

    /**
     * The milex.dashboard_on_widget_detail_generate event is dispatched when generating the detail of a widget type.
     *
     * The event listener receives a
     * Milex\DashbardBundle\Event\WidgetDetailEvent instance.
     *
     * @var string
     */
    public const DASHBOARD_ON_MODULE_DETAIL_GENERATE = 'milex.dashboard_on_widget_detail_generate';

    /**
     * The milex.dashboard_on_widget_detail_pre_load event is dispatched before detail of a widget type is generate.
     *
     * The event listener receives a
     * Milex\DashboardBundle\Event\WidgetDetailEvent instance.
     *
     * @var string
     */
    public const DASHBOARD_ON_MODULE_DETAIL_PRE_LOAD = 'milex.dashboard_on_widget_detail_pre_load';
}
