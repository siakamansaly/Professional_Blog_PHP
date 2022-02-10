<?php

namespace Blog\Controllers;

use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller
{
    private $contactModel;
    protected $modelName = \Models\Contact::class;
    private $data = [];
    public $request;


    public function __construct()
    {
        $this->contactModel = new \Blog\Models\Contact;
        $this->request = Request::createFromGlobals();
    }

    /**
     * Check contact form and send message
     * 
     * @return void
     */
    public function contact()
    {
        $this->data = [];
        $json=[];
        $message="";
        $success="";
        if (empty($this->request->request->all())) {
            $this->redirect('/#contact');
        }
        $this->data['firstName'] = $this->sanitize($this->request->request->get('prenom'));
        $this->data['lastName'] = $this->sanitize($this->request->request->get('name'));
        $this->data['email'] = $this->sanitize($this->request->request->get('email'));
        $this->data['message'] = $this->sanitize($this->request->request->get('message'));

        $this->data['subject'] = $_ENV['TITLE_WEBSITE'] . ' - Formulaire de contact';
        $message = $this->div_alert("Message envoyé avec succès !","success");
        $success=$this->sendMessage($this->data);
        $json['success']=$success;
        $json['message']=$message;
        echo json_encode($json);
        
    }
}
