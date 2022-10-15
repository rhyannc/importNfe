<?php

namespace AwaCode\ImportNfe;

use Exception;

/**
 * Class AwaCode Xml
 *
 * @author Rhyann Carvalhais <https://github.com/rhyannc>
 * @package AwaCode\Importnfe
 */
class Xml extends ImportNfe
{
    /**
     * Allow xml,
     * @var array allowed file types
     * https://www.freeformatter.com/mime-types-list.html
     */
    protected static  $allowTypes = [
        "text/xml",
    ];

    /**
     * Extensões permitidas para tipos.
     * @var array
     */
    protected static  $extensions = [
        "xml"
    ];

    /**
     * @param array $xml
     * @param string $name
     * @return string
     * @throws Exception
     */
    public function upload(array $xml): string
    {
        $this->ext($xml);

        if (!in_array($xml['type'], static::$allowTypes) || !in_array($this->ext, static::$extensions)) {
            throw new Exception("Não é um tipo ou extensão XML de NFe válido");
        }

        $this->name($xml["name"]);
        move_uploaded_file($xml['tmp_name'], "{$this->path}/{$this->name}");
        return "{$this->path}/{$this->name}";

    }

    public function gravaarquivo()
    {
        // OSB: SO SALVA NFE EM AMBIENTE DE PRODUÇÃO E COM NUMERO DE  AUTORIZAÇÃO
        //Diretorio onde estao os arquivos
        $directory = dir($this->path);

        //WHILE ENTRA NA PASTA ARQUIVOS E FAZ A VERIFICAÇÃO DE TODOS OS XML LA DENTRO
        $p = 0;
        $nota = array();
        $nota = (object) $nota;

        while(($arquivo = $directory->read()) !== false)
         {
            if (empty($arquivo) || $arquivo == '..' || $arquivo == '.') continue;

            $lexml   =  $this->path .'/'. $arquivo;
            $xml     =  simplexml_load_file($lexml);

            $environ =  $xml->NFe->infNFe->ide->tpAmb;

             if (empty($xml->protNFe->infProt->nProt))
             {
                 echo "<h4>Arquivo sem dados de Protocolo!</h4>";
                 rename($lexml,"./{$this->pathError}/$arquivo");
                 continue;
             }
             if ($environ != 1)  //se diferente de 1 ( 1 = Produção 2 = Homologação )
             {
                 echo "<h4>Documento emitido em ambiente de homologação!</h4>";
                 rename($lexml,"./{$this->pathError}/$arquivo");
                 continue;
             }

             ++$p;
             $nota->items[$p] = $this->mapa( $xml );
             rename($lexml,"./{$this->pathImport}/$arquivo");

         }

        return $nota;
    }


    public function dataus($data)
    {
        return date("Y-m-d", strtotime($data));
    }


