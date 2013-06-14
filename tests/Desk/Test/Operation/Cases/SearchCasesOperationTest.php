<?php

namespace Desk\Test\Operation\Cases;

use DateTime;
use DateTimeZone;
use Desk\Test\Helper\Operation\ListOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class SearchCasesOperationTest extends ListOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'SearchCases';
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        $date = new DateTime('2013-06-14 13:00:00', new DateTimeZone('UTC'));

        return array(
            array(
                array('name' => 'test!'),
                array('query' => '#^name=test%21$#'),
            ),
            array(
                array('first_name' => 'FirstName'),
                array('query' => '#^first_name=FirstName$#'),
            ),
            array(
                array('last_name' => array('LastName1', 'LastName2')),
                array('query' => '#^last_name=LastName1,LastName2$#'),
            ),
            array(
                array('email' => 'foo@example.com'),
                array('query' => '#^email=foo%40example.com$#'),
            ),
            array(
                array('phone' => '123 456'),
                array('query' => '#^phone=123%20456$#'),
            ),
            array(
                array('company' => array('the company', 'other company')),
                array('query' => '#^company=the%20company,other%20company$#'),
            ),
            array(
                array('twitter' => 'foobar'),
                array('query' => '#^twitter=foobar$#'),
            ),
            array(
                array('labels' => 'label1'),
                array('query' => '#^labels=label1$#'),
            ),
            array(
                array('case_id' => array(3, 4, 5)),
                array('query' => '#^case_id=3,4,5$#'),
            ),
            array(
                array('subject' => 'subject1'),
                array('query' => '#^subject=subject1$#'),
            ),
            array(
                array('description' => 'my description'),
                array('query' => '#^description=my%20description$#'),
            ),
            array(
                array('status' => 'new'),
                array('query' => '#^status=new$#'),
            ),
            array(
                array('priority' => 6),
                array('query' => '#^priority=6#'),
            ),
            array(
                array('assigned_group' => 'admins'),
                array('query' => '#^assigned_group=admins$#'),
            ),
            array(
                array('assigned_user' => 'joe'),
                array('query' => '#^assigned_user=joe$#'),
            ),
            array(
                array('channels' => array('email', 'twitter')),
                array('query' => '#^channels=email,twitter$#'),
            ),
            array(
                array('notes' => 'test'),
                array('query' => '#^notes=test$#'),
            ),
            array(
                array('attachments' => 'foobar.jpg'),
                array('query' => '#^attachments=foobar.jpg$#'),
            ),
            array(
                array('created' => 'today'),
                array('query' => '#^created=today$#'),
            ),
            array(
                array('updated' => 'week'),
                array('query' => '#^updated=week$#'),
            ),
            array(
                array('since_created_at' => $date),
                array('query' => '#^since_created_at=1371214800$#'),
            ),
            array(
                array('max_created_at' => $date),
                array('query' => '#^max_created_at=1371214800$#'),
            ),
            array(
                array('since_updated_at' => $date),
                array('query' => '#^since_updated_at=1371214800$#'),
            ),
            array(
                array('max_updated_at' => $date),
                array('query' => '#^max_updated_at=1371214800$#'),
            ),
            array(
                array('since_id' => 7),
                array('query' => '#^since_id=7$#'),
            ),
            array(
                array('max_id' => 8),
                array('query' => '#^max_id=8$#'),
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterInvalidAdditional()
    {
        return array(
            array(array('name' => true)),
            array(array('first_name' => array(true))),
            array(array('last_name' => array('abc', new \stdClass()))),
            array(array('email' => -44.3)),
            array(array('phone' => false)),
            array(array('company' => 0.123)),
            array(array('twitter' => true)),
            array(array('labels' => false)),
            array(array('case_id' => array(3.3, 4, 5))),
            array(array('subject' => 3.45)),
            array(array('description' => true)),
            array(array('status' => 'old')),
            array(array('priority' => 6.1)),
            array(array('assigned_group' => false)),
            array(array('assigned_user' => true)),
            array(array('channels' => array('carrier pidgeon'))),
            array(array('notes' => 0.3)),
            array(array('attachments' => false)),
            array(array('created' => 'tomorrow')),
            array(array('updated' => 'invalid')),
            array(array('since_created_at' => '123')),
            array(array('max_created_at' => 234)),
            array(array('since_updated_at' => 34.5)),
            array(array('max_updated_at' => true)),
            array(array('since_id' => -2)),
            array(array('max_id' => 56.3)),
        );
    }

    /**
     * {@inheritdoc}
     *
     * Overridden to set parameters to match the mock response.
     */
    protected function getSystemTestCommand()
    {
        $command = parent::getSystemTestCommand();
        $command->set('subject', 'please help');
        $command->set('name', array('jimmy'));
        return $command;
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(array $cases)
    {
        foreach ($cases as $case) {
            $this->assertSame('CaseModel', $case->getStructure()->getName());
        }

        $this->assertSame(2, count($cases));


        $welcome = $cases[0];
        $this->assertSame('Welcome', $welcome->get('subject'));
        $this->assertSame(5, $welcome->get('priority'));
        $this->assertSame('new', $welcome->get('status'));
        $this->assertSame(array('Spam', 'Test'), $welcome->get('labels'));

        $welcomeSelf = $welcome->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $welcomeSelf);
        $this->assertSame('ShowCase', $welcomeSelf->getName());
        $this->assertSame(1, $welcomeSelf->get('id'));

        $welcomeMessage = $welcome->getLink('message');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $welcomeMessage);
        $this->assertSame('ShowCaseMessage', $welcomeMessage->getName());
        $this->assertSame(1, $welcomeMessage->get('case_id'));

        $welcomeCustomer = $welcome->getLink('customer');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $welcomeCustomer);
        $this->assertSame('ShowCustomer', $welcomeCustomer->getName());
        $this->assertSame(1, $welcomeCustomer->get('id'));

        $welcomeAssignedUser = $welcome->getLink('assigned_user');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $welcomeAssignedUser);
        $this->assertSame('ShowUser', $welcomeAssignedUser->getName());
        $this->assertSame(2, $welcomeAssignedUser->get('id'));

        $welcomeAssignedGroup = $welcome->getLink('assigned_group');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $welcomeAssignedGroup);
        $this->assertSame('ShowGroup', $welcomeAssignedGroup->getName());
        $this->assertSame(1, $welcomeAssignedGroup->get('id'));


        $help = $cases[1];
        $this->assertSame('Help Please!', $help->get('subject'));
        $this->assertSame(5, $help->get('priority'));
        $this->assertSame('new', $help->get('status'));
        $this->assertSame(array('Spam', 'Test'), $help->get('labels'));

        $helpSelf = $help->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $helpSelf);
        $this->assertSame('ShowCase', $helpSelf->getName());
        $this->assertSame(2, $helpSelf->get('id'));

        $helpMessage = $help->getLink('message');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $helpMessage);
        $this->assertSame('ShowCaseMessage', $helpMessage->getName());
        $this->assertSame(2, $helpMessage->get('case_id'));

        $helpCustomer = $help->getLink('customer');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $helpCustomer);
        $this->assertSame('ShowCustomer', $helpCustomer->getName());
        $this->assertSame(1, $helpCustomer->get('id'));

        $helpAssignedUser = $help->getLink('assigned_user');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $helpAssignedUser);
        $this->assertSame('ShowUser', $helpAssignedUser->getName());
        $this->assertSame(2, $helpAssignedUser->get('id'));

        $helpAssignedGroup = $help->getLink('assigned_group');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $helpAssignedGroup);
        $this->assertSame('ShowGroup', $helpAssignedGroup->getName());
        $this->assertSame(1, $helpAssignedGroup->get('id'));
    }
}
