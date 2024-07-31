<?php
namespace LaunchDarkly\Integrations;

use LaunchDarkly\Impl\Integrations\DynamoDbFeatureRequester;

class DynamoDb
{
    /**
     * Configures an adapter for reading feature flag data from DynamoDB.
     *
     * After calling this method, store its return value in the `feature_requester` property of your client configuration:
     *
     *     $fr = LaunchDarkly\Integrations\DynamoDb::featureRequester([ 'dynamodb_table' => 'my-table' ]);
     *     $config = [ 'feature_requester' => $fr ];
     *     $client = new LDClient('sdk_key', $config);
     *
     * Or if you already have a client instance:
     *
     *     $dynamoClient = new Aws\DynamoDb\DynamoDbClient($settings);
     *     $fr = LaunchDarkly\Integrations\DynamoDb::featureRequester([ 'dynamo_client' => $dynamoClient ]);
     *     $config = [ 'feature_requester' => $fr ];
     *     $client = new LDClient('sdk_key', $config);
     *
     * For more about using LaunchDarkly with databases, see the
     * [SDK reference guide](https://docs.launchdarkly.com/v2.0/docs/using-a-persistent-feature-store).
     *
     * @param array $options  Configuration settings (can also be passed in the main client configuration):
     *   - `dynamodb_table`: (required) name of an existing table in DynamoDB.
     *   - `dynamodb_options`: can include any settings supported by the AWS SDK client
     *   - `dynamodb_prefix`: a string to be prepended to all database keys; corresponds to the prefix
     * setting in ld-relay
     *   - `dynamodb_client`: an already-configured DynamoDb client instance if you wish to reuse one; if
     * specified, this will cause all other options except `dynamodb_prefix` and `dynamodb_table` to be ignored
     *   - `apc_expiration`: expiration time in seconds for local caching, if `APCu` is installed
     * @return \Closure  an object to be stored in the `feature_requester` configuration property
     */
    public static function featureRequester(array $options = [])
    {
        return function ($baseUri, $sdkKey, $baseOptions) use ($options) {
            return new DynamoDbFeatureRequester($baseUri, $sdkKey, array_merge($baseOptions, $options));
        };
    }
}
