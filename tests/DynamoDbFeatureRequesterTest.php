<?php

namespace LaunchDarkly\Impl\Integrations\Tests;

use Aws\DynamoDb\DynamoDbClient;
use LaunchDarkly\Integrations\DynamoDb;
use LaunchDarkly\SharedTest\DatabaseFeatureRequesterTestBase;
use Psr\Log\NullLogger;

class DynamoDbFeatureRequesterTest extends DatabaseFeatureRequesterTestBase
{
    const TABLE_NAME = 'test-table';

    private static $dynamoDbClient;

    public static function setUpBeforeClass(): void
    {
        self::$dynamoDbClient = new DynamoDbClient(self::makeDynamoDbOptions());
        self::createTableIfNecessary();
    }

    private static function realPrefix(?string $prefix): string
    {
        if ($prefix === null || $prefix === '') {
            return '';
        }
        return $prefix . ':';
    }

    private static function makeDynamoDbOptions()
    {
        return array(
            'credentials' => array('key' => 'x', 'secret' => 'x'), // credentials for local test instance are arbitrary
            'endpoint' => 'http://localhost:8000',
            'region' => 'us-east-1',
            'version' => '2012-08-10'
        );
    }

    protected function makeRequester($prefix)
    {
        $options = array(
            'dynamodb_table' => self::TABLE_NAME,
            'dynamodb_options' => self::makeDynamoDbOptions(),
            'dynamodb_prefix' => $prefix,
            'logger' => new NullLogger()
        );
        $factory = DynamoDb::featureRequester();
        return $factory('', '', $options);
    }

    protected function putSerializedItem($prefix, $namespace, $key, $version, $json): void
    {
        $prefixedNamespace = self::realPrefix($prefix) . $namespace;
        self::$dynamoDbClient->putItem(array(
            'TableName' => self::TABLE_NAME,
            'Item' => array(
                'namespace' => array('S' => $prefixedNamespace),
                'key' => array('S' => $key),
                'version' => array('N' => strval($version)),
                'item' => array('S' =>  $json)
            )
        ));
    }

    protected function clearExistingData($prefix): void
    {
        $p = self::realPrefix($prefix);
        $result = self::$dynamoDbClient->scan(array(
            'TableName' => self::TABLE_NAME,
            'ConsistentRead' => true,
            'AttributesToGet' => array('namespace', 'key')
        ));
        $requests = array();
        foreach ($result['Items'] as $item) {
            $requests[] = array(
                'DeleteRequest' => array('Key' => $item)
            );
        }
        if (count($requests)) {
            self::$dynamoDbClient->batchWriteItem(array(
                'RequestItems' => array(
                    self::TABLE_NAME => $requests
                )
            ));
        }
    }

    private static function createTableIfNecessary()
    {
        try {
            self::$dynamoDbClient->describeTable(array('TableName' => self::TABLE_NAME));
            return; // table already exists
        } catch (\Exception $e) {
        }
        while (true) {
            // We may need to retry this because in the CI build, the local DynamoDB may not have finished starting yet.
            try {
                self::$dynamoDbClient->createTable(array(
                    'TableName' => self::TABLE_NAME,
                    'AttributeDefinitions' => array(
                        array(
                            'AttributeName' => 'namespace',
                            'AttributeType' => 'S'
                        ),
                        array(
                            'AttributeName' => 'key',
                            'AttributeType' => 'S'
                        )
                    ),
                    'KeySchema' => array(
                        array(
                            'AttributeName' => 'namespace',
                            'KeyType' => 'HASH'
                        ),
                        array(
                            'AttributeName' => 'key',
                            'KeyType' => 'RANGE'
                        )
                    ),
                    'ProvisionedThroughput' => array(
                        'ReadCapacityUnits' => 1,
                        'WriteCapacityUnits' => 1
                    )
                ));
                break;
            } catch (\Exception $e) {
                sleep(1);
            }
        }
        while (true) { // table may not be available immediately
            try {
                self::$dynamoDbClient->describeTable(array('TableName' => self::TABLE_NAME));
                return;
            } catch (\Exception $e) {
            }
            sleep(1);
        }
    }
}
