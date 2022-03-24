<?php 

namespace Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Console\Command;

/**
 * SS added: 15/03/2022.
 */
class XmlFileReaderCommand extends Command
{
    //Configure your command
    public function configure()
    {
        $this->setName('processIntoCsv')
            ->setDescription('Process a file to get its data as a CSV.')
            ->setHelp('This command allows you to get a CSV based result out of the file name you mentioned...')
            ->addArgument('filename', InputArgument::REQUIRED, 'The name of the file.');
           /*  ->addOption('loc', null, InputOption::VALUE_OPTIONAL,'Location: Remote|Local?',1)
            ->addOption('host', null, InputOption::VALUE_OPTIONAL,'Host IP in case its a remote file',1)
            ->addOption('username', null, InputOption::VALUE_OPTIONAL,'Host server login',1)        //Ask here or make it Config based
            ->addOption('pwd', null, InputOption::VALUE_OPTIONAL,'Host server login password?',1);  //Ask here or make it config based */
    }

    //This takes forward the process after the command is received.
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->processFile($input, $output);
        /**
         * Return to avoid:  Uncaught TypeError: Return value of execute() must be of the type int, "null" returned.
         */
        return Command::SUCCESS;
    }
}