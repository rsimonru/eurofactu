<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'git@github.com:rsimonru/eurofactu.git');
set('ssh_multiplexing', false);
set('git_tty', false);
set('keep_releases', 3);

add('shared_files', ['.env']);
add('shared_dirs', []);
add('writable_dirs', ['storage/app/private']);

// Hosts
host('euromatica-pro')
    ->set('remote_user', 'administrador')
    ->set('deploy_path', '/var/www/sites-deployer/eurofactu');

// Hooks
// Tarea para instalar dependencias de Node.js
task('build:install', function () {
    run('cd {{release_path}} && npm install');
});

// Tarea para compilar assets
task('build:compile', function () {
    run('cd {{release_path}} && npm run build');
});

// Hook para ejecutar después de actualizar el código
after('deploy:vendors', 'build:install');
after('build:install', 'build:compile');

before('deploy:publish', 'artisan:queue:restart');

after('deploy:failed', 'deploy:unlock');
