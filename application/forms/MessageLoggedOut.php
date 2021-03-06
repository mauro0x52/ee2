<?php
/**
 * MessageLoggedOut.php - Ee_Form_MessageLoggedOut
 * Formulário de nova mensagem para um usuário descadastrado
 * 
 * @author Lucas Gaspar (via Mauro Ribeiro)
 * @since 2012-02-23
 */
class Ee_Form_MessageLoggedOut extends Ee_Form_Form
{
    /**
     * Usuário que está recebendo a mensagem
     * @var Ee_Model_Data_User 
     */
    private $to_user;

    /**
     * Construtor da porra toda
     * @param array $options 
     * @param Ee_Model_Data_User $options['to_user']    usuário que está recebendo a mensagem
     */
    public function __construct($options = null) {
        $this->to_user = isset($options['to_user']) ? $options['to_user'] : null;
        parent::__construct();
    }

    /**
     * Constrói o formulário
     */
    public function init()
    {

        $this->setName('send-message');
        $this->setAction('messages/logged-out-write');

        // importa os filtros da empreendemia
        $this->addElementPrefixPath(
                'Ee_Filter',
                APPLICATION_PATH.'/../library/filters/',
                'filter'
        );
        
        // cria honeypots
        $hps = $this->honeyPots();
        
        // para quem a mensagem vai ser mandada
        $to_user_id = new Zend_Form_Element_Hidden('to_user_id');
        $to_user_id
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->setDecorators(array('ViewHelper'));
        if ($this->to_user) $to_user_id->setValue($this->to_user->id);
        $this->addElement($to_user_id);

        // adiciona dois honeypots
        $this->addElement($hps[0]);
        $this->addElement($hps[1]);

        // nome do usuário (deslogado)
        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Nome')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addFilter('MrWilsonTracesKiller', array('title'))
            ->addValidator('NotEmpty')
            ->setAttrib('maxlength', 100)
            ->setAttrib('class', array('tip_tool_form'))
            ->setAttrib('title', 'Qual o seu nome?')
            ->addErrorMessage('preencha seu nome');
        $this->addElement($name);
        
        // e-mail do usuário (deslogado)
        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('E-mail')
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addFilter('StringToLower')
              ->addValidator('EmailAddress', true, array('messages'=>array(
                  'emailAddressInvalid'=>'digite um e-mail válido'
               )))
              ->setAttrib('maxlength', 50)
              ->setAttrib('class', array('tip_tool_form'))
              ->setAttrib('title', 'Qual seu e-mail?');
        $this->addElement($email);
        
        // título da mensagem
        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Assunto')
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addFilter('MrWilsonTracesKiller', array('title'))
              ->addValidator('NotEmpty')
              ->setAttrib('maxlength', 250)
              ->setAttrib('class', array('tip_tool_form'))
              ->setAttrib('title', 'Sobre o que é esta mensagem?')
              ->addErrorMessage('escreva um assunto');
        $this->addElement($title);

        // corpo da mensagem
        $body = new Zend_Form_Element_Textarea('body');
        $body->setLabel('Mensagem')
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addFilter('MrWilsonTracesKiller', array('text'))
              ->addValidator('NotEmpty')
              ->setAttrib('class', array('wordcount'))
              ->addErrorMessage('mensagem não pode ficar em branco');
        $this->addElement($body);
        
        // adiciona um honeypot
        $this->addElement($hps[2]);

        // botão de submit
        $submit = new Zend_Form_Element_Submit('Enviar mensagem');
        $submit
            ->setLabel('Enviar mensagem');
        $this->addElement($submit);
    }
    
    /**
     * Pega os valores do formulário
     * @return object
     * @return int $object->message->to_user_id     id do usuário recebendo a mensagem
     * @return string $object->message->body        copor da mensagem
     * @return string $object->message->title       título da mensagem
     */
    public function getModels() {
        $values = $this->getValues();

        $models->message->to_user_id = $values['to_user_id'];
        $models->message->name = $values['name']; // Caso de usuário deslogado
        $models->message->email = $values['email']; // Caso de usuário deslogado
        $models->message->body = $values['body'];
        $models->message->title = $values['title'];
        
        return $models;
    }
    

}

