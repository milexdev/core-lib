<?php

declare(strict_types=1);

namespace Milex\IntegrationsBundle\Helper;

use Milex\IntegrationsBundle\Integration\Interfaces\ConfigFormSyncInterface;
use Milex\IntegrationsBundle\Mapping\MappedFieldInfoInterface;

class FieldFilterHelper
{
    /**
     * @var int
     */
    private $totalFieldCount = 0;

    /**
     * @var MappedFieldInfoInterface[]
     */
    private $filteredFields = [];

    /**
     * @var ConfigFormSyncInterface
     */
    private $integrationObject;

    public function __construct(ConfigFormSyncInterface $integrationObject)
    {
        $this->integrationObject = $integrationObject;
    }

    public function filterFieldsByPage(string $objectName, int $page, int $limit = 15): void
    {
        $allFields             = $this->integrationObject->getAllFieldsForMapping($objectName);
        $this->filteredFields  = $this->getPageOfFields($allFields, $page, $limit);
        $this->totalFieldCount = count($allFields);
    }

    public function filterFieldsByKeyword(string $objectName, string $keyword, int $page, int $limit = 15): void
    {
        $allFields            = $this->integrationObject->getAllFieldsForMapping($objectName);
        $this->filteredFields = $this->getFieldsByKeyword($allFields, $keyword);

        // Paginate filtered fields
        $this->totalFieldCount = count($this->filteredFields);
        $this->filteredFields  = $this->getPageOfFields($this->filteredFields, $page, $limit);
    }

    public function getTotalFieldCount(): int
    {
        return $this->totalFieldCount;
    }

    /**
     * @return MappedFieldInfoInterface[]
     */
    public function getFilteredFields(): array
    {
        return $this->filteredFields;
    }

    /**
     * @return MappedFieldInfoInterface[]
     */
    private function getPageOfFields(array $fields, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        return array_slice($fields, $offset, $limit, true);
    }

    /**
     * @return MappedFieldInfoInterface[]
     */
    private function getFieldsByKeyword(array $fields, string $keyword): array
    {
        $found = [];

        foreach ($fields as $name => $field) {
            if (!stristr($field->getName(), $keyword)) {
                continue;
            }

            $found[$name] = $field;
        }

        return $found;
    }
}
