<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function test_users_index_view_uses_relative_ajax_endpoint()
    {
        $view = file_get_contents(resource_path('views/admin/user/index.blade.php'));
        $head = file_get_contents(resource_path('views/admin/layouts/head.blade.php'));

        $this->assertStringContainsString("url: '/admin/users'", $view);
        $this->assertStringContainsString('var SITEURL = window.location.origin', $head);
    }
}
