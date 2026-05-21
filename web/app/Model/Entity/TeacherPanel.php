<?php

namespace App\Model\Entity;

use \App\Db\Database;
use Exception;

class TeacherPanel {

    // Atributos do professor    
    /**
     * idProfessor  @var int
     * nome         @var string
     * email        @var string
     * telefone     @var string
     * cpf          @var string
     * sexo         @var string
     * curriculo    @var string
     */

    private $idProfessor;
    private $nome;
    private $email;
    private $telefone;
    private $cpf;
    private $sexo;
    private $curriculo;



    // Infomações do painel
    /**
     * proximaSala        @var string
     * proximaReuniao     @var string
     * creditos           @var int
     * novasMensagens     @var int
     * rendimentoPorTurma @var array
     * horarios           @var array
     */

    private $proximaSala;
    private $proximaReuniao;
    private $creditos;
    private $novasMensagens;
    private $rendimentoPorTurma = [];
    private $horarios;



    // Programação / Agenda
    /**
     * programação        @var array
     */

    private $programação = [];



    // Mural de avisos
    /**
     * muralAvisos        @var array
     */

    private $muralAvisos = [];



    // Chat
    /**
     * chat              @var array
     */

    private $chat = [];



    // Turmas e alunos
    /**
     * turmas            @var array
     */

    private $turmas = [];


    
    /**
     * Metodo que requisita as informações do professor para visualização no painel Perfil
     *
     * @param  int $idProfessor
     * 
     */

    private function getDadosPerfil ($idProfessor){

        $data = (new Database('professor'))->select('idProfessor = '. $idProfessor)->fetchObject(self::class);
        
        self::idProfessor = $data['idProfessor'];
        self::nome = $data['nome'];
        self::email = $data['email'];
        self::telefone = $data['telefone'];
        self::cpf = $data['cpf'];
        self::sexo = $data['sexo'];
        self::curriculo = $data['curriculo'];

    }

        

}