<?php
namespace Nathejk\Sms;

use Silex\Application as SilexApp;
use Silex\ServiceProviderInterface;

class MessageQueueServiceProvider implements ServiceProviderInterface
{
    public function register(SilexApp $app)
    {
        $app['mq.client'] = $app->share(function () use ($app) {
            extract(parse_url($app['mq.dsn']));
            $options = new \Nats\ConnectionOptions();
            $client = new \Nats\Connection($options->setHost($host)->setPort($port));
            $client->connect();
            $client->setStreamTimeout(300);
            return $client;
        });
        $app['mq'] = $app->share(function () use ($app) { return new MessageQueueService($app); });
    }

    public function boot(SilexApp $app)
    {
    }
}
