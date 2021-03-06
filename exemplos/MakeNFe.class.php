<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MakeNFe
 *
 * @author administrador
 */
//namespace SpedPHP\Components\Xml;

//use \DOMDocument;
//use \DOMElement;

class MakeNFe
{

    public $errmsg='';
 
    public $versao;
    public $mod;
    
    public $dom; //DOMDocument
    public $NFe; //DOMNode
    public $infNFe; //DOMNode
    public $ide; //DOMNode
    public $NFref; //DOMNode
    public $refNFe; //DOMNode
    public $refNF; //DOMNode
    public $refNFP; //DOMNode
    public $refCTe; //DOMNode
    public $ECFref; //DOMNode
    public $emit; //DOMNode
    public $enderEmit; //DOMNode
    public $dest; //DOMNode
    public $enderDest; //DOMNode
    public $retirada; //DOMNode
    public $aAutXML; //array de DOMNode
    public $aProd; //array de DOMNode
    public $aDet; //array de DOMNode
    public $aDetExport; //array de DOMNode
    public $aDI; //array de DOMNode
    public $aAdi; //array de DOMNode
    
    //cria DOM document
    public function __construct()
    {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;
        $this->dom->preserveWhiteSpace = false;
    }

    public function montaNFe()
    {
        //as tags deven ser montadas e inseridas umas nas outras de dentro para fora
        
        //cria e insere as subtags da tag NFref
        if (isset($this->refNFe)) {
            $this->tagNFref();
            $this->NFeref->appendChild($this->refNFe);
        }
        if (isset($this->refNF)) {
            $this->tagNFref();
            $this->NFeref->appendChild($this->refNF);
        }
        if (isset($this->refNFP)) {
            $this->tagNFref();
            $this->NFeref->appendChild($this->refNFP);
        }
        if (isset($this->refCTE)) {
            $this->tagNFref();
            $this->NFeref->appendChild($this->refCTe);
        }
        if (isset($this->ECFref)) {
            $this->tagNFref();
            $this->NFeref->appendChild($this->ECFref);
        }
        //coloca NFref na tag ide
        if (isset($this->ide)) {
            if (isset($this->NFref)) {
                $this->ide->appendChild($this->NFref);
            }
        }
        
        if (isset($this->emit) && isset($this->enderEmit)) {
            $node = $this->emit->getElementsByTagName("IE")->item(0);
            $this->emit->insertBefore($this->enderEmit, $node);
        }
        if (isset($this->dest) && isset($this->enderDest)) {
            $node = $this->dest->getElementsByTagName("indIEDest")->item(0);
            if (!isset($node)) {
                $node = $this->dest->getElementsByTagName("IE")->item(0);
            }
            $this->dest->insertBefore($this->enderDest, $node);
        }

        
        $this->tagNFe();
        if (!isset($this->infNFe)) {
            return false;
        }
        
        $this->infNFe->appendChild($this->ide);
        $this->infNFe->appendChild($this->emit);
        if (isset($this->dest)) {
            $this->infNFe->appendChild($this->dest);
        }
        if (isset($this->retirada)) {
            $this->infNFe->appendChild($this->retirada);
        }
        if (isset($this->entrega)) {
            $this->infNFe->appendChild($this->entrega);
        }
        if (isset($aAutXML)  && $this->versao > 2.00) {
            foreach ($aAutXML as $aut) {
                $this->infNFe->appendChild($aut);
            }
        }
        
        $this->NFe->appendChild($this->infNFe);
        $this->dom->appendChild($this->NFe);
        return $this->dom->saveXML();
    }
    
    //cria a tag NFe
    protected function tagNFe()
    {
        if (!isset($this->NFe)) {
            $this->NFe = $this->dom->createElement("NFe");
            $this->NFe->setAttribute("xmlns", "http://www.portalfiscal.inf.br/nfe");
        }
    }
    
