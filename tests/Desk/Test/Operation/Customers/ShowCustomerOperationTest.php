<?php

namespace Desk\Test\Operation\Customers;

use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowCustomerOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowCustomer';
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(array('id' => 4), array('url' => '#/customers/4$#')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testSystem()
    {
        $client = $this->client();
        $command = $client->getCommand(
            $this->getOperationName(),
            array('id' => 1)
        );

        $this->setMockResponse($client, 'success');
        $user = $command->execute();

        // Result of the ShowUser command should be a UserModel model
        $this->assertInstanceOf('Desk\\Relationship\\Model', $user);
        $this->assertSame('CustomerModel', $user->getStructure()->getName());

        // Check model properties
        $this->assertSame('John', $user->get('first_name'));
        $this->assertSame('Doe', $user->get('last_name'));
        $this->assertSame('ACME, Inc', $user->get('company'));
        $this->assertSame('Senior Ninja', $user->get('title'));

        // Get a link to the "self" link in the response
        $self = $user->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $self);

        // Check the URL for the request is the same as the response
        $url = $self->prepare()->getUrl();
        $this->assertSame('http://mock.localhost/customers/1', $url);
    }
}
