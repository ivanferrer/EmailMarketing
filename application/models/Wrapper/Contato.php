<?php

/*
 * To change this license header; choose License Headers in Project Properties.
 * To change this template file; choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Noticias
 *
 * @author Progiro
 */
class Application_Model_Wrapper_Contato
{

    private $id_contato;    
    private $email;
    private $nome;
    private $status_permissao_envio;
    private $status_envio;
            
    public function getId_contato()
    {
        return $this->id_contato;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function getStatus_permissao_envio()
    {
        return $this->status_permissao_envio;
    }

    public function getStatus_envio()
    {
        return $this->status_envio;
    }

    public function setId_contato($id_contato)
    {
        $this->id_contato = $id_contato;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }

    public function setStatus_permissao_envio($status_permissao_envio)
    {
        $this->status_permissao_envio = $status_permissao_envio;
        return $this;
    }

    public function setStatus_envio($status_envio)
    {
        $this->status_envio = $status_envio;
        return $this;
    }


}