    //tag infNFe
    public function taginfNFe($chave = '', $versao = '')
    {
        if (!ereg('[0-9]{44}', $chave)) {
            $this->errmsg = 'Passe a chave de 44 digitos para esse método. '.$chave;
            return false;
        }
        if (!ereg('^[0-9]{1}[.][0-9]{2}$', $versao)) {
            $this->errmsg = 'Versão incorreta de NFe. '.$chave;
            return false;
        }
        $this->infNFe = $this->dom->createElement("infNFe");
        $this->infNFe->setAttribute("Id", 'NFe'.$chave);
        $this->infNFe->setAttribute("versao", $versao);
        //$this->infNFe->setAttribute("pk_nItem",'');
        $this->versao = (int) $versao;
        return $this->infNFe;
    }
    
    //tag ide
    public function tagide(
        $cUF = '',
        $cNF = '',
        $natOp = '',
        $indPag = '',
        $mod = '',
        $serie = '',
        $nNF = '',
        $dhEmi = '',
        $dhSaiEnt = '',
        $tpNF = '',
        $idDest = '',
        $cMunFG = '',
        $tpImp = '',
        $tpEmis = '',
        $cDV = '',
        $tpAmb = '',
        $finNFe = '',
        $indFinal = '',
        $indPres = '',
        $procEmi = '',
        $verProc = '',
        $dhCont = '',
        $xJust = ''
    ) {
        $ide = $this->dom->createElement("ide");
        //$cUF
        $this->addChild($ide, "cUF", $cUF);
        //$cNF
        $this->addChild($ide, "cNF", $cNF);
        //$natOp
        $this->addChild($ide, "natOp", $natOp);
        //$indPag
        $this->addChild($ide, "indPag", $indPag);
        //$mod
        $this->addChild($ide, "mod", $mod);
        //$serie
        $this->addChild($ide, "serie", $serie);
        //$nNF
        $this->addChild($ide, "nNF", $nNF);
        //$dhEmi nome e formato diferente a partir da versao 3.00
        if ($this->versao > 2.00) {
            $this->addChild($ide, "dhEmi", $dhEmi);
        } else {
            $this->addChild($ide, "dEmi", $dhEmi);
        }
        //$dhSaiEnt (opcional e somente para modelo 55)
        if ($mod == '55' && $dhSaiEnt != '') {
            if ($this->versao > 2.00) {
                $this->addChild($ide, "dhSaiEnt", $dhSaiEnt);
            } else {
                $this->addChild($ide, "dSaiEnt", $dhSaiEnt);
            }
        }
        //$tpNF
        $this->addChild($ide, "tpNF", $tpNF);
        //$idDest essa tag existe somente a partir da versão 3.00
        if ($this->versao > 2.00) {
            $this->addChild($ide, "idDest", $idDest);
        }
        //$cMunFG
        $this->addChild($ide, "cMunFG", $cMunFG);
        //$tpImp
        $this->addChild($ide, "tpImp", $tpImp);
        //$tpEmis
        $this->addChild($ide, "tpEmis", $tpEmis);
        //$cDV
        $this->addChild($ide, "cDV", $cDV);
        //$tpAmb
        $this->addChild($ide, "tpAmb", $tpAmb);
        //$finNFe
        $this->addChild($ide, "finNFe", $finNFe);
        //$indFinal
        if ($this->versao > 2.00) {
            $this->addChild($ide, "indFinal", $indFinal);
        }
        //$indPres
        if ($this->versao > 2.00) {
            $this->addChild($ide, "indPres", $indPres);
        }
        //$procEmi
        $this->addChild($ide, "procEmi", $procEmi);
        //$verProc
        $this->addChild($ide, "verProc", $verProc);
        if ($this->versao > 2.00) {
            //$dhCont
            if ($dhCont != '' && $xJust != '') {
                $this->addChild($ide, "dhCont", $dhCont);
                //$xJust
                $this->addChild($ide, "xJust", $xJust);
            }
        }
        $this->mod = $mod;
        $this->ide = $ide;
        return $ide;
    }

    public function tagNFref()
    {
        if (!isset($this->NFref)) {
            //$NFref
            $this->NFref = $this->dom->createElement("NFref", $NFref);
        }
    }
    
