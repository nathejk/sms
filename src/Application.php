<?php
namespace Nathejk\Sms;

class Application extends \Silex\Application
{
    public function boot()
    {
        $this['time'] = function() {return time();};
        //$this['console.input'] = new \Symfony\Component\Console\Input\ArgvInput();
        $this['console.output'] = new \Symfony\Component\Console\Output\ConsoleOutput();

        $this->registerRoutes();
        $this->registerServices();
        parent::boot();
    }

    protected function registerRoutes()
    {
        $this->get('/', Controller::class . '::indexAction');
        $this->get('/sms', Controller::class . '::smsMockAction');
    }

    protected function registerServices()
    {
        $app = $this;

        $this['message.repo'] = $this->share(function () use ($app) { return new Repository($app); });

        $this['sms'] = $this->share(function () use ($app) { return new SmsService($app); });
        $this['sms.dsn'] = getenv('SMS_DSN');

        $this->register(new MessageQueueServiceProvider, ['mq.dsn' => getenv('MQ_DSN')]);
        $this->register(new \JsonSchemaValidation\SilexServiceProvider, ['jsonschemas' => [
            'message' => __DIR__ . '/../schema.json',
        ]]);

        $dsn = parse_url(getenv('DB_DSN'));
        if (isset($dsn['scheme'])) {
            $this->register(
                new \Silex\Provider\DoctrineServiceProvider(),
                ['dbs.options' => [
                    'default' => [
                        'driver'        => 'pdo_' . $dsn['scheme'],
                        'host'          => $dsn['host'],
                        'dbname'        => substr($dsn['path'], 1),
                        'user'          => $dsn['user'],
                        'password'      => $dsn['pass'],
                        'charset'       => 'utf8',
                        // Do not silently truncate strings/numbers that are too big.
                        'driverOptions' => [\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode = "STRICT_ALL_TABLES"'],
                    ],
                ]]
            );
        }
    }

    /**
     * Ensure that database connection and Postfix connection are not dead.
     *
     * This is necessary for long-running jobs.
     */
    public function pingConnections()
    {
        $db = $this['db'];
        if ($db->isConnected() && !$db->ping()) {
            $db->close();
        }
    }
}
