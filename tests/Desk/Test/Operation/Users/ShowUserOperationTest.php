<?php

namespace Desk\Test\Operation\Users;

use Desk\Relationship\Model;
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
            array(array('id' => 4), array('url' => '#/users/4$#')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $user)
    {
        $this->assertSame('UserModel', $user->getStructure()->getName());

        $this->assertSame('John Doe', $user->get('name'));
        $this->assertSame('John Doe', $user->get('public_name'));
        $this->assertSame('john@acme.com', $user->get('email'));
        $this->assertSame('agent', $user->get('level'));
    }
}