    public function tagrefNFe($refNFe = '')
    {
        $this->refNFe = $this->dom->createElement("refNFe", $refNFe);
        return $this->refNFe;
    }
    
    public function tagrefNF(
        $cUF = '',
        $AAMM = '',
        $CNPJ = '',
        $mod = '',
        $serie = '',
        $nNF = ''
    ) {
        $this->refNF = $this->dom->createElement("refNF");
        $this->addChild($this->refNF, "cUF", $cUF);
        $this->addChild($this->refNF, "AAMM", $AAMM);
        $this->addChild($this->refNF, "CNPJ", $CNPJ);
        $this->addChild($this->refNF, "mod", $mod);
        $this->addChild($this->refNF, "serie", $serie);
        $this->addChild($this->refNF, "nNF", $nNF);
        return $this->refNF;
    }

    public function tagNFPref(
        $cUF = '',
        $AAMM = '',
        $CNPJ = '',
        $CPF = '',
        $IE = '',
        $mod = '',
        $serie = '',
        $nNF = ''
    ) {
        $this->refNFP = $this->dom->createElement("refNFP");
        $this->addChild($this->refNFP, "cUF", $cUF);
        $this->addChild($this->refNFP, "AAMM", $AAMM);
        $this->addChild($this->refNFP, "CNPJ", $CNPJ);
        $this->addChild($this->refNFP, "CPF", $CPF);
        $this->addChild($this->refNFP, "IE", $IE);
        $this->addChild($this->refNFP, "mod", $mod);
        $this->addChild($this->refNFP, "serie", $serie);
        $this->addChild($this->refNFP, "nNF", $nNF);
        return $this->refNFP;
    }
    
    public function tagCTeref($refCTe = '')
    {
        $this->refCTe = $this->dom->createElement("refCTe", $refCTe);
        return $this->refCTe;
    }
    
    public function tagECFref(
        $mod = '',
        $nECF = '',
        $nCOO = ''
    ) {
        $this->ECFref = $this->dom->createElement("ECFref");
        $this->addChild($this->ECFref, "mod", $mod);
        $this->addChild($this->ECFref, "nCOO", $nCOO);
        return $this->ECFref;
    }
    
    //tag emit
    public function tagemit(
        $CNPJ = '',
        $CPF = '',
        $xNome = '',
        $xFant = '',
        $IE = '',
        $IEST = '',
        $IM = '',
        $CNAE = '',
        $CRT = ''
    ) {
        $this->emit = $this->dom->createElement("emit");
        if ($CNPJ != '') {
            $this->addChild($this->emit, "CNPJ", $CNPJ);
        } else {
            $this->addChild($this->emit, "CPF", $CPF);
        }
        $this->addChild($this->emit, "xNome", $xNome);
        if ($xFant != '') {
            $this->emit->appendChild($this->emit, "xFant", $xFant);
        }
        $this->addChild($this->emit, "IE", $IE);
        if ($IEST != '') {
            $this->addChild($this->emit, "IEST", $IEST);
        }
        if ($IM != '') {
            $this->addChild($this->emit, "IM", $IM);
        }
        if ($CNAE != '') {
            $this->addChild($this->emit, "CNAE", $CNAE);
        }
        if ($CRT != '') {
            $this->addChild($this->emit, "CRT", $CRT);
        }
    }
    
    //tagendEmit
    public function tagenderEmit(
        $xLgr = '',
        $nro = '',
        $xCpl = '',
        $xBairro = '',
        $cMun = '',
        $xMun = '',
        $UF = '',
        $CEP = '',
        $cPais = '',
        $xPais = '',
        $fone = ''
    ) {
        $this->enderEmit = $this->dom->createElement("enderEmit");
        $this->addChild($this->enderEmit, "xLgr", $xLgr);
        $this->addChild($this->enderEmit, "nro", $nro);
        if ($xCpl != '') {
            $this->addChild($this->enderEmit, "xCpl", $xCpl);
        }
        $this->addChild($this->enderEmit, "xBairro", $xBairro);
        $this->addChild($this->enderEmit, "cMun", $cMun);
        $this->addChild($this->enderEmit, "xMun", $xMun);
        $this->addChild($this->enderEmit, "UF", $UF);
        $this->addChild($this->enderEmit, "CEP", $CEP);
        if ($cPais != '') {
            $this->addChild($this->enderEmit, "cPais", $cPais);
        }
        if ($xPais != '') {
            $this->addChild($this->enderEmit, "xPais", $xPais);
        }
        if ($fone != '') {
            $this->addChild($this->enderEmit, "fone", $fone);
        }
        return $this->enderEmit;
    }
    
