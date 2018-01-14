<?php

namespace Hunter\cron\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Hunter\Core\App\Application;
use SuperCronManager\CronManager;

/**
 * 执行定时任务命令
 * php hunter cron
 */
class CronCmd extends BaseCommand {
    /**
     * @var moduleHandler
     */
    protected $moduleHandler;

    /**
     * CronCommand constructor.
     */
    public function __construct() {
        $application = new Application();
        $this->moduleHandler = $application->boot()->getModuleHandle();
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure() {
       $this->setName('cron')
            ->setDescription('commands.cron.description')
            ->addArgument(
                'param',
                InputArgument::OPTIONAL,
                'cron param'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        //获取参数值
        $param = $input->getArgument('param');

        if ($param && !in_array($param, ['status', 'worker', 'check', 'stop', 'restart'])) {
            return $output->writeln("error `{$param}` just support ['status', 'worker', 'check', 'stop', 'restart']");
        }

        $manager = new CronManager();
        // 守护进程方式启动
        $manager->daemon = true;
        $manager->argv = $param;

        foreach ($this->moduleHandler->getImplementations('cron') as $module) {
          $module_hook_cron = $this->moduleHandler->invoke($module, 'cron');
          $manager->taskInterval($module_hook_cron['name'], $module_hook_cron['command'], $module_hook_cron['callback']);
        }

        $manager->run();
    }

}
