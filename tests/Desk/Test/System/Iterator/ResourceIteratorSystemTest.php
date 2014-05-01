<?php

namespace Desk\Test\System\Iterator;

use Desk\Test\Helper\SystemTestCase;

/**
 * @coversNothing
 * @group system
 */
class ResourceIteratorSystemTest extends SystemTestCase
{

    public function testSystem()
    {
        $client = $this->getServiceBuilder()->get('mock');
        $this->setMockResponse($client, array('1', '2', '3', '4', '5'));

        $articleIterator = $client->getIterator(
            'ListArticles',
            array('per_page' => 2)
        );

        $index = 0;
        foreach ($articleIterator as $article) {
            // They should be Desk model objects
            $modelClass = 'Desk\\Relationship\\Resource\\Model';
            $this->assertInstanceOf($modelClass, $article);

            // Their subjects should be Article 1, Article 2, etc.
            $this->assertSame('Article ' . ++$index, $article->get('subject'));
        }

        // Assert that we retrieved all 10 articles from all five pages
        $this->assertSame(10, $index);
        $this->assertSame(10, $articleIterator->count());
        $this->assertSame(5, $articleIterator->getRequestCount());
    }
}