    //tag dest (opcional para modelo 65)
    public function tagdest(
        $CNPJ = '',
        $CPF = '',
        $idEstrangeiro = '',
        $xNome = '',
        $indIEDest = '',
        $IE = '',
        $ISUF = '',
        $IM = '',
        $email = ''
    ) {
        $this->dest = $this->dom->createElement("dest");
        if ($CNPJ != '') {
            $this->addChild($this->dest, "CNPJ", $CNPJ);
        } elseif ($CPF != '') {
            $this->addChild($this->dest, "CPF", $CPF);
        } else {
            $this->addChild($this->dest, "idEstrangeiro", $idEstrangeiro);
        }
        if ($xNome != '') {
            $this->addChild($this->dest, "xNome", $xNome);
        }
        if ($this->versao > 2.00) {
            if ($this->mod == '65') {
                $indIEDest = '9';
                $this->addChild($this->dest, "indIEDest", $indIEDest);
            } else {
                $this->addChild($this->dest, "indIEDest", $indIEDest);
            }
        }
        if ($this->versao > 2.00) {
            if ($indIEDest != '9' && $indIEDest != '2') {
                $this->addChild($this->dest, "IE", $IE);
            }
        } else {
            $this->addChild($this->dest, "IE", $IE);
        }
        if ($ISUF != '') {
            $this->addChild($this->dest, "ISUF", $ISUF);
        }
        if ($IM != '') {
            $this->addChild($this->dest, "IM", $IM);
        }
        if ($email != '') {
            $this->addChild($this->dest, "email", $email);
        }
        return $this->dest;
    }
    
    public function tagenderDest(
        $xLgr = '',
        $nro = '',
        $xCpl = '',
        $xBairro = '',
        $cMun = '',
        $xMun = '',
        $UF = '',
        $CEP = '',
        $cPais = '',
        $xPais = '',
        $fone = ''
    ) {
        $this->enderDest = $this->dom->createElement("enderDest");
        $this->addChild($this->enderDest, "xLgr", $xLgr);
        $this->addChild($this->enderDest, "nro", $nro);
        if ($xCpl != '') {
            $this->addChild($this->enderDest, "xCpl", $xCpl);
        }
        $this->addChild($this->enderDest, "xBairro", $xBairro);
        $this->addChild($this->enderDest, "cMun", $cMun);
        $this->addChild($this->enderDest, "xMun", $xMun);
        $this->addChild($this->enderDest, "UF", $UF);
        if ($CEP != '') {
            $this->addChild($this->enderEmit, "CEP", $CEP);
        }
        if ($cPais != '') {
            $this->addChild($this->enderDest, "cPais", $cPais);
        }
        if ($xPais != '') {
            $this->addChild($this->enderDest, "xPais", $xPais);
        }
        if ($fone != '') {
            $this->addChild($this->enderDest, "fone", $fone);
        }
        return $this->enderDest;
    }
    
    //tag retirada (opcional)
    public function tagretirada(
        $CNPJ = '',
        $CPF = '',
        $xLgr = '',
        $nro = '',
        $xCpl = '',
        $xBairro = '',
        $cMun = '',
        $xMun = '',
        $UF = ''
    ) {
        $this->retirada = $this->dom->createElement("retirada");
        if ($CNPJ != '') {
            $this->addChild($this->retirada, "CNPJ", $CNPJ);
        } else {
            $this->addChild($this->retirada, "CPF", $CPF);
        }
        $this->addChild($this->retirada, "xLgr", $xLgr);
        $this->addChild($this->retirada, "nro", $nro);
        if ($xCpl != '') {
            $this->addChild($this->retirada, "xCpl", $xCpl);
        }
        $this->addChild($this->retirada, "xBairro", $xBairro);
        $this->addChild($this->retirada, "cMun", $cMun);
        $this->addChild($this->retirada, "xMun", $xMun);
        $this->addChild($this->retirada, "UF", $UF);
        return $this->retirada;
    }
    
