<?php

namespace API\deliver_teste\Model;

use API\deliver_teste\Model\deliverModel;

class servicoTesteModel extends deliverModel 
{    

    public function teste()
    {
        $this->cachedQuery = false;
        
        $this->strSqlQuery = "SELECT * FROM MsgBoletosBancarios";
        // $this->strSqlQuery = "SELECT * FROM dbo.Cidade";

        $this->executeSqlServer();
        
        return $this->getResultSet();

    }

    public function incluirCorredor($dados)
    {
        $this->cachedQuery = false;
        
        $this->strSqlQuery = "INSERT INTO pessoa (nome,cpf,data_nascimento,data_criacao,data_alteracao) VALUES ('".$dados->nome."','".$dados->cpf."','".$dados->data_nascimento."',SYSDATE(),SYSDATE())";
        
        $this->execute();
        
        $this->strSqlQuery = "INSERT INTO corredor (id_pessoa,data_criacao,data_alteracao) VALUES((select ifnull((select max(p.id_pessoa) from pessoa p),1)),SYSDATE(),SYSDATE())";
        
        $this->execute();
        
        return $this->getResultSet();
    }
    
    public function incluirProva($dados)
    {
        $this->cachedQuery = false;

        $data = strtr($dados->data_prova, '/', '-');
        
        $dataProva = date('Y-m-d h:i:s', strtotime($data));
        
        $this->strSqlQuery = "INSERT INTO prova (tipo_prova,data_prova,data_criacao,data_alteracao) VALUES('".$dados->tipo_prova."','".$dataProva."',SYSDATE(),SYSDATE())";
        
        $this->execute();
        
        return $this->getResultSet();
    }
    
    public function incluirProvaCorredor($dados)
    {
        $this->cachedQuery = false;
        
        $this->strSqlQuery = " INSERT INTO prova_corredor (id_prova,id_corredor,data_criacao,data_alteracao) VALUES($dados->id_prova,$dados->id_corredor,SYSDATE(),SYSDATE())";
        
        $this->execute();
        
        return $this->getResultSet();
    }
    
    public function incluirResultados($dados)
    {
        $this->cachedQuery = false;
        
        $this->strSqlQuery = "INSERT INTO resultados (id_prova_corredor,hora_inicio_prova,hora_conclusao_prova,data_criacao,data_alteracao) VALUES(".$dados->id_prova_corredor.",'".$dados->hora_inicio_prova."','".$dados->hora_conclusao_prova."',SYSDATE(),SYSDATE())";
        
        $this->execute();
        
        return $this->getResultSet();
    }

    public function retornaProvasCorredor($dados)
    {
        $this->cachedQuery = false;

		$this->strSqlQuery = "SELECT *
                                FROM prova_corredor pc
                                WHERE pc.id_corredor = ".$dados->id_corredor."
                                AND pc.id_prova = (SELECT id_prova FROM prova WHERE data_prova = (SELECT data_prova FROM prova WHERE id_prova = ".$dados->id_prova."))";
		$this->execute();
		return $this->getResultSet();
    }
    
    public function retornaIdadeCorredor($dados)
    {
        $this->cachedQuery = false;

		$this->strSqlQuery = "SELECT (YEAR(SYSDATE()) - YEAR(data_nascimento)) AS idade

                                FROM corredor c
                                    INNER JOIN pessoa p ON c.id_pessoa = p.id_pessoa
                                    
                                WHERE id_corredor =".$dados->id_corredor;
		$this->execute();
		return $this->getResultSet();
    }
    
    public function listarClassificacao()
    {
        $this->cachedQuery = false;

		$this->strSqlQuery = "SELECT pc.id_prova,
                                    p.tipo_prova,
                                    pc.id_corredor,
                                    (YEAR(SYSDATE()) - YEAR(pes.data_nascimento)) AS idade,
                                    pes.nome,
                                    timediff(r.hora_conclusao_prova,r.hora_inicio_prova) AS classificacao
                            FROM resultados r
                                    INNER JOIN prova_corredor pc ON pc.id_prova_corredor = r.id_prova_corredor
                                    INNER JOIN prova p ON p.id_prova = pc.id_prova
                                    INNER JOIN corredor c ON c.id_corredor = pc.id_corredor
                                    INNER JOIN pessoa pes ON pes.id_pessoa = c.id_pessoa

                            ORDER BY id_prova,classificacao ASC";
		$this->execute();
		return $this->getResultSet();
    }

    public function listarTiposProva()
    {
        $this->cachedQuery = false;
    
        $this->strSqlQuery = "SELECT tipo_prova FROM prova";
        $this->execute();
        return $this->getResultSet();

    }
}