<?php

namespace Milex\LeadBundle\Segment\Decorator;

use Milex\LeadBundle\Exception\FilterNotFoundException;
use Milex\LeadBundle\Segment\ContactSegmentFilterCrate;
use Milex\LeadBundle\Segment\ContactSegmentFilterOperator;
use Milex\LeadBundle\Services\ContactSegmentFilterDictionary;

class CustomMappedDecorator extends BaseDecorator
{
    /**
     * @var ContactSegmentFilterDictionary
     */
    protected $dictionary;

    /**
     * CustomMappedDecorator constructor.
     */
    public function __construct(
        ContactSegmentFilterOperator $contactSegmentFilterOperator,
        ContactSegmentFilterDictionary $contactSegmentFilterDictionary
    ) {
        parent::__construct($contactSegmentFilterOperator);
        $this->dictionary = $contactSegmentFilterDictionary;
    }

    /**
     * @return string|null
     */
    public function getField(ContactSegmentFilterCrate $contactSegmentFilterCrate)
    {
        $originalField = $contactSegmentFilterCrate->getField();

        try {
            return $this->dictionary->getFilterProperty($originalField, 'field');
        } catch (FilterNotFoundException $e) {
            return parent::getField($contactSegmentFilterCrate);
        }
    }

    /**
     * @return string
     */
    public function getTable(ContactSegmentFilterCrate $contactSegmentFilterCrate)
    {
        $originalField = $contactSegmentFilterCrate->getField();

        try {
            return MILEX_TABLE_PREFIX.$this->dictionary->getFilterProperty($originalField, 'foreign_table');
        } catch (FilterNotFoundException $e) {
            return parent::getTable($contactSegmentFilterCrate);
        }
    }

    /**
     * @return string
     */
    public function getQueryType(ContactSegmentFilterCrate $contactSegmentFilterCrate)
    {
        $originalField = $contactSegmentFilterCrate->getField();

        try {
            return $this->dictionary->getFilterProperty($originalField, 'type');
        } catch (FilterNotFoundException $e) {
            return parent::getQueryType($contactSegmentFilterCrate);
        }
    }

    /**
     * @return string|bool if no func needed
     */
    public function getAggregateFunc(ContactSegmentFilterCrate $contactSegmentFilterCrate)
    {
        $originalField = $contactSegmentFilterCrate->getField();

        try {
            return $this->dictionary->getFilterProperty($originalField, 'func');
        } catch (FilterNotFoundException $e) {
            return false;
        }
    }

    /**
     * @return \Milex\LeadBundle\Segment\Query\Expression\CompositeExpression|string|null
     */
    public function getWhere(ContactSegmentFilterCrate $contactSegmentFilterCrate)
    {
        $originalField = $contactSegmentFilterCrate->getField();

        try {
            return $this->dictionary->getFilterProperty($originalField, 'where');
        } catch (FilterNotFoundException $e) {
            return parent::getWhere($contactSegmentFilterCrate);
        }
    }
}