    //tag entrega (opcional)
    public function tagentrega(
        $CNPJ = '',
        $CPF = '',
        $xLgr = '',
        $nro = '',
        $xCpl = '',
        $xBairro = '',
        $cMun = '',
        $xMun = '',
        $UF = ''
    ) {
        $this->entrega = $this->dom->createElement("entrega");
        if ($CNPJ != '') {
            $this->addChild($this->entrega, "CNPJ", $CNPJ);
        } else {
            $this->addChild($this->entrega, "CPF", $CPF);
        }
        $this->addChild($this->entrega, "xLgr", $xLgr);
        $this->addChild($this->entrega, "nro", $nro);
        if ($xCpl != '') {
            $this->addChild($this->entrega, "xCpl", $xCpl);
        }
        $this->addChild($this->entrega, "xBairro", $xBairro);
        $this->addChild($this->entrega, "cMun", $cMun);
        $this->addChild($this->entrega, "xMun", $xMun);
        $this->addChild($this->entrega, "UF", $UF);
        return $this->entrega;
    }
    
    //tag autXML
    public function tagautoXML($CNPJ = '', $CPF = '')
    {
        $autXML = $this->dom->createElement("autXML");
        if ($CNPJ != '') {
            $this->addChild($autXML, "CNPJ", $CNPJ);
        } else {
            $this->addChild($autXML, "CPF", $CPF);
        }
        $this->aAutXML[]=$autXML;
        return $autXML;
    }
    
    //tag det
    public function tagdet()
    {
        if (isset($this->aProd)) {
            foreach ($this->aProd as $prod) {
                $det = $this->dom->createElement("det");
                $nItem = $prod['nItem'];
                $det->setAttribute("nItem", $nItem);
                $this->aDet[] = $det;
                $det = null;
            }
        }
        
    }
    //tag det/prod
    public function tagprod(
        $nItem = '',
        $cProd = '',
        $cEAN = '',
        $xProd = '',
        $NCM = '',
        $NVE = '',
        $EXTIPI = '',
        $CFOP = '',
        $uCom = '',
        $qCom = '',
        $vUnCom = '',
        $vProd = '',
        $cEANTrib = '',
        $uTrib = '',
        $qTrib = '',
        $vUnTrib = '',
        $vFrete = '',
        $vSeg = '',
        $vDesc = '',
        $vOutro = '',
        $indTot = '',
        $xPed = '',
        $nItemPed = '',
        $nFCI = ''
    ) {
        $prod = $this->dom->createElement("prod");
        $this->addChild($prod, "cProd", $cProd);
        $this->addChild($prod, "cEAN", $cEAN);
        $this->addChild($prod, "xProd", $xProd);
        $this->addChild($prod, "NCM", $NCM);
        if ($NVE != '') {
            $this->addChild($prod, "NVE", $NVE);
        }
        if ($EXTIPI != '') {
            $this->addChild($prod, "EXTIPI", $EXTIPI);
        }
        $this->addChild($prod, "CFOP", $CFOP);
        $this->addChild($prod, "uCom", $uCom);
        $this->addChild($prod, "qCom", $qCom);
        $this->addChild($prod, "vUnCom", $vUnCom);
        $this->addChild($prod, "vProd", $vProd);
        $this->addChild($prod, "cEANTrib", $cEANTrib);
        $this->addChild($prod, "uTrib", $uTrib);
        $this->addChild($prod, "qTrib", $qTrib);
        $this->addChild($prod, "vUnTrib", $vUnTrib);
        if ($vFrete != '') {
            $this->addChild($prod, "vFrete", $vFrete);
        }
        if ($vSeg != '') {
            $this->addChild($prod, "vSeg", $vSeg);
        }
        if ($vDesc != '') {
            $this->addChild($prod, "vDesc", $vDesc);
        }
        if ($vOutro != '') {
            $this->addChild($prod, "vOutro", $vOutro);
        }
        $this->addChild($prod, "indTot", $indTot);
        if ($xPed != '') {
            $this->addChild($prod, "xPed", $xPed);
        }
        if ($nItemPed != '') {
            $this->addChild($prod, "nItemPed", $nItemPed);
        }
        if ($nFCI != '') {
            $this->addChild($prod, "nFCI", $nFCI);
        }
        $this->aProd[$nItem] = $prod;
        return $prod;
    }
    
