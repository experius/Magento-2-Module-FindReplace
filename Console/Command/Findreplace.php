<?php

namespace Experius\FindReplace\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;


class Findreplace extends Command
{

    const FIND = "find";
    const REPLACE = "replace";
    const DRY_RUN_OPTION = 'dry_run';

    const TABLE = "table";
    const COLUMNS = "columns";

    private $connection;
    private $resourceConnection;

    protected $input;
    protected $output;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\State $state
    ){
        $this->resourceConnection = $resourceConnection;
        $this->connection = $this->resourceConnection->getConnection();
        $this->state = $state;

        return parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ){

        $this->input = $input;
        $this->output = $output;

        $this->state->setAreaCode('adminhtml');

        if(!$this->input->getOption(self::FIND)) {
            $this->output->writeln("No Find value");
            return false;
        }

        if(!$this->input->getOption(self::REPLACE)) {
            $this->output->writeln("No Replace value");
            return false;
        }

        if(!$this->input->getOption(self::TABLE)) {
            $this->output->writeln("No Table value");
            return false;
        }

        if(!$this->input->getOption(self::COLUMNS)) {
            $this->output->writeln("No Columns value");
            return false;
        }

        $find = $this->input->getOption(self::FIND);
        $replace = $this->input->getOption(self::REPLACE);

        foreach(explode(',',$this->input->getOption(self::COLUMNS)) as $column) {
            $this->findAndReplace($this->input->getOption(self::TABLE), $column, $find, $replace);
        }

    }

    protected function findAndReplace($table,$column,$find,$replace){
        $tableName  = $this->resourceConnection->getTableName($table);

        /* @var $result \Magento\Framework\DB\Statement\Pdo\Mysql */

        if($this->input->getOption(self::DRY_RUN_OPTION)) {
            $result = $this->connection->query("SELECT `{$column}` FROM `{$tableName}` WHERE `{$column}` LIKE '%{$find}%'");
            $this->output->writeln(sprintf("%d values would be replaced with this command in the %s column",$result->rowCount(),$column));
        } else {
            $result = $this->connection->query("UPDATE `{$tableName}` SET `{$column}` = REPLACE(`from_url`,'{$find}','{$replace}') WHERE `{$column}` LIKE '%{$find}%'");
            $this->output->writeln(sprintf("%d values replaced in the %s column",$result->rowCount(),$column));
        }

    }

    protected function configure()
    {
        $this->setName("experius_findreplace:replace");

        $this->setDescription("Replace values in selected table");

        $this->addOption(
            self::FIND,'f',
            InputOption::VALUE_REQUIRED,
            'Find'
        );

        $this->addOption(
            self::REPLACE,'r',
            InputOption::VALUE_REQUIRED,
            'Replace'
        );

        $this->addOption(
            self::REPLACE,'r',
            InputOption::VALUE_REQUIRED,
            'Replace'
        );

        $this->addOption(
            self::DRY_RUN_OPTION,'d',
            InputOption::VALUE_OPTIONAL,
            'Dry run'
        );

        $this->addOption(
            self::TABLE,'t',
            InputOption::VALUE_REQUIRED,
            'Table (optional comma separated)'
        );

        $this->addOption(
            self::COLUMNS,'c',
            InputOption::VALUE_REQUIRED,
            'Columns (optional comma separated)'
        );

        parent::configure();
    }
}