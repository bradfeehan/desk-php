<?php

namespace Desk\Client;

use BradFeehan\GuzzleModularServiceDescriptions\ServiceDescriptionLoader as ModularServiceDescriptionLoader;

/**
 * A service description loader that also merges the contents of each
 * operations' "data" array, when parameters extend each other.
 *
 * Guzzle's default service description loader only merges the contents
 * of each operation's "parameters" property, but this one applies a
 * similar recursive merging behaviour to the "data" property.
 */
class ServiceDescriptionLoader extends ModularServiceDescriptionLoader
{
    /**
     * {@inheritdoc}
     */
    protected function build($config, array $options)
    {
        $description = $this->parentBuild($config, $options);

        // merge parameters of models which have been extended
        foreach ($description->getModels() as $model) {
            if (!isset($model->extends) || !$model->getProperties()) {
                continue;
            }

            $parent = $description->getModel($model->extends);
            if (!$parent || !$parent->getProperties()) {
                continue;
            }

            foreach ($parent->getProperties() as $property) {
                if (!$model->getProperty($property->getName())) {
                    $model->addProperty($property);
                }
            }
        }

        return $description;
    }

    /**
     * Calls the parent class' build() function
     *
     * @param array $config
     * @param array $options
     *
     * @return \Guzzle\Service\Description\ServiceDescription
     */
    public function parentBuild($config, array $options)
    {
        return parent::build($config, $options);
    }

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
