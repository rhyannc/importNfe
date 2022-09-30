<link rel="stylesheet" href="style.css">
<div class="form">

    <form name="env" method="post" enctype="multipart/form-data">
    <?php
        require __DIR__ . "/../src/ImportNfe.php";
        require __DIR__ . "/../src/Xml.php";
        require __DIR__ . "/conexao.php";


    $xml = new AwaCode\ImportNfe\Xml("uploads", "xmlnfe", "imports", "error", false); //("importados", "xmlnfe");

    //NORMALIZA O ARRAY PARA ENVIAR PARA CADASTRO
    if ($_FILES)
    {
          $nfe = $_FILES["xml"];
          for ($i = 0; $i < count($nfe["type"]); $i++)
         {
            foreach (array_keys($nfe) as $keys)
             {
                $nfeFiles[$i][$keys] = $nfe[$keys][$i];
              }
         }

         //CORRE PELO ARRAY NORMALIZADO E FAZ O UPLOAD
         foreach ($nfeFiles as $file)
         {
            $upload = $xml->upload($file);
           //  var_dump( $file);
         }

        //PEGA O ARRAY DE TODOS OS ARQUIVOS ENVIADO NA PASTA TEMPORARIA

        $post = $xml->gravaarquivo();

        // GRAVA NO BD
      if (!empty($post->items)):
        foreach ($post->items as $notas)
        {
            //VERIFICA SE JA FOI IMPORTADA ANTES
            $chnotes = $conn->prepare("SELECT * FROM tbl_notas WHERE chave = :CHAVE");
            $chnotes->bindParam(":CHAVE", $notas['chave']);
            $chnotes->execute();
            $check = $chnotes->rowCount();

            if ($check == 0 )
            {
                $stmt = $conn->prepare("INSERT INTO tbl_notas ( 
                                                                          numero, chave, protocolo, natop, serie, tpnf, emit_nome, emit_cnpj_cpf, 
                                                                           emit_endereco, emit_numero, emit_bairro, emit_municipio, emit_uf, emit_cep, emit_comp, 
                                                                          emit_fone, dest_nome, dest_cnpj_cpf, vl_produto, vl_frete, vl_total) 
                                                                                   VALUES ( 
                                                                          :NUMERO, :CHAVE, :PROTOCOLO, :NATOP, :SERIE, :TPNF, :EMNOME, :EMCPCN,
                                                                          :EMEND, :EMNUM, :EMBAI, :EMMUN, :EMUF, :EMCEP, :EMCOMP,
                                                                          :EMPHONE, :DESTNOME, :DESTCPCN, :VLP, :VLF, :VLT)");
                $stmt->bindParam(":NUMERO", $notas['cNF']);
                $stmt->bindParam(":CHAVE", $notas['chave']);
                $stmt->bindParam(":PROTOCOLO", $notas['nProt']);
                $stmt->bindParam(":NATOP", $notas['natOp']);
                $stmt->bindParam(":SERIE", $notas['serie']);
                $stmt->bindParam(":TPNF", $notas['tpNF']);
                $stmt->bindParam(":EMNOME", $notas['emit_xNome']);
                $stmt->bindParam(":EMCPCN", $notas['emit_cpf_cnpj']);

                $stmt->bindParam(":EMEND", $notas['emit_xLgr']);
                $stmt->bindParam(":EMNUM", $notas['emit_nro']);
                $stmt->bindParam(":EMCOMP", $notas['emit_comp']);
                $stmt->bindParam(":EMBAI", $notas['emit_xBairro']);
                $stmt->bindParam(":EMMUN", $notas['emit_xMun']);
                $stmt->bindParam(":EMUF", $notas['emit_UF']);
                $stmt->bindParam(":EMCEP", $notas['emit_CEP']);

                $stmt->bindParam(":EMPHONE", $notas['emit_fone']);
                $stmt->bindParam(":DESTNOME", $notas['dest_xNome']);
                $stmt->bindParam(":DESTCPCN", $notas['dest_cpf_cnpj']);
                $stmt->bindParam(":VLP", $notas['vProd']);
                $stmt->bindParam(":VLF", $notas['vFrete']);
                $stmt->bindParam(":VLT", $notas['vNF']);
                $stmt->execute();
                $idNota = $conn->lastInsertId();
            }


          foreach ($notas['itens'] as $iten)
           {
               $stmt = $conn->prepare("INSERT INTO tbl_itens_nota ( 
                                                                          id_nota, codigo, nome, codigo_barra, ncm, cfop, un_medida, qtd, vl_unidade, vl_total) 
                                                                                   VALUES ( 
                                                                          :IDNOTA, :CODIGO, :NOME, :BARRAS, :NCM, :CFOP, :UNM, :QTD, :VLUN, :VLTOTAL)");
               $stmt->bindParam(":IDNOTA", $idNota);
               $stmt->bindParam(":CODIGO", $iten['codigo']);
               $stmt->bindParam(":NOME", $iten['xProd']);
               $stmt->bindParam(":BARRAS", $iten['cean']);
               $stmt->bindParam(":NCM", $iten['NCM']);
               $stmt->bindParam(":CFOP", $iten['CFOP']);
               $stmt->bindParam(":UNM", $iten['uCom']);
               $stmt->bindParam(":QTD", $iten['indTot']);
               $stmt->bindParam(":VLUN", $iten['vUnCom']);
               $stmt->bindParam(":VLTOTAL", $iten['vProd']);
               $stmt->execute();
           } // endforeach;


        }
      endif;







    }


    ?>



        <h1>Importar XML da NFe</h1>
        <input type="file" multiple="multiple" name="xml[]" id="xml[]" required/>
        <button>Enviar Nfe</button>

    </form>
</div>