<?php

namespace App\Controller;

use Exception;
use Pimcore;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\EmailAddress;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApplicationController extends AbstractController
{

    /**
     * @Route("/api/subscribe", name="api_subscribe", methods={"POST"})
     */
    public function subscribe(Request $request): Response
    { 
        /**
         * @var array $data
         * @var string $subscriberEmail 
         * @var DataObject\EmailAddress $newEmail 
        */               
        $data = json_decode($request->getContent(), true);
        if(!isset($data["emailSubscriber"])){
            return $this->json(['status' => 'KO', 'message' => 'An email must be passed!']); 
        }
        $subscriberEmail = $data["emailSubscriber"];

        if($this->emailExists($subscriberEmail)){
            return $this->json(['status' => 'KO', 'message' => 'User already existing!']);
        }

        try{
            $newEmail = $this->createEmailAddress($subscriberEmail);
            $this->sendSubscriptionEmail($newEmail);
            return $this->json(['status' => 'OK', "message" => "user subscribed successfully"]);         
        } catch (Exception $e){
            return $this->json(['status' => 'KO', 'message' => 'There is an error:' . $e->getMessage()]);
        }
    }

    /**
     * Send the subscription email. Very basic
     */
    function sendSubscriptionEmail($email){
        $mail = new Pimcore\Mail();
        $mail->to($email);
        $mail->text("Registration successful");
        $mail->send();
    }

    /** 
     * @param string $subscriberEmail 
     * @return boolean
    */
    private function emailExists($subscriberEmail){
        //need this code because I need also unpublished emails
        $entries = new DataObject\EmailAddress\Listing();
        $entries->setCondition("emailAddress = ?", ["$subscriberEmail"]);
        $loadedList = $entries->load();
        return sizeof($loadedList)>0;
    }

    /** 
     * @param string $newEmailAddress 
     * @return DataObject\EmailAddress
    */
    private function createEmailAddress($newEmailAddress){
        $newEmail = new DataObject\EmailAddress();
        $newEmail->setEmailAddress($newEmailAddress);
        $newEmail->setKey($newEmailAddress);
        $newEmail->setParentId('29');
        $newEmail->setPublished(true);     
        $newEmail->save();        
        return $newEmail;
    }
}
