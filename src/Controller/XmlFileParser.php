<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class XmlFileParser extends AbstractController
{
    protected $xmlFileName;
    protected $xmlFileCompletePathLocal;
    protected $extractedXmlFromFile;
    protected $csvFileName;
    protected $columnHeadersArr=array();
    protected $headersInserted=false;
    protected $singleInsertableRow=array();
    private $csvFilePointer;

    const CSV_GENERATOR_FILE_PATH=__DIR__.'\..\..\public\uploads\CSV';
    const EXTENSION_CSV = 'csv';

    public function __construct(string $xmlfileName) {
        $this->xmlFileName=$xmlfileName;
    }

    public function readXml($localXmlFileCompletePath) {
        //var_dump($shallIProceed);die("--yes proceedd");
        if($localXmlFileCompletePath=='') {
            //TODO::write an error log
            return false;
        }
        //die("__shallProceed");
        $this->xmlFileCompletePathLocal = $localXmlFileCompletePath;
        $shallIProceed=$this->checkXMLFilePresent();
        if(!$shallIProceed){
            //TODO::write an error log
            return false;
        }
        $this->extractedXmlFromFile = simplexml_load_file($this->xmlFileCompletePathLocal,'SimpleXMLElement', LIBXML_NOCDATA);
        if($this->extractedXmlFromFile) {
            $output = $this->createCSVandTransferDataFromXml();
            return $output;
        } else {
            return false;
        }
    }

    private function checkXMLFilePresent()
    {
        if(file_exists($this->xmlFileCompletePathLocal)){
            return true;
        } else {
            return false;
        }
    }

    private function createCSVandTransferDataFromXml()
    {
        $this->generateCSVFileNameAsPerXML();
        $pathCsv=self::CSV_GENERATOR_FILE_PATH."\lile_".$this->csvFileName;
        $this->csvFilePointer = fopen($pathCsv,'a+');
        //var_dump($this->csvFilePointer);die("oop");
        if($this->csvFilePointer==null) {
            //TODO::Write an error log
            return false;
        }
        $this->createCSV($this->extractedXmlFromFile);
        fclose($this->csvFilePointer);
        return true;
    }

    public function generateCSVFileNameAsPerXML() 
    {
        $temp = explode(".",$this->xmlFileName)[0];
        $this->csvFileName = $temp.'.csv';
    }


    private function createCSV($xmlData)
    {
        //echo"<pre>";print_r($item);
        foreach ($xmlData->children() as $item) {
            $hasChild = (count($item->children()) > 0) ? true : false;

            if (!$hasChild) {
                array_push($this->columnHeadersArr,$item->getName());
                array_push($this->singleInsertableRow,$item);                
            } else {
                if(!empty($this->columnHeadersArr) && $this->headersInserted===false){
                    fputcsv($this->csvFilePointer, $this->columnHeadersArr); // Add the keys as the column headers
                    //print_r($this->columnHeadersArr);die("Header data");
                    $this->headersInserted=true;
                    $this->columnHeadersArr=array();
                }
                if(!empty($this->singleInsertableRow)) {
                    //print_r($singleInsertableRow);die('singleInsertableRow');
                    fputcsv($this->csvFilePointer, $this->singleInsertableRow);
                    $this->singleInsertableRow=array();
                }
                $this->singleInsertableRow=array();
                $this->createCsv($item);        //recursive solution
            }
        }
    }
}