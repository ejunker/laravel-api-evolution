<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::deleteDirectory(app_path().'/Http/Migrations');
});

afterEach(function () {
    File::deleteDirectory(app_path().'/Http/Migrations');
});

it('can create ApiMigration files', function () {
    Artisan::call('make:api-migration', ['name' => 'TestApiMigration']);

    $this->assertFileExists(app_path().'/Http/Migrations/TestApiMigration.php');
});
