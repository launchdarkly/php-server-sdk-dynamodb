<?php
namespace LaunchDarkly\Impl\Integrations;

use Aws\DynamoDb\DynamoDbClient;

/**
 * @internal
 */
class DynamoDbFeatureRequester extends FeatureRequesterBase
{
    /** @var string */
    protected $_tableName;
    /** @var string */
    protected $_prefix;
    /** @var DynamoDbClient */
    protected $_client;

    public function __construct(string $baseUri, string $sdkKey, array $options)
    {
        parent::__construct($baseUri, $sdkKey, $options);

        if (!isset($options['dynamodb_table'])) {
            throw new \InvalidArgumentException('dynamodb_table must be specified');
        }
        $this->_tableName = $options['dynamodb_table'];

        $dynamoDbOptions = $options['dynamodb_options'] ?? [];
        $dynamoDbOptions['version'] = '2012-08-10'; // in the AWS SDK for PHP, this is how you specify the API version
        $client = $options['dynamodb_client'] ?? null;
        $this->_client = $client instanceof DynamoDbClient ? $client : new DynamoDbClient($dynamoDbOptions);

        $prefix = $options['dynamodb_prefix'] ?? '';
        $this->_prefix = ($prefix != null && $prefix != '') ? ($prefix . ':') : '';
    }

    protected function readItemString(string $namespace, string $key): ?string
    {
        $request = array(
            'TableName' => $this->_tableName,
            'ConsistentRead' => true,
            'Key' => array(
                'namespace' => array('S' => $this->_prefix . $namespace),
                'key' => array('S' => $key)
            )
        );
        $result = $this->_client->getItem($request);
        if (!$result) {
            return null;
        }
        $item = $result->get('Item');
        if (!$item || !isset($item['item'])) {
            return null;
        }
        $attr = $item['item'];
        return $attr['S'] ?? null;
    }

    protected function readItemStringList(string $namespace): ?array
    {
        $items = array();
        $request = array(
            'TableName' => $this->_tableName,
            'ConsistentRead' => true,
            'KeyConditions' => array(
                'namespace' => array(
                    'ComparisonOperator' => 'EQ',
                    'AttributeValueList' => array(array('S' => $this->_prefix . $namespace))
                )
            )
        );
        // We may need to repeat this query several times due to pagination
        $moreItems = true;
        while ($moreItems) {
            $result = $this->_client->query($request);
            foreach ($result->get('Items') as $item) {
                if (isset($item['item'])) {
                    $attr = $item['item'];
                    if (isset($attr['S'])) {
                        $items[] = $attr['S'];
                    }
                }
            }
            if (isset($result['LastEvaluatedKey']) && $result['LastEvaluatedKey']) {
                $request['ExclusiveStartKey'] = $result['LastEvaluatedKey'];
            } else {
                $moreItems = false;
            }
        }
        return $items;
    }
}
