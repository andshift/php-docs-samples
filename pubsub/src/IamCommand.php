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

namespace Google\Cloud\Samples\Pubsub;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Output\OutputInterface;
use InvalidArgumentException;

/**
 * Command line utility to manage Pub/Sub IAM.
 *
 * Usage: php pubsub.php iam
 */
class IAMCommand extends Command
{
    use ProjectIdTrait;

    protected function configure()
    {
        $this
            ->setName('iam')
            ->setDescription('Browse a BigQuery table')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command outputs the rows of a BigQuery table.

    <info>php %command.full_name% DATASET.TABLE</info>

EOF
            )
            ->addOption(
                'project',
                null,
                InputOption::VALUE_REQUIRED,
                'The Google Cloud Platform project name to use for this invocation. ' .
                'If omitted then the current gcloud project is assumed. '
            )
            ->addOption(
                'topic',
                null,
                InputOption::VALUE_REQUIRED,
                'The topic name. '
            )
            ->addOption(
                'subscription',
                null,
                InputOption::VALUE_REQUIRED,
                'The subscription name. '
            )
            ->addOption(
                'create',
                null,
                InputOption::VALUE_REQUIRED,
                'Create the IAM for the supplied user email. '
            )
            ->addOption(
                'test',
                null,
                InputOption::VALUE_NONE,
                'Test the IAM policy. '
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$projectId = $input->getOption('project')) {
            $projectId = $this->getProjectIdFromGcloud();
        }
        $topicName = $input->getOption('topic');
        $subscriptionName = $input->getOption('topic');
        if ($topicName) {
            if ($userEmail = $input->getOption('create')) {
                set_topic_policy($projectId, $topicName, $userEmail);
            } elseif ($input->getOption('test')) {
                test_topic_permissions($projectId, $topicName);
            } else {
                get_topic_policy($projectId, $topicName);
            }
        } elseif ($subscriptionName) {
            if ($userEmail = $input->getOption('create')) {
                set_subscription_policy($projectId, $subscriptionName, $userEmail);
            } elseif ($input->getOption('test')) {
                test_subscription_permissions($projectId, $subscriptionName);
            } else {
                get_subscription_policy($projectId, $subscriptionName);
            }
        } else {
            throw new \Exception('Must provide "--topic", or "--subscription"');
        }
    }
}
