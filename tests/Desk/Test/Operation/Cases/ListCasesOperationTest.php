<?php

namespace Desk\Test\Operation\Cases;

use Desk\Test\Helper\Operation\ListOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListCasesOperationTest extends ListOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListCases';
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

        $welcomeHistory = $welcome->getLink('history');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $welcomeHistory);
        $this->assertSame('ListCaseHistory', $welcomeHistory->getName());
        $this->assertSame(1, $welcomeHistory->get('case_id'));


        $help = $cases[1];
        $this->assertSame('Help Please!', $help->get('subject'));
        $this->assertSame(5, $help->get('priority'));
        $this->assertSame('new', $help->get('status'));
        $this->assertSame(array('Spam', 'Test'), $help->get('labels'));

        $helpSelf = $help->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $helpSelf);
        $this->assertSame('ShowCase', $helpSelf->getName());
        $this->assertSame(2, $helpSelf->get('id'));

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

        $helpHistory = $help->getLink('history');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $helpHistory);
        $this->assertSame('ListCaseHistory', $helpHistory->getName());
        $this->assertSame(2, $helpHistory->get('case_id'));
    }
}
