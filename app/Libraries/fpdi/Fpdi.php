<?php



/**

 * This file is part of FPDI

 *

 * @package   setasign\Fpdi

 * @copyright Copyright (c) 2020 Setasign GmbH & Co. KG (https://www.setasign.com)

 * @license   http://opensource.org/licenses/mit-license The MIT License

 */



namespace setasign\Fpdi;



use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;

use setasign\Fpdi\PdfParser\PdfParserException;

use setasign\Fpdi\PdfParser\Type\PdfIndirectObject;

use setasign\Fpdi\PdfParser\Type\PdfNull;



/**

 * Class Fpdi

 *

 * This class let you import pages of existing PDF documents into a reusable structure for FPDF.

 */

 

 

class Fpdi extends FpdfTpl

{

    use FpdiTrait;



    /**

     * FPDI version

     *

     * @string

     */

    const VERSION = '2.3.4';

protected $B;

protected $I;

protected $U;

protected $HREF;

protected $fontList;

protected $issetfont;

protected $issetcolor;



function __construct($orientation='P', $unit='mm', $format='A4')

{

	//Call parent constructor

	parent::__construct($orientation,$unit,$format);

	//Initialization

	$this->B=0;

	$this->I=0;

	$this->U=0;

	$this->HREF='';

	$this->ALIGN='';

	$this->fontlist=array('arial', 'times', 'courier', 'helvetica', 'symbol');

	$this->issetfont=true;

	$this->issetcolor=true;

}



    protected function _enddoc()

    {

        parent::_enddoc();

        $this->cleanUp();

		

    }



    /**

     * Draws an imported page or a template onto the page or another template.

     *

     * Give only one of the size parameters (width, height) to calculate the other one automatically in view to the

     * aspect ratio.

     *

     * @param mixed $tpl The template id

     * @param float|int|array $x The abscissa of upper-left corner. Alternatively you could use an assoc array

     *                           with the keys "x", "y", "width", "height", "adjustPageSize".

     * @param float|int $y The ordinate of upper-left corner.

     * @param float|int|null $width The width.

     * @param float|int|null $height The height.

     * @param bool $adjustPageSize

     * @return array The size

     * @see Fpdi::getTemplateSize()

     */

    public function useTemplate($tpl, $x = 0, $y = 0, $width = null, $height = null, $adjustPageSize = false)

    {

        if (isset($this->importedPages[$tpl])) {

            $size = $this->useImportedPage($tpl, $x, $y, $width, $height, $adjustPageSize);

            if ($this->currentTemplateId !== null) {

                $this->templates[$this->currentTemplateId]['resources']['templates']['importedPages'][$tpl] = $tpl;

            }

            return $size;

        }



        return parent::useTemplate($tpl, $x, $y, $width, $height, $adjustPageSize);

    }



    /**

     * Get the size of an imported page or template.

     *

     * Give only one of the size parameters (width, height) to calculate the other one automatically in view to the

     * aspect ratio.

     *

     * @param mixed $tpl The template id

     * @param float|int|null $width The width.

     * @param float|int|null $height The height.

     * @return array|bool An array with following keys: width, height, 0 (=width), 1 (=height), orientation (L or P)

     */

    public function getTemplateSize($tpl, $width = null, $height = null)

    {

        $size = parent::getTemplateSize($tpl, $width, $height);

        if ($size === false) {

            return $this->getImportedPageSize($tpl, $width, $height);

        }



        return $size;

    }



    /**

     * @inheritdoc

     * @throws CrossReferenceException

     * @throws PdfParserException

     */

    protected function _putimages()

    {

        $this->currentReaderId = null;

        parent::_putimages();



        foreach ($this->importedPages as $key => $pageData) {

            $this->_newobj();

            $this->importedPages[$key]['objectNumber'] = $this->n;

            $this->currentReaderId = $pageData['readerId'];

            $this->writePdfType($pageData['stream']);

            $this->_put('endobj');

        }



        foreach (\array_keys($this->readers) as $readerId) {

            $parser = $this->getPdfReader($readerId)->getParser();

            $this->currentReaderId = $readerId;



            while (($objectNumber = \array_pop($this->objectsToCopy[$readerId])) !== null) {

                try {

                    $object = $parser->getIndirectObject($objectNumber);

                } catch (CrossReferenceException $e) {

                    if ($e->getCode() === CrossReferenceException::OBJECT_NOT_FOUND) {

                        $object = PdfIndirectObject::create($objectNumber, 0, new PdfNull());

                    } else {

                        throw $e;

                    }

                }



                $this->writePdfType($object);

            }

        }



        $this->currentReaderId = null;

    }



