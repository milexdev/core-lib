<?php

namespace Milex\LeadBundle\Tests\Entity;

use Milex\LeadBundle\Entity\DoNotContact;

class DoNotContactTest extends \PHPUnit\Framework\TestCase
{
    public function testDoNotContactComments()
    {
        $doNotContact = new DoNotContact();
        $doNotContact->setComments(null);
        $this->assertSame('', $doNotContact->getComments());

        $comment      = '<script>alert(\'x\')</script>';
        $doNotContact->setComments($comment);
        $this->assertNotSame($comment, $doNotContact->getComments());
        $this->assertSame('alert(\'x\')', $doNotContact->getComments());
    }
}
