<?php
namespace Deployer;

require 'recipe/symfony3.php';

// Project name
set('application', 'gpnnvi');

// Project repository
set('repository', 'git@git.accurateweb.ru:gpn/gpn_nvi.git');

// [Optional] Allocate tty for git clone. Default value is false.
//set('git_tty', true);

// Shared files/dirs between deploys 
add('shared_files', [
  'app/config/sphinx/sphinx.conf'
]);
add('shared_dirs', [
  'var/cdekcatalogue',
  'var/sphinx',
  'var/uploads',
  'web/uploads'
]);

// Writable dirs by web server 
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts

host('192.168.1.4')
    ->stage('staging')
    ->user('deployer')
    ->set('deploy_path', '/var/www/sites/gpnnvi')
    ->set('bin/php', '/usr/bin/php71')
    ->set('branch', 'development');

/*host('94.130.148.164')
    ->stage('prod')
    ->user('deployer')
    ->set('deploy_path', '/var/www/excam');*/

// Tasks

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:cache:warmup', 'database:migrate');

