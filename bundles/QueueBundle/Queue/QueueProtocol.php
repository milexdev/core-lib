<?php

namespace Milex\QueueBundle\Queue;

/**
 * Class QueueProtocol.
 */
class QueueProtocol
{
    const BEANSTALKD = 'beanstalkd';
    const RABBITMQ   = 'rabbitmq';
}