    public function mapa($xml)
    {

        $chave = $xml->NFe->infNFe->attributes()->Id;
        $chave = strtr(strtoupper($chave), array("NFE" => NULL));

        //IDE
        @$cUF =         $xml->NFe->infNFe->ide->cUF;    	 //<cUF>41</cUF>  Código do Estado do Fator gerador
        @$cNF =         $xml->NFe->infNFe->ide->cNF;       	 //<cNF>21284519</cNF>   Código número da nfe
        @$natOp =       $xml->NFe->infNFe->ide->natOp;       //<natOp>V E N D A</natOp>  Resumo da Natureza de operação
        @$indPag =      $xml->NFe->infNFe->ide->indPag;      //<indPag>2</indPag> 0 – pagamento à vista; 1 – pagamento à prazo; 2 - outros
        @$mod =         $xml->NFe->infNFe->ide->mod;         //<mod>55</mod> Modelo do documento Fiscal
        @$serie =       $xml->NFe->infNFe->ide->serie;    	 //<serie>2</serie>
        @$nNF =         $xml->NFe->infNFe->ide->nNF;   	     //<nNF>19685</nNF> Número da Nota Fiscal
        @$dhEmi =       $xml->NFe->infNFe->ide->dhEmi;       //<dEmi>2011-09-06</dEmi> Data de emissão da Nota Fiscal
        @$dhEmi =       $this->dataus(@$dhEmi);
        @$dhSaiEnt =    $xml->NFe->infNFe->ide->dhSaiEnt;    //<dSaiEnt>2011-09-06</dSaiEnt> Data de entrada ou saida da Nota Fiscal
        @$tpNF =        $xml->NFe->infNFe->ide->tpNF;        //<tpNF>1</tpNF>  0-entrada / 1-saída
        @$cMunFG =      $xml->NFe->infNFe->ide->cMunFG;      //<cMunFG>4106407</cMunFG> Código do municipio Tabela do IBGE
        @$tpImp =       $xml->NFe->infNFe->ide->tpImp;       //<tpImp>1</tpImp>
        @$tpEmis =      $xml->NFe->infNFe->ide->tpEmis;      //<tpEmis>1</tpEmis>
        @$cDV =         $xml->NFe->infNFe->ide->cDV;         //<cDV>0</cDV>
        $finNFe =       $xml->NFe->infNFe->ide->finNFe;      //<finNFe>1</finNFe>
        $procEmi =      $xml->NFe->infNFe->ide->procEmi;     //<procEmi>0</procEmi>
        $verProc =      $xml->NFe->infNFe->ide->verProc;     //<verProc>2.0.0</verProc>
        @$tpAmb =       $xml->NFe->infNFe->ide->tpAmb;       //<tpAmb>1</tpAmb> 1 = Produção 2 = Homologação.

        //INFPROT
        $xMotivo =      $xml->protNFe->infProt->xMotivo;
        $nProt =        $xml->protNFe->infProt->nProt;

        //EMIT
        $emit_CPF =     $xml->NFe->infNFe->emit->CPF;
        $emit_CNPJ =    $xml->NFe->infNFe->emit->CNPJ;
        $emit_xNome =   $xml->NFe->infNFe->emit->xNome;
        $emit_IE =   $xml->NFe->infNFe->emit->IE;
        $emit_xFant =   $xml->NFe->infNFe->emit->xFant;
        $emit_cpf_cnpj = $emit_CNPJ . $emit_CPF;

        //ENDEREMIT
        $emit_xLgr =    $xml->NFe->infNFe->emit->enderEmit->xLgr;
        $emit_nro=      $xml->NFe->infNFe->emit->enderEmit->nro;
        $emit_comp=      $xml->NFe->infNFe->emit->enderEmit->xCpl;
        $emit_xBairro = $xml->NFe->infNFe->emit->enderEmit->xBairro;
        $emit_cMun =    $xml->NFe->infNFe->emit->enderEmit->cMun;
        $emit_xMun =    $xml->NFe->infNFe->emit->enderEmit->xMun;
        $emit_UF =      $xml->NFe->infNFe->emit->enderEmit->UF;
        $emit_CEP =     $xml->NFe->infNFe->emit->enderEmit->CEP;
        $emit_cPais =   $xml->NFe->infNFe->emit->enderEmit->cPais;
        $emit_xPais =   $xml->NFe->infNFe->emit->enderEmit->xPais;
        $emit_fone =    $xml->NFe->infNFe->emit->enderEmit->fone;

        //DEST
        $dest_CPF   =   $xml->NFe->infNFe->dest->CPF;
        $dest_CNPJ  =   $xml->NFe->infNFe->dest->CNPJ;
        $dest_xNome =   $xml->NFe->infNFe->dest->xNome;
        $dest_xFant =   $xml->NFe->infNFe->dest->xFant;
        $dest_IE    =   $xml->NFe->infNFe->dest->IE;
        $dest_cpf_cnpj = $dest_CNPJ . $dest_CPF;

        //ENDERDEST
        @$dest_xLgr =    $xml->NFe->infNFe->dest->enderDest->xLgr;
        @$dest_nro=      $xml->NFe->infNFe->dest->enderDest->nro;
        @$dest_xBairro = $xml->NFe->infNFe->dest->enderDest->xBairro;
        @$dest_cMun =    $xml->NFe->infNFe->dest->enderDest->cMun;
        @$dest_xMun =    $xml->NFe->infNFe->dest->enderDest->xMun;
        @$dest_UF =      $xml->NFe->infNFe->dest->enderDest->UF;
        @$dest_CEP =     $xml->NFe->infNFe->dest->enderDest->CEP;
        @$dest_cPais =   $xml->NFe->infNFe->dest->enderDest->cPais;
        @$dest_xPais =   $xml->NFe->infNFe->dest->enderDest->xPais;
        @$dest_fone =    $xml->NFe->infNFe->dest->enderDest->fone;

        //TRANSP
        $transp_mod   = $xml->NFe->infNFe->transp->modFrete;
        $transp_CPF   = $xml->NFe->infNFe->transp->transporta->CPF;
        $transp_CNPJ  = $xml->NFe->infNFe->transp->transporta->CNPJ;
        $transp_xNome = $xml->NFe->infNFe->transp->transporta->xNome;
        $transp_IE    = $xml->NFe->infNFe->transp->transporta->IE;
        $transp_cpf_cnpj = $transp_CNPJ .$transp_CPF;

        //VOL
        $transp_qVol  = $xml->NFe->infNFe->transp->vol->qVol;
        $transp_esp   = $xml->NFe->infNFe->transp->vol->esp;
        $transp_marca = $xml->NFe->infNFe->transp->vol->marca;

        //VEICTRANSP
        $transp_placa = $xml->NFe->infNFe->transp->veicTransp->placa;
        $transp_UF =    $xml->NFe->infNFe->transp->veicTransp->UF;

        //TOTAL
        $vBC =          $xml->NFe->infNFe->total->ICMSTot->vBC;
        $vICMS =        $xml->NFe->infNFe->total->ICMSTot->vICMS;
        $vBCST =        $xml->NFe->infNFe->total->ICMSTot->vBCST;
        $vST =          $xml->NFe->infNFe->total->ICMSTot->vST;
        $vProd =        $xml->NFe->infNFe->total->ICMSTot->vProd;
        $vNF =          $xml->NFe->infNFe->total->ICMSTot->vNF;
        $vFrete =       $xml->NFe->infNFe->total->ICMSTot->vFrete;
        $vSeg =         $xml->NFe->infNFe->total->ICMSTot->vSeg;
        $vDesc =        $xml->NFe->infNFe->total->ICMSTot->vDesc;
        $vIPI =         $xml->NFe->infNFe->total->ICMSTot->vIPI;
        $vOutro =       $xml->NFe->infNFe->total->ICMSTot->vOutro;

        $dadosnfe = [
                     //IDE
                     "chave"         =>   "$chave",
                     "cUF"           =>   "$cUF",
                     "cNF"           =>   "$cNF",
                     "natOp"         =>   "$natOp",
                     "indPag"       =>    "$indPag",
                     "mod"           =>   "$mod",
                     "serie"         =>   "$serie",
                     "nNF"           =>   "$nNF",
                     "dhEmi"         =>   "$dhEmi",
                     "dhSaiEnt"      =>   "$dhSaiEnt",
                     "tpNF"          =>   "$tpNF",
                     "cMunFG"        =>   "$cMunFG",
                     "tpImp"         =>   "$tpImp",
                     "tpEmis"        =>   "$tpEmis",
                     "cDV"           =>   "$cDV",
                     "finNFe"        =>   "$finNFe",
                     "procEmi"       =>   "$procEmi",
                     "verProc"       =>   "$verProc",
                     "tpAmb"         =>   "$tpAmb",

                     //INFPROT
                     "xMotivo"       =>   "$xMotivo",
                     "nProt"         =>   "$nProt",

                     //EMIT
                     "emit_cpf_cnpj" =>   "$emit_cpf_cnpj",
                     "emit_xNome"    =>   "$emit_xNome",
                     "emit_xFant"    =>   "$emit_xFant",
                     "emit_ie"       =>   "$emit_IE",

                     //ENDEREMIT
                     "emit_xLgr"     =>   "$emit_xLgr",
                     "emit_nro"      =>   "$emit_nro",
                     "emit_comp"     =>   "$emit_comp",
                     "emit_xBairro"  =>   "$emit_xBairro",
                     "emit_cMun"    =>   "$emit_cMun",
                     "emit_xMun"    =>   "$emit_xMun",
                     "emit_UF"      =>   "$emit_UF",
                     "emit_CEP"     =>   "$emit_CEP",
                     "emit_cPais"   =>   "$emit_cPais",
                     "emit_xPais"   =>   "$emit_xPais",
                     "emit_fone"    =>   "$emit_fone",

                     //DEST
                     "dest_cpf_cnpj"  => "$dest_cpf_cnpj",
                     "dest_xNome"   =>   "$dest_xNome",
                     "dest_xFant"   =>   "$dest_xFant",
                     "dest_IE"      =>   "$dest_IE",

                     //ENDERDEST
                     "dest_xLgr"    =>   "$dest_xLgr",
                     "dest_nro"     =>   "$dest_nro",
                     "dest_xBairro" =>   "$dest_xBairro",
                     "dest_cMun"    =>   "$dest_cMun",
                     "dest_xMun"    =>   "$dest_xMun",
                     "dest_UF"      =>   "$dest_UF",
                     "dest_CEP"     =>   "$dest_CEP",
                     "dest_cPais"   =>   "$dest_cPais",
                     "dest_xPais"   =>   "$dest_xPais",
                     "dest_fone"    =>   "$dest_fone",

                     //TRANSP
                     "transp_mod"   =>   "$transp_mod",
                     "transp_cpf_cnpj"   =>   "$transp_cpf_cnpj",
                     "transp_xNome" =>   "$transp_xNome",
                     "transp_IE"    =>   "$transp_IE",

                     //VOL
                     "transp_qVol"  =>   "$transp_qVol",
                     "transp_esp"   =>   "$transp_esp",
                     "transp_marca" =>   "$transp_marca",

                     //VEICTRANSP
                     "transp_placa" =>   "$transp_placa",
                     "transp_UF"    =>   "$transp_UF",

                     //TOTAL
                     "vBC"          =>   "$vBC",
                     "vICMS"        =>   "$vICMS",
                     "vBCST"        =>   "$vBCST",
                     "vST"          =>   "$vST",
                     "vProd"        =>   "$vProd",
                     "vNF"          =>   "$vNF",
                     "vFrete"       =>   "$vFrete",
                     "vSeg"         =>   "$vSeg",
                     "vDesc"        =>   "$vDesc",
                     "vIPI"         =>   "$vIPI",
                     "vOutro"       =>   "$vOutro"
            ];

        $i =0;
        foreach($xml->NFe->infNFe->det as $item)
        {
            ++$i;
            $codigo = $item->prod->cProd;
            $cean = $item->prod->cEAN;
            $xProd = $item->prod->xProd;
            $NCM = $item->prod->NCM;
            $CFOP = $item->prod->CFOP;
            $cbenef = $item->prod->cBenef;
            $uCom = $item->prod->uCom;
            $qCom = $item->prod->qCom;
            $vUnCom = $item->prod->vUnCom;
            $vProd = $item->prod->vProd;
            $indTot = $item->prod->indTot;

            $arr_itens =[
                "codigo"      =>   "$codigo",
                "cean"        =>   "$cean",
                "xProd"       =>   "$xProd",
                "NCM"         =>   "$NCM",
                "CFOP"        =>   "$CFOP",
                "cbenef"      =>   "$cbenef",
                "uCom"        =>   "$uCom",
                "qCom"        =>   "$qCom",
                "vUnCom"      =>   "$vUnCom",
                "vProd"       =>   "$vProd",
                "indTot"      =>   "$indTot"
            ];

            $dadosnfe['itens'][$i]= $arr_itens;
        }

        return ($dadosnfe);
    }
}