    //tag det/prod/DI
    public function tagDI(
        $nItem = '',
        $nDI = '',
        $dDI = '',
        $xLocDesemb = '',
        $UFDesemb = '',
        $dDesemb = '',
        $tpViaTransp = '',
        $vAFRMM = '',
        $tpIntermedio = '',
        $CNPJ = '',
        $UFTerceiro = '',
        $cExportador = ''
    ) {
        $DI = $this->dom->createElement("DI");
        $this->addChild($DI, "nDI", $nDI);
        $this->addChild($DI, "dDI", $dDI);
        $this->addChild($DI, "xLocDesemb", $xLocDesemb);
        $this->addChild($DI, "UFDesemb", $UFDesemb);
        $this->addChild($DI, "dDesemb", $dDesemb);
        $this->addChild($DI, "tpViaTransp", $tpViaTransp);
        if ($vAFRMM != '') {
            $this->addChild($DI, "vAFRMM", $vAFRMM);
        }
        $this->addChild($DI, "tpIntermedio", $tpIntermedio);
        if ($CNPJ != '') {
            $this->addChild($DI, "CNPJ", $CNPJ);
        }
        if ($UFTerceiro != '') {
            $this->addChild($DI, "UFTerceiro", $UFTerceiro);
        }
        $this->addChild($DI, "cExportador", $cExportador);
        $this->aDI[$nItem][$nDI] = $DI;
        return $DI;
    }
    
    //tag det/prod/DI/adi
    public function tagadi(
        $nItem = '',
        $nDI = '',
        $nAdicao = '',
        $nSeqAdicC = '',
        $cFabricante = '',
        $vDescDI = '',
        $nDraw = ''
    ) {
        $adi = $this->dom->createElement("adi");
        $this->addChild($adi, "nAdicao", $nAdicao);
        $this->addChild($adi, "nSeqAdicC", $nSeqAdicC);
        $this->addChild($adi, "cFabricante", $cFabricante);
        if ($vDescDI != '') {
            $this->addChild($adi, "vDescDI", $vDescDI);
        }
        if ($nDraw != '') {
            $this->addChild($adi, "nDraw", $nDraw);
        }
        $this->aAdi[$nItem][$nDI][] = $adi;
        return $adi;
    }
    
    //tag det/prod/detExport
    public function tagdetExport(
        $nItem = '',
        $nDraw = '',
        $exportInd = '',
        $nRE = '',
        $chNFe = '',
        $qExport = ''
    ) {
        if ($this->versao > 2.00) {
            $detExport = $this->dom->createElement("detExport");
            if ($nDraw != '') {
                $this->addChild($detExport, "nDraw", $nDraw);
            }
            if ($exportInd != '') {
                $this->addChild($detExport, "exportInd", $exportInd);
            }
            $this->addChild($detExport, "nRE", $nRE);
            $this->addChild($detExport, "chNFe", $chNFe);
            $this->addChild($detExport, "qExport", $qExport);
            $this->aDetExport[$nItem] = $detExport;
        }
    }
    
