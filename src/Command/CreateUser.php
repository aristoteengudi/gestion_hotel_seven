<?php


namespace App\Command;

use App\Model\Users;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CreateUser extends Command
{

    protected static $defaultName = 'app:create-user';


    protected function configure()
    {
        $this
            ->setDescription('Create new user')
            ->setHelp('This command is for create user for test.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo 'Begin...'.PHP_EOL;


        $user = new Users();
        $user->username = 'admin';
        $user->name = 'admin';
        $user->first_name = 'admin';
        $user->phone_number = '111111';
        $user->email = 'admin@admin.com';
        $user->status = '10';
        $user->password_hash = '123456';

        if ($user->CreateUser()){
            $output->writeln('message : finished');
        }else{
            $output->writeln('message : failed');

        }

        return 0;
    }
}