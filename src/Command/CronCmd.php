<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command as BaseCommand;
use SuperCronManager\CronManager;

/**
 * 执行定时任务命令
 * php hunter cron
 */
class CronCmd extends BaseCommand {
    protected function configure() {
       $this->setName('cron')
            ->setDescription('commands.cron.description')
            ->addArgument(
                'param',
                InputArgument::OPTIONAL,
                'cron param'
            );
    }

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

        // crontab格式解析
        $manager->taskInterval('每个小时的1,3,5分钟时运行一次', '1,3,5 * * *', function(){
            echo "每个小时的1,3,5分钟时运行一次\n";
        });

        $manager->taskInterval('每1分钟运行一次', '*/1 * * *', function(){
            echo "每1分钟运行一次\n";
        });

        $manager->taskInterval('每天凌晨运行', '0 0 * *', function(){
            echo "每天凌晨运行\n";
        });

        $manager->taskInterval('每秒运行一次', 's@1', function(){
            echo "每秒运行一次\n";
        });

        $manager->taskInterval('每分钟运行一次', 'i@1', function(){
            echo "每分钟运行一次\n";
        });

        $manager->taskInterval('每小时钟运行一次', 'h@1', function(){
            echo "每小时运行一次\n";
        });

        $manager->taskInterval('指定每天00:00点运行', 'at@00:00', function(){
            echo "指定每天00:00点运行\n";
        });

        $manager->run();
    }

}
