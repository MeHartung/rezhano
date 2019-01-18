<?php
namespace Deployer;

require 'recipe/symfony3.php';

// Project name
set('application', 'rezhano');

// Project repository
set('repository', 'git@git.accurateweb.ru:accurateweb/rezhano.git');

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
    ->set('deploy_path', '/var/www/sites/rezhano')
    ->set('bin/php', '/usr/bin/php71')
    ->set('branch', 'development');

host('beta.rezhano.ru')
  ->stage('prod-beta')
  ->user('rezhano')
  ->set('deploy_path', '/home/r/rezhano/beta.rezhano.ru')
  ->set('bin/php', '/usr/local/bin/php7.2')
  ->set('branch', 'development')
  ->set('http_user', 'rezhano')
  ->set('keep_releases', 1);

host('rezhano.beget.tech')
  ->stage('prod')
  ->user('rezhano')
  ->set('deploy_path', '/home/r/rezhano/rezhano.ru')
  ->set('bin/php', '/usr/local/bin/php7.2')
  ->set('branch', 'development')
  ->set('http_user', 'rezhano')
  ->set('keep_releases', 3);

/*host('94.130.148.164')
    ->stage('prod')
    ->user('deployer')
    ->set('deploy_path', '/var/www/Store');*/

// Tasks

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:cache:warmup', 'database:migrate');

