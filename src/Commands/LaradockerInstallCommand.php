<?php

namespace Jdsantos\Laradocker\Commands;

use Illuminate\Console\Command;
use Jdsantos\Laradocker\Contracts\StubConfigurator;
use Jdsantos\Laradocker\Contracts\StubProcessor;

class LaradockerInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradocker:install
                            {--d|database=* : What database support to include in the image, separated by comma (if multiple). Possible values are: sqlite, mysql, mariadb, pgsql, sqlsrv }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install any necessary files to prepare this app for docker deployments';

    public function __construct(private StubProcessor $processor, private StubConfigurator $configurator)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->collectConsoleOptionsInput();

        $this->greetUser();

        $this->confirmDatabaseChoices();

        $this->informUserAboutFilesToBeCreated();

        if ($this->confirm(question: 'Laradocker will now generate and create all necessary files inside your project. Do you wish to continue?', default: true)) {
            $this->processAndCopyStubs();
        } else {
            $this->error('Installation canceled. No files were generated inside your project.');
            $this->newLine();
        }
    }

    /**
     * Collects all user input from the command parameters/options
     */
    private function collectConsoleOptionsInput(): void
    {
        $databases = $this->option('database');

        // wipe out empty options
        $databases = array_filter($databases);

        foreach ($databases as $databaseIdentifier) {
            $this->configurator->addDatabaseSupportFor($databaseIdentifier);
        }
    }

    /**
     * Greet the user with a nice message and guide him onwards
     */
    private function greetUser(): void
    {
        $this->line('----------------------------------------------------------------');
        $this->newLine();
        $this->info('👋 Welcome to Laradocker!');
        $this->newLine();
        $this->comment('The following steps will help you set up Laradocker in your project.');
        $this->newLine();
        $this->line('----------------------------------------------------------------');
        $this->newLine();
    }

    /**
     * Confirm all database choices made and allow the user to add support for multiple databases
     *
     * @return [type]
     */
    private function confirmDatabaseChoices(): void
    {
        if ($this->confirm('Do you want your image support to (more) databases?', true)) {
            $databases = $this->choice(
                question: 'Which databases do you want to support?',
                choices: $this->configurator->getAllPossibleDatabasesToSupport(),
                multiple: true
            );
            foreach ($databases as $database) {
                $this->configurator->addDatabaseSupportFor($database);
            }
            $this->informInstallOptions();
            $this->confirmDatabaseChoices();
        } else {
            $this->informInstallOptions();
        }
    }

    /**
     * Inform the user about the install options made
     */
    private function informInstallOptions(): void
    {
        $databasesAsTableCell = implode(', ', $this->configurator->getDatabasesToSupport());

        $this->line('Laradocker has collected the following options: ');
        $this->newLine();
        $this->table(
            ['Databases to support'],
            [[$databasesAsTableCell]]
        );
        $this->newLine();
    }

    /**
     * Give feedback about the files that are going to be generated in the project directory
     *
     * @return [type]
     */
    private function informUserAboutFilesToBeCreated(): void
    {
        $files = $this->processor->getStubFiles();

        $this->line('');
        $this->line('The following files will be created in your project:');
        $this->line('');
        foreach ($files as $file) {
            $this->line("<options=bold;fg=green>\t + $file</>");
        }
    }

    /**
     * Processes and copies necessary files to the user's project directory
     */
    private function processAndCopyStubs(): void
    {
        // retrieve files to be copied to the project folder
        $files = $this->processor->getStubFiles();

        $this->newLine();
        $this->line('Generating and copying your files...');
        $this->newLine();

        // process files considering all options collected
        $this->processor->process();

        $bar = $this->output->createProgressBar(count($files));
        $bar->start();
        $this->newLine();

        // copy each one of the files to the destination
        foreach ($files as $file) {
            $this->processor->copy($file);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine(2);

        // do some housekeeping
        $this->processor->cleanup();

        $this->line('');
        $this->line('<options=bold;fg=green>Installed successfully.</>');
        $this->line('');
        $this->line('<fg=white>Now you can run your app on docker using one of the following commands:</>');
        $this->line('');
        $this->line('<options=bold> • docker run -p 80:80 -v laravel_storage:/opt/laravel/storage --rm -it $(docker build -q .)</>');
        $this->line('<options=bold> • docker build -t foo . && docker run -p 80:80 -v laravel_storage:/opt/laravel/storage -it foo</>');
        $this->line('');
    }
}