    //tag det/prod/veicProd (opcional)
    public function tagveicProd(
        $nItem = '',
        $tpOp = '',
        $chassi = '',
        $cCor = '',
        $xCor = '',
        $pot = '',
        $cilin = '',
        $pesoL = '',
        $pesoB = '',
        $nSerie = '',
        $tpComb = '',
        $nMotor = '',
        $CMT = '',
        $dist = '',
        $anoMod = '',
        $anoFab = '',
        $tpPint = '',
        $tpVeic = '',
        $espVeic = '',
        $VIN = '',
        $condVeic = '',
        $cMod = '',
        $cCorDENATRAN = '',
        $lota = '',
        $tpRest = ''
    ) {
        $veicProd = $this->dom->createElement("veicProd");
        $this->addChild($veicProd, "tpOp", $tpOp);
        $this->addChild($veicProd, "chassi", $chassi);
        $this->addChild($veicProd, "cCor", $cCor);
        $this->addChild($veicProd, "xCor", $xCor);
        $this->addChild($veicProd, "pot", $pot);
        $this->addChild($veicProd, "cilin", $cilin);
        $this->addChild($veicProd, "pesoL", $pesoL);
        $this->addChild($veicProd, "pesoB", $pesoB);
        $this->addChild($veicProd, "nSerie", $nSerie);
        $this->addChild($veicProd, "tpCpmb", $tpComb);
        $this->addChild($veicProd, "nMotor", $nMotor);
        $this->addChild($veicProd, "CMT", $CMT);
        $this->addChild($veicProd, "dist", $dist);
        $this->addChild($veicProd, "anoMd", $anoMod);
        $this->addChild($veicProd, "anoFab", $anoFab);
        $this->addChild($veicProd, "tpPint", $tpPint);
        $this->addChild($veicProd, "tpVeic", $tpVeic);
        $this->addChild($veicProd, "espVeic", $espVeic);
        $this->addChild($veicProd, "VIN", $VIN);
        $this->addChild($veicProd, "condVeic", $condVeic);
        $this->addChild($veicProd, "cMod", $cMod);
        $this->addChild($veicProd, "cCorDENATRAN", $cCorDENATRAN);
        $this->addChild($veicProd, "lota", $lota);
        $this->addChild($veicProd, "tpResp", $tpRest);
        $this->aVeicProd[$nItem] = $veicProd;
        return $veicProd;
    }
    
    //tag det/prod/med (opcional)
    public function tagmed(
        $nItem = '',
        $nLote = '',
        $qLote = '',
        $dFab = '',
        $dVal = '',
        $vPMC = ''
    ) {
        $med = $this->dom->createElement("med");
        $this->addChild($med, "nLote", $nLote);
        $this->addChild($med, "qLote", $qLote);
        $this->addChild($med, "dFab", $dFab);
        $this->addChild($med, "dVal", $dVal);
        $this->addChild($med, "vPMC", $vPMC);
        $this->aMed[$nItem] = $med;
        return $med;
    }
    
    //tag det/prod/arma (opcional)
    public function tagarma(
        $nItem = '',
        $tpArma = '',
        $nSerie = '',
        $nCano = '',
        $descr = ''
    ) {
        $arma = $this->dom->createElement("arma");
        $this->addChild($arma, "tpArma", $tpArma);
        $this->addChild($arma, "nSerie", $nSerie);
        $this->addChild($arma, "nCano", $nCano);
        $this->addChild($arma, "descr", $descr);
        $this->aArma[$nItem] = $arma;
        return $arma;
    }
    
    //tag det/prod/comb (opcional)
    public function tagcomb(
        $nItem = '',
        $cProdANP = '',
        $pMixGN = '',
        $CODIF = '',
        $qTemp = '',
        $UFCons = '',
        $CIDE = '',
        $qBCProd = '',
        $vAliqProd = '',
        $vCIDE = ''
    ) {
        $comb = $this->dom->createElement("comb");
        $this->addChild($comb, "cProdANP", $cProdANP);
        $this->addChild($comb, "pMixGN", $pMixGN);
        $this->addChild($comb, "CODIF", $CODIF);
        $this->addChild($comb, "qTemp", $qTemp);
        $this->addChild($comb, "UFCons", $UFCons);
        $this->addChild($comb, "CIDE", $CIDE);
        $this->addChild($comb, "qBCProd", $qBCProd);
        $this->addChild($comb, "vAliqProd", $vAliqProd);
        $this->addChild($comb, "vCIDE", $vCIDE);
        $this->aComb[$nItem] = $comb;
        return $comb;
    }

