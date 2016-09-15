<?php
/**
 * Copyright 2015 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * For instructions on how to run the full sample:
 *
 * @see https://github.com/GoogleCloudPlatform/php-docs-samples/tree/master/pubsub/README.md
 */

namespace Google\Cloud\Samples\Pubsub;

# [START get_subscription_policy]
use Google\Cloud\ServiceBuilder;

/**
 * Prints the policy for a PubSub subscription.
 *
 * @param string $projectId  The Google project ID.
 * @param string $subscriptionName  The Pub/Sub subscription name.
 */
function get_subscription_policy($projectId, $subscriptionName)
{
    $builder = new ServiceBuilder([
        'projectId' => $projectId,
    ]);
    $pubsub = $builder->pubsub();
    $subscription = $pubsub->subscription($subscriptionName);
    $policy = $subscription->iam()->policy();
    print_r($policy);
}
# [END get_subscription_policy]