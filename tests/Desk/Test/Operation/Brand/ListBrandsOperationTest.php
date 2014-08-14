<?php

namespace Desk\Test\Operation\Brand;

use Desk\Test\Helper\Operation\ListOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListBrandsOperationTest extends ListOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListBrands';
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(array $brands)
    {
        foreach ($brands as $brand) {
            $this->assertSame('BrandModel', $brand->getStructure()->getName());
        }

        $this->assertSame(2, count($brands));


        $one = $brands[0];
        $this->assertSame('Brand One', $one->get('name'));

        $oneSelf = $one->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $oneSelf);
        $this->assertSame('ShowBrand', $oneSelf->getName());
        $this->assertSame(1, $oneSelf->get('id'));

        $oneArticles = $one->getLink('articles');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $oneArticles);
        $this->assertSame('ListBrandArticles', $oneArticles->getName());
        $this->assertSame(1, $oneArticles->get('brand_id'));


        $two = $brands[1];
        $this->assertSame('Brand Two', $two->get('name'));

        $twoSelf = $two->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $twoSelf);
        $this->assertSame('ShowBrand', $twoSelf->getName());
        $this->assertSame(2, $twoSelf->get('id'));

        $twoArticles = $two->getLink('articles');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $twoArticles);
        $this->assertSame('ListBrandArticles', $twoArticles->getName());
        $this->assertSame(2, $twoArticles->get('brand_id'));
    }
}
