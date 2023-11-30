# LaunchDarkly Server-Side SDK for PHP - DynamoDB integration

[![Run CI](https://github.com/launchdarkly/php-server-sdk-dynamodb/actions/workflows/ci.yml/badge.svg)](https://github.com/launchdarkly/php-server-sdk-dynamodb/actions/workflows/ci.yml)
[![Packagist](https://img.shields.io/packagist/v/launchdarkly/server-sdk-dynamodb.svg?style=flat-square)](https://packagist.org/packages/launchdarkly/server-sdk-dynamodb)
[![Documentation](https://img.shields.io/static/v1?label=GitHub+Pages&message=API+reference&color=00add8)](https://launchdarkly.github.io/php-server-sdk-dynamodb)

This library provides a DynamoDB-backed data source for the [LaunchDarkly PHP SDK](https://github.com/launchdarkly/php-server-sdk), replacing the default behavior of querying the LaunchDarkly service endpoints. The underlying DynamoDB client implementation is the [AWS SDK for PHP](https://aws.amazon.com/sdk-for-php/).

The minimum version of the LaunchDarkly PHP SDK for use with this library is 4.0.0. In earlier versions of the SDK, the DynamoDB integration was bundled in the main SDK package.

The minimum PHP version is 7.3.

For more information, see [our SDK documentation](https://docs.launchdarkly.com/sdk/features/storing-data).

## Quick setup

This assumes that you have already installed the LaunchDarkly PHP SDK in your project.

1. Install this package with `composer`:

```shell
php composer.phar install launchdarkly/server-sdk-dynamodb --save
```

If your project does not already have a dependency on `aws/aws-sdk-php`, that package will be loaded automatically as a dependency of `launchdarkly/server-sdk-dynamodb`.

3. In your SDK configuration code, configure the DynamoDB integration:

```php
    $fr = LaunchDarkly\Integrations\DynamoDB::featureRequester([
        "dynamodb_table" => "my-table-name"
    ]);
    $config = [ "feature_requester" => $fr ];
    $client = new LDClient("sdk_key", $config);
```

By default, the store will use whatever AWS credentials are present in your local AWS configuration. You may specify an alternate configuration as described in the API documentation for `Consul::featureRequester`. Make sure the `dynamodb_table` option corresponds to the table name that is being used by the Relay Proxy. If the Relay Proxy is also configured to use a key prefix for DynamoDB data, you must set the `dynamodb_prefix` option to that value.

## About LaunchDarkly

* LaunchDarkly is a continuous delivery platform that provides feature flags as a service and allows developers to iterate quickly and safely. We allow you to easily flag your features and manage them from the LaunchDarkly dashboard.  With LaunchDarkly, you can:
    * Roll out a new feature to a subset of your users (like a group of users who opt-in to a beta tester group), gathering feedback and bug reports from real-world use cases.
    * Gradually roll out a feature to an increasing percentage of users, and track the effect that the feature has on key metrics (for instance, how likely is a user to complete a purchase if they have feature A versus feature B?).
    * Turn off a feature that you realize is causing performance problems in production, without needing to re-deploy, or even restart the application with a changed configuration file.
    * Grant access to certain features based on user attributes, like payment plan (eg: users on the ‘gold’ plan get access to more features than users in the ‘silver’ plan). Disable parts of your application to facilitate maintenance, without taking everything offline.
* LaunchDarkly provides feature flag SDKs for a wide variety of languages and technologies. Check out [our documentation](https://docs.launchdarkly.com/docs) for a complete list.
* Explore LaunchDarkly
    * [launchdarkly.com](https://www.launchdarkly.com/ "LaunchDarkly Main Website") for more information
    * [docs.launchdarkly.com](https://docs.launchdarkly.com/  "LaunchDarkly Documentation") for our documentation and SDK reference guides
    * [apidocs.launchdarkly.com](https://apidocs.launchdarkly.com/  "LaunchDarkly API Documentation") for our API documentation
    * [blog.launchdarkly.com](https://blog.launchdarkly.com/  "LaunchDarkly Blog Documentation") for the latest product updates
