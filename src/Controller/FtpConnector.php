<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use phpseclib3\Net\SFTP;

class FtpConnector extends AbstractController
{
    const FTP_SERVER = 'ftp.transport.productsup.io';
    const FTP_SERVER_USERNAME='pupDev';         //or save in DB/file based config?
    const FTP_SERVER_PASSWORD='pupDev2018';     //or save in DB/file based config?
    private $conn_id;

    public function __construct(){}

    public function connect()
    {
        try {
            /* $sftp = new SFTP('ftp.transport.productsup.io');
            $sftp_login = $sftp->login('pupDev', 'pupDev2018');

            print_r($sftp_login);die("--till sftp login"); */

            $ftp_server = self::FTP_SERVER;
            $this->conn_id = ftp_connect($ftp_server); //or die("Couldn't connect to $ftp_server");
            
            $login_result = ftp_login($this->conn_id, self::FTP_SERVER_USERNAME, self::FTP_SERVER_PASSWORD);
            if ((!$this->conn_id) || (!$login_result)) {
                //die("FTP Connection Failed");
                return false;
                //TODO::Write an error log here
            } else {
                ftp_pasv($this->conn_id, true);
            }
            return true;
            
        } catch (\Throwable $th) {
            //throw $th;
            //print_r($th->getMessage());die("--exception");
            //TODO::Write an error log here
            return false;
        }
    }

    /**
     * This will download the XMl from remote FTP server to our server's local storage.
     * SS added: 15/03/2022
     */
    public function transferFileFromRemoteServerToLocal($remoteServerFilename) 
    {
        if($remoteServerFilename!='') {

            $now=date("Y-m-d-His");

            //you know my OS now.
            $local_file = __DIR__."\..\..\public\uploads\local_$now"."_"."$remoteServerFilename";

            $fp = fopen($local_file,"w");//or die("Can't create file");;    // open local file to write to
            if(!$fp){
                //TODO::Write an error log here
                return false;   
            }
            // download server file and save it to open local file
            if (ftp_fget($this->conn_id, $fp, $remoteServerFilename, FTP_ASCII, 0)) {
                return $local_file;
            } else {
                //TODO::Write an error log here
                return false;
            }
            // close connection and file handler
            $this->closeFtpConnection();
            fclose($fp);
        }
    }

    private function closeFtpConnection() {
        ftp_close($this->conn_id);
    }  
}