    /**

     * @inheritdoc

     */

    protected function _putxobjectdict()

    {

        foreach ($this->importedPages as $key => $pageData) {

            $this->_put('/' . $pageData['id'] . ' ' . $pageData['objectNumber'] . ' 0 R');

        }



        parent::_putxobjectdict();

    }



    /**

     * @inheritdoc

     */

    protected function _put($s, $newLine = true)

    {

        if ($newLine) {

            $this->buffer .= $s . "\n";

        } else {

            $this->buffer .= $s;

        }

    }

	

	

function hex2dec($couleur = "#000000"){

    $R = substr($couleur, 1, 2);

    $rouge = hexdec($R);

    $V = substr($couleur, 3, 2);

    $vert = hexdec($V);

    $B = substr($couleur, 5, 2);

    $bleu = hexdec($B);

    $tbl_couleur = array();

    $tbl_couleur['R']=$rouge;

    $tbl_couleur['V']=$vert;

    $tbl_couleur['B']=$bleu;

    return $tbl_couleur;

}



//conversion pixel -> millimeter at 72 dpi

function px2mm($px){

    return $px*25.4/72;

}
function WriteHtmlCell($cellWidth, $html){  

//echo $this->w.'===='. $cellWidth;
    
    $rm = $this->rMargin;
	$lm = $this->lMargin;
    $this->SetRightMargin($this->w - $this->GetX() - $cellWidth);
	$this->SetLeftMargin(13);
    $this->WriteHtmlPara($html);
    $this->SetRightMargin($rm);
	
}


function txtentities($html){

    $trans = get_html_translation_table(HTML_ENTITIES);

    $trans = array_flip($trans);	

    return strtr($html, $trans);

}



function txtentities_para($html){
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);	
    return strtr($html, $trans);

}



function Justify($text, $w, $h)

{

    $tab_paragraphe = explode("\n", $text);

    $nb_paragraphe = count($tab_paragraphe);

    $j = 0;



    while ($j<$nb_paragraphe) {



        $paragraphe = $tab_paragraphe[$j];

        $tab_mot = explode(' ', $paragraphe);

        $nb_mot = count($tab_mot);



        // Handle strings longer than paragraph width

        $k=0;

        $l=0;

        while ($k<$nb_mot) {



            $len_mot = strlen ($tab_mot[$k]);

            if ($len_mot<($w-5) )

            {

                $tab_mot2[$l] = $tab_mot[$k];

                $l++;    

            } else {

                $m=0;

                $chaine_lettre='';

                while ($m<$len_mot) {



                    $lettre = substr($tab_mot[$k], $m, 1);

                    $len_chaine_lettre = $this->GetStringWidth($chaine_lettre.$lettre);



                    if ($len_chaine_lettre>($w-7)) {

                        $tab_mot2[$l] = $chaine_lettre . '-';

                        $chaine_lettre = $lettre;

                        $l++;

                    } else {

                        $chaine_lettre .= $lettre;

                    }

                    $m++;

                }

                if ($chaine_lettre) {

                    $tab_mot2[$l] = $chaine_lettre;

                    $l++;

                }



            }

            $k++;

        }



        // Justified lines

        $nb_mot = count($tab_mot2);

        $i=0;

        $ligne = '';

        while ($i<$nb_mot) {



            $mot = $tab_mot2[$i];

            $len_ligne = $this->GetStringWidth($ligne . ' ' . $mot);



            if ($len_ligne>($w-5)) {



                $len_ligne = $this->GetStringWidth($ligne);

                $nb_carac = strlen ($ligne);

                $ecart = (($w-2) - $len_ligne) / $nb_carac;

                $this->_out(sprintf('BT %.3F Tc ET',$ecart*$this->k));

                $this->MultiCell($w,$h,$ligne);

                $ligne = $mot;



            } else {



                if ($ligne)

                {

                    $ligne .= ' ' . $mot;

                } else {

                    $ligne = $mot;

                }



            }

            $i++;

        }



        // Last line

        $this->_out('BT 0 Tc ET');

        $this->MultiCell($w,$h,$ligne);

        $tab_mot = '';

        $tab_mot2 = '';

        $j++;

    }

}





function WriteHTML($html)

