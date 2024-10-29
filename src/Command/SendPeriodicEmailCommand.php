<?php

namespace App\Command;

use Carbon\Carbon;
use Exception;
use Pimcore;
use Pimcore\Model\DataObject;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class SendPeriodicEmailCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try{
            $emailsToSend = $this->getListOfEmailsToSend();
            $mailingList = $this->getMailingList();
            $this->sendEmails($emailsToSend, $mailingList, $output);
            return Command::SUCCESS;    
        } catch(Exception $e){
            $output->writeln("error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    protected function configure(): void
    {
        $this->setDescription('Checks if there are emails to send. If there are, sends them!')->setName('email:sendScheduledEmails');
    }


    /**
     * Get all the emails that must be sent
     * @return array 
     */
    private function getListOfEmailsToSend(){
        /**
         * @var DataObject\MailingListEmail $listing
         * @var Carbon\Carbon $today
         * @var float $todayInMillis
         */
        $listing = new DataObject\MailingListEmail\Listing();
        $today = Carbon::now();
        $todayInMillis = $today->valueOf();
        $listing->setCondition('UNIX_TIMESTAMP(expire)*1000 < ? AND (sent IS NULL OR sent<>1)', [$todayInMillis]);
        return $listing->load();
    }

    /**
     * Get a list of string of all the users subscribed to the mailing list
     * @return array
     */
    private function getMailingList(){
        /**
         * @var DataObject\EmailAddress\Listing() $listing
        */
        $listing = new DataObject\EmailAddress\Listing();
        $listing->setCondition('unscribed IS NULL OR unscribed <> 1');
        return $listing->load();
    }

    /**
     * @param array $emailsToSend
     * @param array $mailingList
     */
    private function sendEmails($emailsToSend, $mailingList, $output){
        foreach($emailsToSend as $emailData){
            $mail = new Pimcore\Mail();
            $mail->text($emailData->getBody());
            $mail->from('no-reply@localhost');
            $output->writeln("here works 1");
            foreach($mailingList as $to){
                $mail->addBcc($to->getEmailAddress());
            }
            $output->writeln("here works 2");
            $mail->send();
            $output->writeln("here works 3");
        }
        $output->writeln("here works 4");
    }

}
