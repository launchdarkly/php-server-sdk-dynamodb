# Change log

All notable changes to the project will be documented in this file. This project adheres to [Semantic Versioning](http://semver.org).

## [1.3.0](https://github.com/launchdarkly/php-server-sdk-dynamodb/compare/1.2.0...1.3.0) (2024-07-31)


### Features

* Support using fully configured DynamoDbClient via new `dynamodb_client` option ([#20](https://github.com/launchdarkly/php-server-sdk-dynamodb/issues/20)) ([ccb1380](https://github.com/launchdarkly/php-server-sdk-dynamodb/commit/ccb1380ea59291e1f64f48d71cdaccc49fe25212))


### Miscellaneous Chores

* Add test covering direct DynamoDbClient injection ([#22](https://github.com/launchdarkly/php-server-sdk-dynamodb/issues/22)) ([4f43ee8](https://github.com/launchdarkly/php-server-sdk-dynamodb/commit/4f43ee85a5d326ea05a752f9438fcefa233f0cab))

## [1.2.0] - 2023-10-25
### Changed:
- Expanded SDK version support to v6

## [1.1.0] - 2022-12-28
### Changed:
- Relaxed the SDK version dependency constraint to allow this package to work with the upcoming v5.0.0 release of the LaunchDarkly PHP SDK.

## [1.0.0] - 2021-08-06
Initial release, for use with version 4.x of the LaunchDarkly PHP SDK.
