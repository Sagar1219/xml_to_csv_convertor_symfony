<?php 

namespace Console;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\Controller\FileCsvGenerator;
use App\Controller\FtpConnector;

/**
 * SS added: 15/03/22
 */
class Command extends SymfonyCommand
{
    
    public function __construct()
    {
        parent::__construct();
    }

    //Presents you the final result of the processing
    protected function processFile(InputInterface $input, OutputInterface $output)
    {
        //outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            '====**** Xml to CSV generator Console App ****====',
            '==========================================',
            '',
        ]);
        
        $receivedFileName   = $input->getArgument('filename');

        $filePresence = $hostServer = $hostLoginUsr = $hostLoginPswd = null;
        //Optional Params Check
        if($input->hasArgument('loc')) {
            $filePresence = $input->getArgument('loc');
        }
        if ($input->hasArgument('host')) {
            $hostServer   = $input->getArgument('host');
        }
        if($input->hasArgument('username')) {
            $hostLoginUsr = $input->getArgument('username');
        }
        if($input->hasArgument('pwd')) {
            $hostLoginPswd = $input->getArgument('pwd');
        }

        if($filePresence=='' && 0) {
            $output->writeln([
                'Error: Mention Location : Remote|Local',
                '',
            ]);
        } else {
            $output->write($this->incomingJobProcessor($receivedFileName));
        }
        
        //return 0;
    }

    /**
     * @param String $remoteFilename
     * SS added: 15/03
     */
    private function incomingJobProcessor($remoteFilename)
    {
        if($remoteFilename!='') {
            try {
                $ftpObj     = new FtpConnector();                 //It's a remote file we know!
                $connected  = $ftpObj->connect();

                if($connected) 
                {
                    $remote_file_on_local_server_path = $ftpObj->transferFileFromRemoteServerToLocal($remoteFilename);
                    if($remote_file_on_local_server_path!='') {
                        $obj=new FileCsvGenerator($remoteFilename, $remote_file_on_local_server_path);
                        $isSuccess = $obj->processFile();
                    } else {
                        $isSuccess=false;
                    }                    
                } else {
                    $isSuccess=false;
                }                
            } catch (\Throwable $th) {
                //throw $th;
            }            
        } else {
            $isSuccess = false;
        }
        if($isSuccess) {
            print_r("CSV Created Successfully");
        } else {
            print_r("Error In CSV Generation");
        }
    }
}