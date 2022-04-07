<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsCoreLogBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @deprecated since 6.3.0 - use Psr\Log\LoggerInterface in conjunction with Monolog logging instead
 */
class LogShowCommand extends Command
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('log:show')
            ->setDescription('show log entries from database')
            ->addOption(
                'channel',
                'c',
                InputOption::VALUE_OPTIONAL,
                'log channel ( "%" is allowed)',
                null
            )
            ->addOption(
                'cmsident',
                null,
                InputOption::VALUE_OPTIONAL,
                'cmsident (if set, all options except of "details" will be ignored)',
                null
            )
            ->addOption(
                'number',
                null,
                InputOption::VALUE_OPTIONAL,
                'number of log entries',
                10
            )
            ->addOption(
                'level',
                'l',
                InputOption::VALUE_OPTIONAL,
                'the minimum level',
                100
            )
            ->addOption(
                'ip',
                'ip',
                InputOption::VALUE_OPTIONAL,
                'ip address to restrict entries to',
                null
            )
            ->addOption(
                'details',
                null,
                InputOption::VALUE_OPTIONAL,
                'determines what is shown (min, med or max)',
                'min'
            )
            ->addOption(
                'page',
                null,
                InputOption::VALUE_OPTIONAL,
                'shows the given page',
                0
            )
            ->addOption(
                'sort',
                null,
                InputOption::VALUE_OPTIONAL,
                'sort the entries (desc/asc)',
                'desc'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $infoStrings = array();

        $channel = $input->getOption('channel');
        $number = $input->getOption('number');
        $level = $input->getOption('level');
        $ip = $input->getOption('ip');
        $cmsident = $input->getOption('cmsident');
        $page = $input->getOption('page');
        $sorting = $input->getOption('sort');

        if (!array_key_exists($sorting, array('desc', 'asc'))) {
            $sorting = 'desc';
        }

        if ($page < 0) {
            $page = 0;
        }
        $limitOffset = $page * $number;
        $limitNumber = $number;

        $sql = 'SELECT * FROM `pkg_cms_core_log`';

        $conditions = array();

        if ($cmsident) {
            $sql .= " WHERE `pkg_cms_core_log`.`cmsident` = '".\MySqlLegacySupport::getInstance()->real_escape_string($cmsident)."'";
            $infoStrings[] = '<comment>cmsident: '.$cmsident.'</comment>';

            $input->setOption('details', 'single');
        } else {
            if ($channel) {
                $conditions[] = "`pkg_cms_core_log`.`channel` LIKE '".\MySqlLegacySupport::getInstance()->real_escape_string($channel)."'";
                $infoStrings[] = '<comment>channel: '.$channel.'</comment>';
            }
            if ($level) {
                $conditions[] = "`pkg_cms_core_log`.`level` >= '".\MySqlLegacySupport::getInstance()->real_escape_string($level)."'";
                $infoStrings[] = '<comment>level: >= '.$level.'</comment>';
            }
            if ($ip) {
                $conditions[] = "`pkg_cms_core_log`.`ip` LIKE '".\MySqlLegacySupport::getInstance()->real_escape_string($ip)."'";
                $infoStrings[] = '<comment>ip: '.$ip.'</comment>';
            }

            if (!empty($conditions)) {
                $sql .= ' WHERE '.implode(' AND ', $conditions);
            }

            $sql .= ' ORDER BY `pkg_cms_core_log`.`timestamp` '.strtoupper($sorting);

            $sqlCount = $sql;
            $rowCount = \MySqlLegacySupport::getInstance()->num_rows(\MySqlLegacySupport::getInstance()->query($sqlCount));

            $sql .= ' LIMIT '.\MySqlLegacySupport::getInstance()->real_escape_string($limitOffset).','.\MySqlLegacySupport::getInstance()->real_escape_string($limitNumber);
            //$infoStrings[] = '<comment>limit: '.$number.'</comment>';
            $limitEnd = $limitOffset + $limitNumber;
            $infoStrings[] = '<comment>showing entries '.$limitOffset.'-'.$limitEnd.' of '.$rowCount.' entries</comment>';
        }

        $infoStrings[] = '<comment>details: '.$input->getOption('details').'</comment>';

        $output->writeln('<info>SHOWING LOGS:</info>');
        $output->writeln(implode(' | ', $infoStrings));

        $output->writeln('resulted query: '.$sql);
        $output->writeln(' ');

        $tableColumns = array();

        $result = \MySqlLegacySupport::getInstance()->query($sql);
        while ($row = \MySqlLegacySupport::getInstance()->fetch_object($result)) {
            $timestamp = date('d.m.Y H:i:s', $row->timestamp);

            $message = $row->message;
            $message = str_replace(array("\n", "\t", "\r"), '', $message);
            if (strlen($message) > 100) {
                $message = substr($message, 0, 50).' ... '.substr($message, (strlen($message) - 50), strlen($message));
            }

            switch ($input->getOption('details')) {
                case 'min':
                    $tableColumns[] = array($row->cmsident, $timestamp, $row->channel, $row->level, $message);
                    break;
                case 'med':
                    $tableColumns[] = array(
                        $row->cmsident,
                        $timestamp,
                        $row->channel,
                        $row->level,
                        $message,
                        strtoupper($row->http_method).' '.$row->server.$row->request_url,
                    );
                    break;
                case 'max':
                    $tableColumns[] = array(
                        $row->cmsident,
                        $timestamp,
                        $row->channel,
                        $row->level,
                        $message,
                        strtoupper($row->http_method).' / '.$row->server.$row->request_url.' / '.$row->referrer_url,
                        $row->ip.' / '.$row->data_extranet_user_id.' / '.$row->data_extranet_user_name.' / '.$row->cms_user_id,
                    );
                    break;
                case 'single':
                    foreach ($row as $key => $value) {
                        if ('timestamp' === $key) {
                            $value = $timestamp.' ('.$value.')';
                        }
                        $tableColumns[] = array($key, $value);
                    }
            }
        }

        if (!empty($tableColumns)) {
            $table = new Table($output);

            switch ($input->getOption('details')) {
                case 'min':
                    $table->setHeaders(array('cmsident', 'time', 'channel', 'level', 'message'));
                    break;
                case 'med':
                    $table->setHeaders(array(
                            'cmsident',
                            'time',
                            'channel',
                            'level',
                            'message',
                            'method / server + request_url',
                        )
                    );
                    break;
                case 'max':
                    $table->setHeaders(array(
                            'cmsident',
                            'time',
                            'channel',
                            'level',
                            'message',
                            'method / server + request_url / referrer_url',
                            'ip / data_extranet_user_id / -_user_name / cms_user_id',
                        )
                    );
                    break;
            }
            if ('single' === $input->getOption('details')) {
                foreach ($tableColumns as $column) {
                    $key = $column[0];
                    $value = $column[1];

                    if ('context' === $key) {
                        $value = print_r(unserialize($value), true);
                    }
                    $output->writeln('<info>'.$key.':</info> <comment>'.$value.'</comment>');
                }
            } else {
                $table->setRows($tableColumns);
                $table->render();
            }
        } else {
            $output->writeln('<error>No log entries found</error>');
        }

        return 0;
    }
}