{

    //HTML parser

   // $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus

    $html=str_replace("\n",chr(10),$html); //remplace retour à la ligne par un espace

    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises

    foreach($a as $i=>$e)

    {

        if($i%2==0)

        {

            //Text

            if($this->HREF)

                $this->PutLink($this->HREF,$e);

		    else{				

                $this->Write(5,stripslashes($this->txtentities($e)));

			}

        }

        else

        {

            //Tag

            if($e[0]=='/')

                $this->CloseTag(strtoupper(substr($e,1)));

            else

            {

                //Extract attributes

                $a2=explode(' ',$e);

                $tag=strtoupper(array_shift($a2));

                $attr=array();

                foreach($a2 as $v)

                {

                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))

                        $attr[strtoupper($a3[1])]=$a3[2];

                }

                $this->OpenTag($tag,$attr);

            }

        }

    }

}


function textWrap($content) {
        $break = strpos( $content, "\r" ) === false ? "\n" : "\r\n";
		$content = wordwrap( $content, 78, $break );
		
    return $content;
		
    }
	
function WriteHTMLPara($html)

{
	
$html = str_replace('<span style="text-align:right;">','<span style="text-align:left;">',$html);
$new_html = '<div style="text-align:left;left:200px;margin-left:200px;">'.$html.'</div>';

    //HTML parser

   // $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus

    $html=str_replace("\n",chr(10),$new_html); //remplace retour à la ligne par un espace
$new_html = str_replace(chr(194)," ",$new_html);
    $a=preg_split('/<(.*)>/U',$new_html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises

    foreach($a as $i=>$e)

    {

        if($i%2==0)

        {

            //Text

            if($this->HREF)

                $this->PutLink($this->HREF,$e);

		    else{				
				// $break = strpos( $e, "\r" ) === false ? "\n" : "\r\n";
				//$e = wordwrap( $e,100,$break,true );
		     
                $this->Write(5,stripslashes($this->txtentities_para($e)));

			}

        }

        else

        {

            //Tag

            if($e[0]=='/')

                $this->CloseTag(strtoupper(substr($e,1)));

            else

            {

                //Extract attributes

                $a2=explode(' ',$e);

                $tag=strtoupper(array_shift($a2));

                $attr=array();

                foreach($a2 as $v)

                {

                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))

                        $attr[strtoupper($a3[1])]=$a3[2];

                }

                $this->OpenTag($tag,$attr);

            }

        }

    }

}


function OpenTag($tag, $attr)

{

    //Opening tag

    switch($tag){

        case 'STRONG':

            $this->SetStyle('B',true);

            break;

        case 'EM':

            $this->SetStyle('I',true);

            break;

        case 'B':

        case 'I':

        case 'U':

            $this->SetStyle($tag,true);

            break;

        case 'A':

            $this->HREF=$attr['HREF'];

            break;

        case 'IMG':

            if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {

                if(!isset($attr['WIDTH']))

                    $attr['WIDTH'] = 0;

                if(!isset($attr['HEIGHT']))

                    $attr['HEIGHT'] = 0;

                $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));

            }

            break;

        case 'TR':

        case 'BLOCKQUOTE':

        case 'BR':

            $this->Ln(5);

            break;

        case 'P':

            $this->Ln(10);

            break;

        case 'FONT':

            if (isset($attr['COLOR']) && $attr['COLOR']!='') {

                $coul=$this->hex2dec($attr['COLOR']);

                $this->SetTextColor($coul['R'],$coul['V'],$coul['B']);

                $this->issetcolor=true;

            }

            if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {

                $this->SetFont(strtolower($attr['FACE']));

                $this->issetfont=true;

            }

            break;

    }

}



function CloseTag($tag)

{

    //Closing tag

    if($tag=='STRONG')

        $tag='B';

    if($tag=='EM')

        $tag='I';

    if($tag=='B' || $tag=='I' || $tag=='U')

        $this->SetStyle($tag,false);

    if($tag=='A')

        $this->HREF='';

    if($tag=='FONT'){

        if ($this->issetcolor==true) {

            $this->SetTextColor(0);

        }

        if ($this->issetfont) {

            $this->SetFont('arial');

            $this->issetfont=false;

        }

    }

}



function SetStyle($tag, $enable)

{

    //Modify style and select corresponding font

    $this->$tag+=($enable ? 1 : -1);

    $style='';

    foreach(array('B','I','U') as $s)

    {

        if($this->$s>0)

            $style.=$s;

    }

    $this->SetFont('',$style);

}



function PutLink($URL, $txt)

{

    //Put a hyperlink

    $this->SetTextColor(0,0,255);

    $this->SetStyle('U',true);

    $this->Write(5,$txt,$URL);

    $this->SetStyle('U',false);

    $this->SetTextColor(0);

}

}

