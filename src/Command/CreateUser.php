<?php


namespace App\Command;

use App\Classes\config;
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

        try{



            foreach (config::getConfig()['TEST_USER'] as $user)

            $users = new Users();
            $users->username = $user['username'];
            $users->name = $user['name'];
            $users->first_name = $user['first_name'];
            $users->phone_number = $user['phone_number'];
            $users->email = $user['email'];
            $users->status = $user['status'];
            $users->password_hash = $user['password_hash'];
            $users->CreateUser();

            $output->writeln('message : finished');

        }catch (\Exception $exception){
            $output->writeln("message : {$exception->getMessage()}");

        }

        return 0;
    }
}