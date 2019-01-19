<?php
/**
 * @package midcom.console
 * @author CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @copyright CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

namespace midcom\console\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use midcom;
use midcom_error;
use midcom_services_cron;
use midcom_baseclasses_components_cron_handler;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Cron command
 *
 * When executed, it checks all component manifests for cron jobs and runs them sequentially.
 * The components are processed in the order they are returned by the component loader, the
 * jobs are run in the order they are listed in the manifest.
 *
 * <b>Launching MidCOM Cron from a System Cron</b>
 *
 * Add something like this to your crontab:
 *
 * <pre>
 * * * * * *  www-data {PROJECT PATH}vendor/bin/midcom --servername=example.com midcom:cron minute
 * 1 * * * *  www-data {PROJECT PATH}vendor/bin/midcom --servername=example.com midcom:cron hour
 * 1 1 * * *  www-data {PROJECT PATH}vendor/bin/midcom --servername=example.com midcom:cron day
 * </pre>
 *
 * Make sure to pass the correct hostname, this is used e.g. when generating links in mails sent by cron jobs.
 * In case you're running more than one site on the same server, this is also needed to get the same cache
 * prefix as the website the job belongs to.
 *
 * Also take care to run the cron command with the same user the website is running, otherwise RCS entries or
 * attachments generated by cron jobs could end up unreadable.
 *
 * @see midcom_services_cron
 * @package midcom.console
 */
class cron extends Command
{
    protected function configure()
    {
        $this->setName('midcom:cron')
            ->setDescription('Checks all component manifests for cron jobs and runs them sequentially')
            ->addArgument('type', InputArgument::OPTIONAL, 'Recurrence (minute, hour, or day)', 'minute')
            ->addOption('job', 'j', InputOption::VALUE_REQUIRED, 'Run only this job');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!midcom::get()->auth->request_sudo('midcom.services.cron')) {
            throw new midcom_error('Failed to get sudo');
        }

        // compat for old-style calls (type=minute)
        $type = str_replace('type=', '', $input->getArgument('type'));

        $recurrence = 'MIDCOM_CRON_' . strtoupper($type);
        if (!defined($recurrence)) {
            throw new midcom_error('Unsupported type ' . $type);
        }

        if ($job = $input->getOption('job')) {
            $this->run_job($job, $output);
        } else {
            // Instantiate cron service and run
            $cron = new midcom_services_cron(constant($recurrence));
            $data = midcom::get()->componentloader->get_all_manifest_customdata('midcom.services.cron');

            foreach ($cron->load_jobs($data) as $job) {
                $this->run_job($job['handler'], $output);
            }
        }
        midcom::get()->auth->drop_sudo();
    }

    private function run_job($classname, OutputInterface $output)
    {
        $handler = new $classname;
        if ($handler->initialize($output)) {
            $output->writeln("Executing job <info>{$classname}</info>", OutputInterface::VERBOSITY_VERBOSE);
            $handler->execute();
        } else {
            $output->writeln("Skipping job <info>{$classname}</info>", OutputInterface::VERBOSITY_VERBOSE);
        }
    }
}