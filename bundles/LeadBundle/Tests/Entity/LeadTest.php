<?php

namespace Milex\LeadBundle\Tests\Entity;

use Milex\CoreBundle\Entity\IpAddress;
use Milex\CoreBundle\Form\RequestTrait;
use Milex\LeadBundle\Entity\DoNotContact;
use Milex\LeadBundle\Entity\FrequencyRule;
use Milex\LeadBundle\Entity\Lead;

class LeadTest extends \PHPUnit\Framework\TestCase
{
    use RequestTrait;

    public function testPreferredChannels()
    {
        $frequencyRules = [
            'channel1' => [
                'frequencyNumber'  => '',
                'frequencyTime'    => '',
                'preferredChannel' => 0,
                'pauseFromDate'    => '2016-01-01',
                'pauseToDate'      => '2100-01-01',
            ],
            'channel2' => [
                'frequencyNumber'  => '',
                'frequencyTime'    => '',
                'preferredChannel' => 1,
                'pauseFromDate'    => '',
                'pauseToDate'      => '',
            ],
            'channel3' => [
                'frequencyNumber'  => '7',
                'frequencyTime'    => FrequencyRule::TIME_DAY, // 210
                'preferredChannel' => 0,
                'pauseFromDate'    => '',
                'pauseToDate'      => '',
            ],
            'channel4' => [
                'frequencyNumber'  => '',
                'frequencyTime'    => '',
                'preferredChannel' => 0,
                'pauseFromDate'    => '',
                'pauseToDate'      => '',
            ],
            'channel5' => [
                'frequencyNumber'  => '10',
                'frequencyTime'    => FrequencyRule::TIME_WEEK, // 40
                'preferredChannel' => 0,
                'pauseFromDate'    => '',
                'pauseToDate'      => '',
            ],
            'channel6' => [
                'frequencyNumber'  => '10',
                'frequencyTime'    => FrequencyRule::TIME_MONTH, // 10
                'preferredChannel' => 0,
                'pauseFromDate'    => '',
                'pauseToDate'      => '',
            ],
        ];

        $lead = new Lead();

        foreach ($frequencyRules as $channel => $rule) {
            $frequencyRule = (new FrequencyRule())
                ->setPreferredChannel($rule['preferredChannel'])
                ->setFrequencyNumber($rule['frequencyNumber'])
                ->setFrequencyTime($rule['frequencyTime'])
                ->setChannel($channel)
                ->setPauseFromDate(($rule['pauseFromDate']) ? new \DateTime($rule['pauseFromDate']) : null)
                ->setPauseToDate((($rule['pauseToDate']) ? new \DateTime($rule['pauseToDate']) : null));

            $lead->addFrequencyRule($frequencyRule);
        }

        $dnc = (new DoNotContact())->setChannel('channel4');
        $lead->addDoNotContactEntry($dnc);

        $channelRules = Lead::generateChannelRules($lead->getFrequencyRules()->toArray(), $lead->getDoNotContact()->toArray());
        $this->assertEquals(['channel2', 'channel3', 'channel5', 'channel6', 'channel1', 'channel4'], array_keys($channelRules));
    }

    public function testAdjustPoints()
    {
        // new lead
        $this->adjustPointsTest(5, $this->getLeadChangedArray(0, 5), new Lead());
        $this->adjustPointsTest(5, $this->getLeadChangedArray(0, 5), new Lead(), 'plus');
        $this->adjustPointsTest(5, $this->getLeadChangedArray(0, -5), new Lead(), 'minus');
        $this->adjustPointsTest(5, [], new Lead(), 'times');
        $this->adjustPointsTest(5, [], new Lead(), 'divide');

        // existing lead
        $lead = new Lead();
        $lead->setPoints(5);

        $this->adjustPointsTest(5, $this->getLeadChangedArray(5, 10), $lead);
        $this->adjustPointsTest(5, $this->getLeadChangedArray(10, 15), $lead);
        $this->adjustPointsTest(10, $this->getLeadChangedArray(15, 150), $lead, 'times');
        $this->adjustPointsTest(10, $this->getLeadChangedArray(150, 15), $lead, 'divide');
    }

    public function testCustomFieldGetterSetters()
    {
        $lead = new Lead();

        $fields = [
            'core' => [
                'notes' => [
                    'alias' => 'notes',
                    'label' => 'Notes',
                    'type'  => 'textarea',
                    'value' => 'Blah blah blah',
                ],
                'test' => [
                    'alias' => 'test',
                    'label' => 'Test',
                    'type'  => 'textarea',
                    'value' => 'Test blah',
                ],
            ],
        ];

        $lead->setFields($fields);

        // This should not killover with a segmentation fault due to a loop
        $lead->setNotes('hello');

        // Not using getNotes because it conflicts with an existing method and not sure what to do about that yet
        $lead->setTest('hello');
        $this->assertEquals('hello', $lead->getTest());
    }

