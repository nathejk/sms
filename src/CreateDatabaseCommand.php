<?php
namespace Nathejk\Sms;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDatabaseCommand extends Command
{
    protected $app;

    public function __construct(Application $app)
    {
        parent::__construct();
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('database:create')
            ->addArgument('user', InputArgument::REQUIRED, 'Database superuser')
            ->addArgument('pass', InputArgument::OPTIONAL, 'Database superuser password')
            ->setDescription('Creates database and user needed by this app');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $un = $input->getArgument('user');
        $pw = $input->getArgument('pass');

        $dsn = parse_url(getenv('DB_DSN'));
        $port = isset($dsn['port']) ? $dsn['port'] : 3306;
        if (!isset($dsn['user'], $dsn['pass'], $dsn['path'])) {
            die('specify DB_DSN');
        }
        $dbname = substr($dsn['path'], 1);
        //$user = $dsn['user'

        $sqls = [
            "CREATE DATABASE IF NOT EXISTS `$dbname`",
            "GRANT ALL ON `$dbname`.* to '{$dsn['user']}' identified by '{$dsn['pass']}'",
            "FLUSH PRIVILEGES",
        ];
        try {
            $db = new \PDO("{$dsn['scheme']}:host={$dsn['host']};port={$port}", $un, $pw);
            foreach ($sqls as $sql) {
                $ok = $db->exec($sql);
            }
        } catch (\PDOException $e) {
            $output->writeln("<error>\n  {$e->getMessage()}  \n</>");
        }
        $output->writeln('done');
    }
}
