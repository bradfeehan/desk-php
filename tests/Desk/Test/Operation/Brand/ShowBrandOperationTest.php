<?php

namespace Desk\Test\Operation\Brand;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowBrandOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowBrand';
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(array('id' => 4), array('url' => '#/brands/4$#')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $article)
    {
        $this->assertSame('BrandModel', $article->getStructure()->getName());

        $this->assertSame('Brand Name', $article->get('name'));
        $this->assertInstanceOf('DateTime', $article->get('created_at'));
        $this->assertSame(1407967854, $article->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $article->get('updated_at'));
        $this->assertSame(1407967854, $article->get('updated_at')->getTimestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('articles', 'ListBrandArticles', array('brand_id' => 1)),
        );
    }
}
