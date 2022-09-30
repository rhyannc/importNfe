# ImportNfe Library @AwaCode

[![Maintainer](http://img.shields.io/badge/maintainer-@rhyannc_-blue.svg?style=flat-square)](https://twitter.com/rhyannc_)
[![Source Code](http://img.shields.io/badge/source-AwaCode/ImportNfe-blue.svg?style=flat-square)](https://github.com/rhyannc/ImportNfe)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/AwaCode/ImportNfe.svg?style=flat-square)](https://packagist.org/packages/AwaCode/ImportNfe)
[![Latest Version](https://img.shields.io/github/release/rhyannc/ImportNfe.svg?style=flat-square)](https://github.com/rhyannc/ImportNfe/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build](https://img.shields.io/scrutinizer/build/g/rhyannc/ImportNfe.svg?style=flat-square)](https://scrutinizer-ci.com/g/rhyannc/ImportNfe)
[![Quality Score](https://img.shields.io/scrutinizer/g/rhyannc/ImportNfe.svg?style=flat-square)](https://scrutinizer-ci.com/g/rhyannc/ImportNfe)
[![Total Downloads](https://img.shields.io/packagist/dt/AwaCode/ImportNfe.svg?style=flat-square)](https://packagist.org/packages/AwaCode/ImportNfe)


ImportNfe é um componente extremamente compacto e fácil de usar. Você só precisa configurar seu comportamento uma vez pelo contrutor, e depois enviar os aquivos xml para fazer a importação e cadastro no Banco de Dados.

###### ImportNfe Library is an extremely compact and easy-to-use component. You only need to configure its behavior once by the constructor, and then send the xml files to make the remittance and register in the Bank.


## Sobre AwaCode

AwaCode é um conjunto de pequenos e otimizados componentes PHP para tarefas comuns. Mantido por Rhyann C. Com eles você executa tarefas rotineiras com poucas linhas, escrevendo menos e fazendo muito mais.

###### AwaCode is a set of small, optimized PHP components for common tasks. Maintained by Rhyan C. With them you can do routine tasks with few lines, writing less and doing a lot more.


### Destaques


- Fácil para configurar e personalizar via ***construtor*** da classe (Easy to configure and customize via constructor class)
- Simples de integra e fazer a importação de suas Notas Fiscais em XML (Simple to integrate and import your Invoices in XML)
- Controle para nao importar Nfe emitidas em Homologação e sem numero de protocolo (Control not to import Nfe issued in Homologation and without protocol number)
- Controle para nao importar Nfe duplicadas. (Control not to import duplicate Nfe.)
- Organização de pasta de Nfe importadas com sucesso e com erros (Nfe folder organization imported successfully and with errors)
- Pronto para o composer e compatível com PSR-2 (Composer ready and PSR-2 compliant) 

## Installation

ImportNfe está disponível via Composer:

```bash
"AwaCode/ImportNfe": "^1.0"
```

or run

```bash
composer require AwaCode/ImportNfe
```

## Documentation

###### For details on how to use the ImportNfe, see the sample folder with details in the component directory

Para mais detalhes sobre como usar o ImportNfe, veja a pasta de exemplo com detalhes no diretório do componente

```php
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
                { /* GRAVA A NOTA NA TABELA */ }
             foreach ($notas['itens'] as $iten)
                { /* GRAVA OS PRODUTOS  DA NOTA NA TABELA DE ITENS */ }
        }      
      endif;

    }
```

##### Result

````html
<form name="env" method="post" enctype="multipart/form-data">

   <h1>Importar XML da NFe</h1>
   <input type="file" multiple="multiple" name="xml[]" id="xml[]" required/>
   <button>Enviar Nfe</button>

</form>
````

## Contributing

Please see [CONTRIBUTING](https://github.com/rhyannc/ImportNfe/blob/master/CONTRIBUTING.md) for details.

## Support

###### Security: If you discover any security related issues, please email rhyannc@hotmail.com.br instead of using the issue tracker.

Se você descobrir algum problema relacionado à segurança, envie um e-mail para rhyannc@hotmail.com.br em vez de usar o rastreador de problemas.

Thank you

## Credits

- [Rhyann C.](https://github.com/rhyannc) (Developer)
- [All Contributors](https://github.com/rhyannc/ImportNfe/contributors) (This Contributors)

## License

The MIT License (MIT). Please see [License File](https://github.com/rhyannc/ImportNfe/blob/master/LICENSE) for more information.