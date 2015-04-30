<?php

namespace Desk\Test\Operation\Cases\History;

use Guzzle\Service\Resource\Model;
use Desk\Test\Helper\Operation\ListSubOperationTestCase;

class ListCaseHistoryOperationTest extends ListSubOperationTestCase
{

    /**
     * Contains assertions to make about the results of the system test
     *
     * @param Model[] $history Resulting models from system test
     */
    protected function assertSystem(array $history)
    {
        foreach ($history as $event) {
            $this->assertEquals('CaseHistoryModel', $event->getStructure()->getName());
        }

        $this->assertCount(3, $history);

        $event = $history[0];
        $this->assertEquals('rule_applied', $event->get('type'));
        $this->assertEquals('5540291e4d7d0a4d540006a5', $event->get('context'));
        $this->assertInstanceOf('\DateTime', $event->get('created_at'));

        $event = $history[1];
        $this->assertEquals('case_updated', $event->get('type'));
        $this->assertEquals('5540291e4d7d0a4d540006a5', $event->get('context'));
        $this->assertInstanceOf('\DateTime', $event->get('created_at'));

        $event = $history[2];
        $this->assertEquals('rule_applied', $event->get('type'));
        $this->assertEquals('5540291e4d7d0a4d540006a5', $event->get('context'));
        $this->assertInstanceOf('\DateTime', $event->get('created_at'));
    }

    /**
     * Gets the name of the property containing the main object's ID
     *
     * For example, if the operation is ListArticleTranslation, this
     * function should return "article_id", as the "main object" is
     * the article (which is having its translations listed).
     *
     * @return string
     */
    protected function getIdProperty()
    {
        return 'case_id';
    }

    /**
     * The name of the operation to be tested
     *
     * This should be one of the keys under "operation" in the client's
     * service description.
     *
     * @return string
     */
    protected function getOperationName()
    {
        return 'ListCaseHistory';
    }
}