    //tag det/imposto
    //tag det/imposto/ICMS
    //tag det/imposto/IPI (opcional)
    //tag det/imposto/II (opcional)
    //tag det/imposto/ISSQN (opcional)
    //tag det/imposto/PIS
    //tag det/imposto/PISST (opcional)
    //tag det/imposto/COFINS
    //tag det/imposto/COFINSST (opcional)
    
    //tag total
    public function tagtotal()
    {
        
    }
    //tag total/ICMSTot
    //tag total/ISSQNTot (opcional)
    //tag total/reTrib (opcional)
    
    //tag transp
    public function tagtransp()
    {
        
    }
    //tag transp/tranporta (opcional)
    //tag transp/veiculo (opcional)
    //tag transp/reboque (opcional)
    //tag transp/reTransp (opcional)
    //tag transp/vol (opcional)
    
    //tag infNFe/cobr (opcional)
    public function tagcobr()
    {
        
    }
    //tag infNFe/cobr/fat (opcional)
    //tag infNFe/cobr/fat/dup (opcional)
    
    //tag infNFe/pag (opcional)
    public function tagpag(
        $tPag = '',
        $vPag = ''
    ) {
        if ($this->mod == '65') {
            $this->pag = $this->dom->createElement("pag");
            $this->addChild($this->pag, "tPag", $tPag);
            $this->addChild($this->pag, "vPag", $vPag);
        }
        return $this->pag;
    }
    
    //tag infNFe/pag/card
    public function tagcard(
        $CNPJ = '',
        $tBand = '',
        $cAut = ''
    ) {
        if ($this->mod == '65' && $tBand != '') {
            $this->card = $this->dom->createElement("card");
            $this->addChild($this->card, "CNPJ", $CNPJ);
            $this->addChild($this->card, "tBand", $tBand);
            $this->addChild($this->card, "cAut", $cAut);
        }
        return $this->card;
    }
    
    //tag infAdic (opcional)
    public function taginfAdic(
        $infAdFisco = '',
        $infCpl = ''
    ) {
        $this->infAdic = $this->dom->createElement("infAdic");
        if ($infAdFisco != '') {
            $this->addChild($this->infAdic, "$infAdFisco", $infAdFisco);
        }
        if ($infCpl != '') {
            $this->addChild($this->infAdic, "$infCpl", $infCpl);
        }
        return $this->infAdic;
    }
    
    //tag infAdic/obsCont (opcional)
    public function tagobsCont(
        $xCampo = '',
        $xTexto = ''
    ) {
        
    }
    
    //tag infAdic/obsFisco (opcional)
    public function tagobsFisco(
        $xCampo = '',
        $xTexto = ''
    ) {
        
    }
    
    //tag infAdic/procRef (opcional)
    public function tagprocRef(
        $nProc = '',
        $indProc = ''
    ) {
        
    }
    
    //tag infNFe/exporta (opcional)
    public function tagexporta(
        $UFSaidaPais = '',
        $xLocExporta = '',
        $xLocDespacho = ''
    ) {
        
    }
    
    //tag infNFe/compra (opcional)
    public function tagcompra(
        $xNEmp = '',
        $xPed = '',
        $xCont = ''
    ) {
        
    }
    
    //tag infNFe/cana (opcional)
    public function tagcana(
        $safra = '',
        $ref = ''
    ) {
        
    }
    
    //tag infNFe/cana/forDia
    public function tagforDia(
        $dia = '',
        $qtde = '',
        $qTotMes = '',
        $qTotAnt = '',
        $qTotGer = ''
    ) {
        
    }
    
    //tag infNFe/cana/deduc (opcional)
    public function tagdeduc(
        $xDed = '',
        $vDed = '',
        $vFor = '',
        $vTotDed = '',
        $vLiqFor = ''
    ) {
        
    }
    
    public function validChave($chave)
    {
        
    }
    
    private function addChild(&$parent, $name, $content)
    {
        $temp = $this->dom->createElement($name, $content);
        $parent->appendChild($temp);
    }
}
