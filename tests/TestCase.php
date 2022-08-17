<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection\Tests;

use Shirokovnv\ModelReflection\ModelReflectionServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @param $app
     * @return string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            ModelReflectionServiceProvider::class,
        ];
    }

    /**
     * @param $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        include_once __DIR__ . '/database/migrations/2022_08_17_185516_create_users_table.php';
        include_once __DIR__ . '/database/migrations/2022_08_17_190740_create_posts_table.php';

        // run the up() method (perform the migration)
        (new \CreateUsersTable())->up();
        (new \CreatePostsTable())->up();
    }
}
