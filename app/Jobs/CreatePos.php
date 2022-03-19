<?php

namespace App\Jobs;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use WebReinvent\CPanel\CPanel;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CreatePos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $sub_domain;
    public $cpanel_domain;
    public $cpanel_project_dir;
    public $cpanel_home;
    public $cpanel_profix;
    public $user_database_data;
    public $database_file_path;
    public $composer_path;
    public $composer_home;
    public $github_repository;
    public function __construct($domain)
    {
        $this->sub_domain = $domain;
        $this->cpanel_domain = env('CPANEL_DOMAIN');            // "example.com"
        $this->cpanel_project_dir = env('CPANEL_PROJECT_DIR');  // "/home/your_cpanel_name/your_file"
        $this->cpanel_home = env('CPANEL_HOME');                // "/home/your_cpanel_name"
        $this->cpanel_profix = env('CPANEL_PREFIX');            // *_
        $this->user_database_data = $this->cpanel_profix . $this->sub_domain;
        $this->database_file_path = public_path('');            // your_database_name.sql
        $this->composer_path = env('CPANEL_COMPOSER_PATH');     // /opt/cpanel/..../composer
        $this->composer_home = env('CPANEL_COMPOSER_HOME');     // /home/your_cpanel_name/.composer
        $this->github_repository = env('GITHUB_REPOSITORY');    // github_repository
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cpanel = new CPanel();

        $path = $this->cpanel_project_dir . $this->sub_domain;

        if(File::exists($this->cpanel_home . $path)) {
            return "false";
        }

        info("create subdomain");
        $sub_domain_return  = $cpanel->createSubDomain($this->sub_domain, $this->cpanel_domain, $this->cpanel_project_dir);
        info($sub_domain_return);

        info("create database");
        $database_return  = $cpanel->createDatabase($this->user_database_data);
        info($database_return);

        info("create user database");
        $user_database_return  = $cpanel->createDatabaseUser($this->user_database_data, "testTest@12345678");
        info($user_database_return);

        info("all allpriveleges to user");
        $set_allprivileges = $cpanel->setAllPrivilegesOnDatabase($this->user_database_data, $this->user_database_data);
        info($set_allprivileges);

        $path = $this->cpanel_home . $path;

        info($path);

        $clone_process = Process::fromShellCommandline('git clone '. $this->github_repository . ' ' . $path)->setTimeout(30);
        $clone_process->run();
        info("clone done", [$clone_process->getErrorOutput()]);

        sleep(10);

        chdir($path);
        

        if(!File::copy($path . '/' . ".env.example", $path . '/' . ".env")) {
            return false;
        }

        $text = Str::of(file_get_contents($path . '/.env'))
        ->replace("APP_NAME=", "APP_NAME=\"". $this->sub_domain ."\"")
        ->replace("DB_DATABASE=laravel", "DB_DATABASE=" . $this->user_database_data)
        ->replace("DB_USERNAME=root", "DB_USERNAME=" . $this->user_database_data)
        ->replace("APP_KEY=", "APP_KEY=base64:ssXAVnlUwKl4Bz2E6Fit903WnCkkImqUBAfoHmxbwb0=")
        ->replace("DB_PASSWORD=", "DB_PASSWORD=testTest@12345678")->__toString();

        
        File::replace($path . '/.env', $text);
        
        info(".env done");

        $vendor_process = Process::fromShellCommandline( $this->composer_path . " install --no-progress -n", null, ['COMPOSER_HOME' => $this->composer_home])->setTimeout(300);
        $vendor_process->run();

        info("vendor done", [$vendor_process->getErrorOutput()]);
        
        
        $sql = public_path($this->database_file_path);

        $db = [
            'username' => $this->user_database_data,
            'password' => "testTest@12345678",
            'host' => env('DB_HOST'),
            'database' => $this->user_database_data
        ];

        exec("mysql --user={$db['username']} --password={$db['password']} --host={$db['host']} --database {$db['database']} < $sql");

        return ;
    }
}
