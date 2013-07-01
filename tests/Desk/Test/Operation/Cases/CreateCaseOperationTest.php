<?php

namespace Desk\Test\Operation\Cases;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\CreateOperationTestCase;
use DateTime;
use DateTimeZone;

/**
 * @coversNothing
 * @group system
 */
class CreateCaseOperationTest extends CreateOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'CreateCase';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSystemParameters()
    {
        $date = new DateTime('2012-05-01T21:38:48Z', new DateTimeZone('UTC'));

        return array(
            'type' => 'email',
            'subject' => 'Creating a case via the API',
            'priority' => 4,
            'status' => 'open',
            'labels' => array('Spam', 'Ignore'),
            'created_at' => $date,
            'customer_id' => 1,
            'assigned_user_id' => 1,
            'assigned_group_id' => 1,
            'locked_by_id' => 1,
            'message' => array(
                'direction' => 'in',
                'status' => 'received',
                'to' => 'someone@desk.com',
                'from' => 'someone-else@desk.com',
                'cc' => 'alpha@desk.com',
                'bcc' => 'beta@desk.com',
                'subject' => 'Creating a case via the API',
                'body' => 'Please assist me with this case',
                'created_at' => $date,
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        $body = '{"type":"email","message":{"direction":"in",' .
            '"body":"example","subject":"the subject"},' .
            '"_links":{"customer":{"class":"customer",' .
            '"href":"\\\\/api\\\\/v2\\\\/customers\\\\/4"}}}';

        return array(
            array(
                array(
                    'type' => 'email',
                    'customer_id' => 4,
                    'message' => array(
                        'direction' => 'in',
                        'body' => 'example',
                        'subject' => 'the subject',
                    ),
                ),
                array('body' => "#^$body$#")
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterInvalid()
    {
        $validMessage = array(
            'direction' => 'in',
            'body' => 'example',
            'subject' => 'the subject',
        );

        return array(
            array(
                array(
                    'type' => 'foo',
                    'customer_id' => 4,
                    'message' => $validMessage,
                ),
            ),
            array(
                array(
                    'type' => 'email',
                    'message' => $validMessage,
                ),
            ),
            array(
                array(
                    'type' => 'email',
                    'customer_id' => 9,
                    'message' => array(
                        'direction' => 'foo',
                        'body' => 'example',
                        'subject' => 'the subject',
                    ),
                ),
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $case)
    {
        $this->assertSame('CaseModel', $case->getStructure()->getName());

        $this->assertNull($case->get('external_id'));
        $this->assertSame('Creating a case via the API', $case->get('subject'));
        $this->assertSame(4, $case->get('priority'));
        $this->assertSame('open', $case->get('status'));
        $this->assertSame('email', $case->get('type'));
        $this->assertSame(array('Spam', 'Ignore'), $case->get('labels'));
        $this->assertInstanceOf('DateTime', $case->get('created_at'));
        $this->assertSame(1373570905, $case->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $case->get('updated_at'));
        $this->assertSame(1373570905, $case->get('updated_at')->getTimestamp());
        $this->assertNull($case->get('active_at'));
        $this->assertInstanceOf('DateTime', $case->get('received_at'));
        $this->assertSame(1335994728, $case->get('received_at')->getTimestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('customer', 'ShowCustomer', array('id' => 1)),
            array('assigned_user', 'ShowUser', array('id' => 1)),
            array('assigned_group', 'ShowGroup', array('id' => 1)),
        );
    }
}
