<?php

namespace Milex\SmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SmsTransportPass implements CompilerPassInterface
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    public function process(ContainerBuilder $container)
    {
        $this->container = $container;

        $this->registerTransports();
        $this->registerCallbacks();
    }

    private function registerTransports()
    {
        if (!$this->container->has('milex.sms.transport_chain')) {
            return;
        }

        $definition     = $this->container->getDefinition('milex.sms.transport_chain');
        $taggedServices = $this->container->findTaggedServiceIds('milex.sms_transport');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addTransport', [
                $id,
                new Reference($id),
                !empty($tags[0]['alias']) ? $tags[0]['alias'] : $id,
                !empty($tags[0]['integrationAlias']) ? $tags[0]['integrationAlias'] : $id,
            ]);
        }
    }

    private function registerCallbacks()
    {
        if (!$this->container->has('milex.sms.callback_handler_container')) {
            return;
        }

        $definition     = $this->container->getDefinition('milex.sms.callback_handler_container');
        $taggedServices = $this->container->findTaggedServiceIds('milex.sms_callback_handler');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('registerHandler', [
                new Reference($id),
            ]);
        }
    }
}
