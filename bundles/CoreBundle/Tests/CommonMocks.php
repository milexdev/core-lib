<?php

namespace Milex\CoreBundle\Tests;

use Doctrine\ORM\EntityManager;
use Milex\CoreBundle\Helper\BundleHelper;
use Milex\CoreBundle\Helper\CoreParametersHelper;
use Milex\CoreBundle\Helper\IpLookupHelper;
use Milex\CoreBundle\Helper\PathsHelper;
use Milex\CoreBundle\Model\AuditLogModel;
use Milex\CoreBundle\Translation\Translator;
use PHPUnit\Framework\MockObject\MockObject;

abstract class CommonMocks extends \PHPUnit\Framework\TestCase
{
    /**
     * @return MockObject&Translator
     */
    protected function getTranslatorMock()
    {
        $translator = $this->createMock(Translator::class);
        $translator->method('hasId')
            ->will($this->returnValue(false));

        return $translator;
    }

    /**
     * @return MockObject&EntityManager
     */
    protected function getEntityManagerMock()
    {
        return $this->createMock(EntityManager::class);
    }

    /**
     * @return MockObject&PathsHelper
     */
    protected function getPathsHelperMock()
    {
        return $this->createMock(PathsHelper::class);
    }

    /**
     * @return MockObject&CoreParametersHelper
     */
    protected function getCoreParametersHelperMock()
    {
        return $this->createMock(CoreParametersHelper::class);
    }

    /**
     * @return MockObject&BundleHelper
     */
    protected function getBundleHelperMock()
    {
        return $this->createMock(BundleHelper::class);
    }

    /**
     * @return MockObject&IpLookupHelper
     */
    protected function getIpLookupHelperMock()
    {
        return $this->createMock(IpLookupHelper::class);
    }

    /**
     * @return MockObject&AuditLogModel
     */
    protected function getAuditLogModelMock()
    {
        return $this->createMock(AuditLogModel::class);
    }
}
