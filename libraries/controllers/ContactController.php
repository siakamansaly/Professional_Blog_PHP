<?php

namespace Blog\Controllers;

class ContactController extends Controller
{
    protected $modelName = \Blog\Models\Contact::class;
    private $data = [];

    public function __construct()
    {
        parent::__construct();
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
        if (empty($this->var->request->all())) {
            $this->redirect('/#contact');
        }
        $this->data['firstName'] = $this->sanitize($this->var->request->get('prenom'));
        $this->data['lastName'] = $this->sanitize($this->var->request->get('name'));
        $this->data['email'] = $this->sanitize($this->var->request->get('email'));
        $this->data['message'] = $this->sanitize($this->var->request->get('message'));

        $this->data['subject'] = EnvironmentController::get('TITLE_WEBSITE') . ' - Formulaire de contact';
        $message = $this->div_alert("Message envoyé avec succès !","success");
        $success=$this->sendMessage($this->data);
        $json['success']=$success;
        $json['message']=$message;
        print_r(json_encode($json));
        
    }
}
