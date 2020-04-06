<?php

namespace Dusterio\PlainSqs\Sqs;

use Aws\Sqs\SqsClient;
use Illuminate\Support\Arr;
use Illuminate\Queue\Connectors\SqsConnector;
use Illuminate\Queue\Jobs\SqsJob;
use App\Services\Enelogic\EnelogicDataReceiver;

class Connector extends SqsConnector
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        $config = $this->getDefaultConfiguration($config);

        $dataReceiver = new EnelogicDataReceiver();
        $awsCredentials = $dataReceiver->getAWSCredentials();
        
        $config['credentials']['key'] = $awsCredentials['awsCredentials']['accessKeyId'];
        $config['credentials']['secret'] = $awsCredentials['awsCredentials']['secretAccessKey'];
        $config['credentials']['token'] = $awsCredentials['awsCredentials']['sessionToken'];

        $queue = new Queue(
            new SqsClient($config),
            $config['queue'],
            Arr::get($config, 'prefix', '')
        );
        
        return $queue;
    }
}
