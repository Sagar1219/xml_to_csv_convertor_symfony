<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\FtpConnector;
use App\Controller\XmlFileParser;


class FileCsvGenerator extends AbstractController
{
    protected $filename;
    protected $fileLocalPath;
    const FILE_TYPE_XML='xml';

    /**
     * @param string $filename
     */
    public function __construct(string $remoteServerFilename, string $remoteFileLocalCompletePath)
    {
        //TODO::Inject Logger class dependency here.
        if (!empty($remoteServerFilename)) {
            $this->filename = $remoteServerFilename;
        }
        if(!empty($remoteFileLocalCompletePath)) {
            $this->fileLocalPath = $remoteFileLocalCompletePath;
        }
    }

    /**
     * You will have your CSV ready at this step.
     */
    public function processFile()
    {
        //double check
        if (!empty($this->filename)) {
            $fileType = pathinfo($this->filename, PATHINFO_EXTENSION);
            if($fileType==self::FILE_TYPE_XML) {
                //die("Reached here----");
                $xmlReaderObj=new XmlFileParser($this->filename);
                $result = $xmlReaderObj->readXml($this->fileLocalPath);
                return $result;
            }
        }
    }
}