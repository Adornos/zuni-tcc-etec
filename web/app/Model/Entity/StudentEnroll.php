<?php

namespace App\Model\Entity;

use \App\Db\Database;
use Exception;

class StudentEnroll {

    /**
     * ID da solicitação de matrícula (PK)
     * @var int
     */
    public $id_solicitacao;

    /**
     * Nome completo do aluno
     * @var string
     */
    public $nome_aluno;

    /**
     * Data de nascimento do aluno
     * @var string
     */
    public $data_nascimento;

    /**
     * Sexo do aluno (M, F, O)
     * @var string
     */
    public $sexo_aluno;

    /**
     * ID da série (FK)
     * @var int
     */
    public $id_serie;


    /* =========================
       RESPONSÁVEL 1
    ========================= */

    /**
     * Nome do responsável 1
     * @var string
     */
    public $resp1_nome;

    /**
     * Telefone do responsável 1
     * @var string
     */
    public $resp1_tel;

    /**
     * CPF do responsável 1
     * @var string
     */
    public $resp1_cpf;

    /**
     * Email do responsável 1
     * @var string
     */
    public $resp1_email;

    /**
     * Sexo do responsável 1
     * @var string
     */
    public $resp1_sexo;

    /**
     * Parentesco do responsável 1 com o aluno
     * @var string
     */
    public $resp1_parentesco;


    /* =========================
       ENDEREÇO
    ========================= */

    /**
     * Logradouro do aluno
     * @var string
     */
    public $logradouro;

    /**
     * Número do endereço
     * @var string
     */
    public $numero;

    /**
     * Bairro
     * @var string
     */
    public $bairro;

    /**
     * Cidade
     * @var string
     */
    public $cidade;

    /**
     * Estado (UF)
     * @var string
     */
    public $estado;


    /* =========================
       INFORMAÇÕES MÉDICAS
    ========================= */

    /**
     * Neurodivergência (0/1)
     * @var int
     */
    public $neurodivergencia;

    /**
     * Alergias (0/1)
     * @var int
     */
    public $alergia;

    /**
     * Restrição alimentar (0/1)
     * @var int
     */
    public $restricao_alimentar;

    /**
     * Cuidados especiais (0/1)
     * @var int
     */
    public $cuidados_especiais;

    /**
     * Descrição livre
     * @var string
     */
    public $descricao;


    /* =========================
       RESPONSÁVEL 2 (OPCIONAL)
    ========================= */

    public $resp2_nome;
    public $resp2_tel;
    public $resp2_cpf;
    public $resp2_email;
    public $resp2_sexo;
    public $resp2_parentesco;


    /**
     * Retorna as séries cadastradas no banco
     *
     * @return array [id => nome]
     */
    public static function getSeries(){
        return (new Database("serie"))
            ->select()
            ->fetchAll(\PDO::FETCH_KEY_PAIR);
    }


    /**
     * Preenche os atributos da classe com dados do formulário
     *
     * @param array $values [campo => valor]
     * @return void
     */
    public function hydratate($values = []){

        $this->nome_aluno = $values['nome_aluno'];
        $this->data_nascimento = $values['data_nascimento'];
        $this->sexo_aluno = $values['sexo_aluno'];
        $this->id_serie = $values['id_serie'];

        $this->resp1_nome = $values['resp1_nome'];
        $this->resp1_tel = $values['resp1_tel'];
        $this->resp1_cpf = $values['resp1_cpf'];
        $this->resp1_email = $values['resp1_email'];
        $this->resp1_sexo = $values['resp1_sexo'];
        $this->resp1_parentesco = $values['resp1_parentesco'];

        $this->logradouro = $values['logradouro'];
        $this->numero = $values['numero'];
        $this->bairro = $values['bairro'];
        $this->cidade = $values['cidade'];
        $this->estado = $values['estado'];

        $this->neurodivergencia = $values['neurodivergencia'];
        $this->alergia = $values['alergia'];
        $this->restricao_alimentar = $values['restricao_alimentar'];
        $this->cuidados_especiais = $values['cuidados_especiais'];
        $this->descricao = $values['descricao'];

        if(isset($values['toggleResp2'])){
            $this->resp2_nome = $values['resp2_nome'];
            $this->resp2_tel = $values['resp2_tel'];
            $this->resp2_cpf = $values['resp2_cpf'];
            $this->resp2_email = $values['resp2_email'];
            $this->resp2_sexo = $values['resp2_sexo'];
            $this->resp2_parentesco = $values['resp2_parentesco'];
        }
    }


    /**
     * Cadastra uma nova solicitação de matrícula
     *
     * @return bool
     * @throws Exception
     */
    public function cadastrar() {

        $db = new Database();
        $conn = $db->getConnection();

        try{

            $conn->beginTransaction();

            /* =========================
               MATRÍCULA
            ========================= */
            $id_solicitacao = (new Database('solicitacao_matricula'))->insert([
                'nome_aluno' => $this->nome_aluno,
                'data_nascimento' => $this->data_nascimento,
                'sexo_aluno' => $this->sexo_aluno,
                'id_serie' => $this->id_serie,

                'logradouro' => $this->logradouro,
                'numero' => $this->numero,
                'bairro' => $this->bairro,
                'cidade' => $this->cidade,
                'estado' => $this->estado,

                'neurodivergencia' => $this->neurodivergencia,
                'alergia' => $this->alergia,
                'restricao_alimentar' => $this->restricao_alimentar,
                'cuidados_especiais' => $this->cuidados_especiais,
                'descricao' => $this->descricao
            ]);

            /* =========================
               RESPONSÁVEL 1
            ========================= */
            $id_responsavel1 = (new Database('solicitacao_responsavel'))->insert([
                'nome' => $this->resp1_nome,
                'telefone' => $this->resp1_tel,
                'email' => $this->resp1_email,
                'cpf' => $this->resp1_cpf,
                'sexo' => $this->resp1_sexo,
            ]);

            (new Database('relacao_responsavel_matricula'))->insert([
                'id_solicitacao' => $id_solicitacao,
                'id_responsavel' => $id_responsavel1,
                'parentesco'=> $this->resp1_parentesco
            ]);

            /* =========================
               RESPONSÁVEL 2 (OPCIONAL)
            ========================= */
            if (!empty($this->resp2_nome)) {

                $id_responsavel2 = (new Database('solicitacao_responsavel'))->insert([
                    'nome' => $this->resp2_nome,
                    'telefone' => $this->resp2_tel,
                    'email' => $this->resp2_email,
                    'cpf' => $this->resp2_cpf,
                    'sexo' => $this->resp2_sexo,
                ]);

                (new Database('relacao_responsavel_matricula'))->insert([
                    'id_solicitacao' => $id_solicitacao,
                    'id_responsavel' => $id_responsavel2,
                    'parentesco'=> $this->resp2_parentesco
                ]);
            }

            $conn->commit();

            $this->id_solicitacao = $id_solicitacao;

            return true;

        } catch(Exception $e){

            $conn->rollBack();
            throw $e;
        }
    }
}