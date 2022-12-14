<?php

declare(strict_types=1);

namespace Milex\IntegrationsBundle\Integration\Interfaces;

use Milex\IntegrationsBundle\Sync\DAO\Mapping\MappingManualDAO;
use Milex\IntegrationsBundle\Sync\SyncDataExchange\SyncDataExchangeInterface;

interface SyncInterface extends IntegrationInterface
{
    public function getMappingManual(): MappingManualDAO;

    public function getSyncDataExchange(): SyncDataExchangeInterface;
}
