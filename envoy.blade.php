@servers(['develop' => 'rachel.lee@52.76.60.162','staging' => 'rachel.lee@52.76.230.79', 'production' => ''])

# use to deploy specific branch to development environment for testing
@task('deploy_branch', ['on' => ['develop']])
    # update composer
    # /usr/local/bin/composer self-update

    # deploy admin
    cd /var/www/hubwire-admin
    git fetch
    git checkout {{ $admin_branch }}
    git pull origin {{ $admin_branch }}
    composer install
    composer dump-autoload
    php artisan migrate

    # deploy hapi
    cd /var/www/hapi
    git fetch
    git checkout {{ $hapi_branch }}
    git pull origin {{ $hapi_branch }}
    composer install
    composer dump-autoload
    php artisan migrate
@endtask

# use to deploy latest changes to development server
@task('deploy_develop', ['on' => ['develop']])
    # update composer
    # /usr/local/bin/composer self-update

    cd /var/www/hubwire-admin
    git fetch
    git checkout develop
    git pull origin develop
    composer install
    composer dump-autoload
    php artisan migrate --step

    cd /var/www/hapi
    git fetch
    git checkout develop
    git pull origin develop
    composer install
    composer dump-autoload
    php artisan migrate --step
@endtask

# use to deploy latest changes to development server
@task('deploy_demo', ['on' => ['develop']])
    # update composer
    # /usr/local/bin/composer self-update

    cd /var/www/hubwire-demo/hubwire-admin
    git fetch
    git checkout develop
    git pull origin develop
    composer install
    composer dump-autoload
    php artisan migrate --step

    cd /var/www/hubwire-demo/hapi
    git fetch
    git checkout develop
    git pull origin develop
    composer install
    composer dump-autoload
    php artisan migrate --step
@endtask

# use to deploy latest changes to staging server
@task('deploy_staging', ['on' => ['staging']])
    # update composer
    # /usr/local/bin/composer self-update

    cd /var/www/Hubwire-Admin-3
    /usr/local/bin/composer self-update
    git fetch
    git checkout staging
    git pull origin staging
    composer install
    composer dump-autoload
    php artisan migrate --step

    cd /var/www/hapi
    git fetch
    git checkout staging
    git pull origin staging
    composer install
    composer dump-autoload
    php artisan migrate --step
@endtask

# use to deploy latest changes to production server
@task('deploy_production', ['on' => 'production', 'confirm' => true])
    cd /var/www/hubwire-admin
    /usr/local/bin/composer self-update
    git fetch
    git checkout master
    git pull origin master
    composer install
    composer dump-autoload
    php artisan migrate --step
@endtask