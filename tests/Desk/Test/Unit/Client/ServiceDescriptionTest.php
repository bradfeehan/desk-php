<?php

namespace Desk\Test\Unit\Client;

use Desk\Client;
use Desk\Test\Helper\TestCase;
use Seld\JsonLint\JsonParser;

class ServiceDescriptionTest extends TestCase
{

    /**
     * @coversNothing
     */
    public function testLintServiceDescription()
    {
        if (!$this->canServiceDescriptionBeLoaded()) {
            $json = file_get_contents(Client::getDescriptionFilename());

            $parser = new JsonParser();
            $result = $parser->lint($json);

            if ($result) {
                $this->fail($result->getMessage());
            }
        }

        $this->assertTrue(true);
    }
}
