<?php

namespace Desk\Client;

use Guzzle\Service\Description\ServiceDescriptionLoader as GuzzleServiceDescriptionLoader;

/**
 * A service description loader that also merges the contents of each
 * operations' "data" array, when parameters extend each other.
 *
 * Guzzle's default service description loader only merges the contents
 * of each operation's "parameters" property, but this one applies a
 * similar recursive merging behaviour to the "data" property.
 */
class ServiceDescriptionLoader extends GuzzleServiceDescriptionLoader
{

    /**
     * {@inheritdoc}
     */
    protected function resolveExtension($name, array &$op, array &$operations)
    {
        // parent method handles "parameters" property
        parent::resolveExtension($name, $op, $operations);

        $resolved = array();
        $original = empty($op['data']) ? false : $op['data'];
        foreach ((array) $op['extends'] as $extendedCommand) {
            $toArray = $operations[$extendedCommand];
            $toArray['data'] = isset($toArray['data']) ? $toArray['data'] : array();
            $resolved = empty($resolved)
                ? $toArray['data']
                : array_merge($resolved, $toArray['data']);

            $op = $op + $toArray;
        }
        $op['data'] = $original ? array_merge_recursive($resolved, $original) : $resolved;
    }
}
