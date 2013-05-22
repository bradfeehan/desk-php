<?php

namespace Desk\Test\Operation\Users;

use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowUserOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowUser';
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(array('id' => 3), array('url' => '#/users/3$#')),
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
        $this->assertSame('UserModel', $user->getStructure()->getName());

        // Check model properties
        $this->assertSame('John Doe', $user->get('name'));
        $this->assertSame('John Doe', $user->get('public_name'));
        $this->assertSame('john@acme.com', $user->get('email'));
        $this->assertSame('agent', $user->get('level'));

        // Get a link to the "self" link in the response
        $self = $user->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $self);

        // Check the URL for the request is the same as the response
        $url = $self->prepare()->getUrl();
        $this->assertSame('http://mock.localhost/users/1', $url);
    }
}
