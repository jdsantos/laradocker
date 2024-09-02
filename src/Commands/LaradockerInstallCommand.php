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

    /**
     * @param  StubProcessor
     * @param  StubConfigurator
     */
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
        // Collect existing params sent by the user
        $this->collectConsoleOptionsInput();

        // Greet the user
        $this->greetUser();

        // prompt the user for database confirmation
        $this->confirmDatabaseChoices();

        $this->informUserAboutFilesToBeCreated();

        if ($this->confirm(question: 'Laradocker will now generate and create all necessary files inside your project. Do you wish to continue?', default: true)) {
            // Copy the resulting stubs to the app installation directory
            $this->processAndCopyStubs();
        } else {
            $this->error('Installation canceled. No files were generated inside your project.');
            $this->newLine();
        }
    }

    private function collectConsoleOptionsInput()
    {
        // Collect the database option
        $databases = $this->option('database');

        // wipe out empty options
        $databases = array_filter($databases);

        foreach ($databases as $databaseIdentifier) {
            $this->configurator->addDatabaseSupportFor($databaseIdentifier);
        }
    }

    private function greetUser(): void
    {
        $this->line('----------------------------------------------------------------');
        $this->newLine();
        $this->info('👋 Welcome to the Laradocker installer!');
        $this->newLine();
        $this->line('----------------------------------------------------------------');
        $this->newLine();
    }

    private function confirmDatabaseChoices()
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
            // Inform the user of what is going to be inestalled
            $this->informInstallOptions();
            $this->confirmDatabaseChoices();
        } else {
            // Inform the user of what is going to be inestalled
            $this->informInstallOptions();
        }
    }

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

    private function informUserAboutFilesToBeCreated()
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
     * Copies stubs from the 'Stubs' folder to the current project's root location
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
