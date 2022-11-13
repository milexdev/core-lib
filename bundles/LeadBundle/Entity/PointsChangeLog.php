<?php

namespace Milex\LeadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Milex\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

/**
 * Class PointsChangeLog.
 */
class PointsChangeLog
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Lead
     */
    private $lead;

    /**
     * @var \Milex\CoreBundle\Entity\IpAddress
     */
    private $ipAddress;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $eventName;

    /**
     * @var string
     */
    private $actionName;

    /**
     * @var int
     */
    private $delta;

    /**
     * @var \DateTime
     */
    private $dateAdded;

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('lead_points_change_log')
            ->setCustomRepositoryClass('Milex\LeadBundle\Entity\PointsChangeLogRepository')
            ->addIndex(['date_added'], 'point_date_added');

        $builder->addBigIntIdField();

        $builder->addLead(false, 'CASCADE', false, 'pointsChangeLog');

        $builder->addIpAddress();

        $builder->createField('type', 'text')
            ->length(50)
            ->build();

        $builder->createField('eventName', 'string')
            ->columnName('event_name')
            ->build();

        $builder->createField('actionName', 'string')
            ->columnName('action_name')
            ->build();

        $builder->addField('delta', 'integer');

        $builder->addDateAdded();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return PointsChangeLog
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set eventName.
     *
     * @param string $eventName
     *
     * @return PointsChangeLog
     */
    public function setEventName($eventName)
    {
        $this->eventName = $eventName;

        return $this;
    }

    /**
     * Get eventName.
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * Set actionName.
     *
     * @param string $actionName
     *
     * @return PointsChangeLog
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;

        return $this;
    }

    /**
     * Get actionName.
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * Set delta.
     *
     * @param int $delta
     *
     * @return PointsChangeLog
     */
    public function setDelta($delta)
    {
        $this->delta = $delta;

        return $this;
    }

    /**
     * Get delta.
     *
     * @return int
     */
    public function getDelta()
    {
        return $this->delta;
    }

    /**
     * Set dateAdded.
     *
     * @param \DateTime $dateAdded
     *
     * @return PointsChangeLog
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded.
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * Set lead.
     *
     * @return PointsChangeLog
     */
    public function setLead(Lead $lead)
    {
        $this->lead = $lead;

        return $this;
    }

    /**
     * Get lead.
     *
     * @return \Milex\LeadBundle\Entity\Lead
     */
    public function getLead()
    {
        return $this->lead;
    }

    /**
     * Set ipAddress.
     *
     * @return PointsChangeLog
     */
    public function setIpAddress(\Milex\CoreBundle\Entity\IpAddress $ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get ipAddress.
     *
     * @return \Milex\CoreBundle\Entity\IpAddress
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }
}