    public function testDataIsCleanedCorrectly()
    {
        $fields = [
            'core' => [
                'boolean' => [
                    'alias' => 'boolean',
                    'label' => 'Boolean',
                    'type'  => 'boolean',
                    'value' => false,
                ],
                'dateField' => [
                    'alias' => 'dateField',
                    'label' => 'Date Time',
                    'type'  => 'datetime',
                    'value' => '12-12-2017 23:00:00',
                ],
                'multiselect' => [
                    'alias' => 'multi',
                    'label' => 'Multi Select',
                    'type'  => 'multiselect',
                    'value' => ['a', 'b', 'c'],
                ],
            ],
        ];
        $data = [
            'boolean'   => 'yes',
            'dateField' => '12-12-2017 22:03:59',
            'multi'     => 'a|b',
        ];

        $this->cleanFields($data, $fields['core']['boolean']);

        $this->cleanFields($data, $fields['core']['dateField']);

        $this->cleanFields($data, $fields['core']['multiselect']);

        $testDateObject = new \DateTime('12-12-2017 22:03:59');

        $this->assertEquals($testDateObject->format('Y-m-d H:i:s'), $data['dateField']);
        $this->assertEquals((int) true, $data['boolean']);
        $this->assertEquals(['a', 'b'], $data['multi']);
    }

    public function testCleanBooleanAndNumberAsNullAreNotConverted()
    {
        $fields = [
            'core' => [
                'boolean' => [
                    'alias' => 'boolean',
                    'label' => 'Boolean',
                    'type'  => 'boolean',
                    'value' => false,
                ],
                'number' => [
                    'alias' => 'number',
                    'label' => 'Number',
                    'type'  => 'number',
                    'value' => '1234',
                ],
            ],
        ];
        $data = [
            'boolean' => null,
            'number'  => null,
        ];

        $this->cleanFields($data, $fields['core']['boolean']);
        $this->cleanFields($data, $fields['core']['number']);

        $this->assertEquals(null, $data['boolean']);
        $this->assertEquals(null, $data['number']);
    }

    public function testAttributionDateIsAdded()
    {
        $lead = new Lead();
        $lead->addUpdatedField('attribution', 100);
        $lead->checkAttributionDate();
        $this->assertEquals((new \Datetime())->format('Y-m-d'), $lead->getFieldValue('attribution_date'));
        $this->assertNotEmpty($lead->getChanges());
    }

    public function testAttributionDateIsRemoved()
    {
        $lead = new Lead();
        $lead->setFields(
            [
                'core' => [
                    'attribution_date' => [
                        'type'  => 'date',
                        'value' => '2017-09-09',
                    ],
                    'attribution' => [
                        'type'  => 'int',
                        'value' => 100,
                    ],
                ],
            ]
        );

        $lead->addUpdatedField('attribution', 0);
        $lead->checkAttributionDate();
        $this->assertNull($lead->getFieldValue('attribution_date'));
        $this->assertNotEmpty($lead->getChanges());
    }

    public function testAttributionDateIsNotChangedWhen0ChangedToNull()
    {
        $lead = new Lead();
        $lead->setFields(
            [
                'core' => [
                        'attribution_date' => [
                            'type'  => 'date',
                            'value' => 0,
                        ],
                        'attribution' => [
                            'type'  => 'int',
                            'value' => 0,
                        ],
                    ],
            ]
        );

        $lead->checkAttributionDate();
        $this->assertEmpty($lead->getChanges());
    }

    public function testChangingPropertiesHydratesFieldChanges()
    {
        $email = 'foo@bar.com';
        $lead  = new Lead();
        $lead->addUpdatedField('email', $email);
        $changes = $lead->getChanges();

        $this->assertFalse(empty($changes['email']));
        $this->assertFalse(empty($changes['fields']['email']));

        $this->assertEquals($email, $changes['email'][1]);
        $this->assertEquals($email, $changes['fields']['email'][1]);
    }

    public function testIpAddressChanges()
    {
        $ip1 = (new IpAddress())->setIpAddress('1.2.3.4');
        $ip2 = (new IpAddress())->setIpAddress('1.2.3.5');

        $contact = new Lead();

        $this->assertCount(0, $contact->getChanges());

        $contact->addIpAddress($ip1);
        $changes = $contact->getChanges();

        $this->assertSame(['1.2.3.4' => $ip1], $contact->getChanges()['ipAddressList']);

        $contact->addIpAddress($ip2);

        $this->assertSame(['1.2.3.4' => $ip1, '1.2.3.5' => $ip2], $contact->getChanges()['ipAddressList']);
    }

    /**
     * @param      $points
     * @param      $expected
     * @param bool $operator
     */
    private function adjustPointsTest($points, $expected, Lead $lead, $operator = false)
    {
        if ($operator) {
            $lead->adjustPoints($points, $operator);
        } else {
            $lead->adjustPoints($points);
        }

        $this->assertEquals($expected, $lead->getChanges());
    }

    /**
     * @param int $oldValue
     * @param int $newValue
     *
     * @return array
     */
    private function getLeadChangedArray($oldValue = 0, $newValue = 0)
    {
        return [
            'points' => [
                0 => $oldValue,
                1 => $newValue,
            ],
        ];
    }
}
