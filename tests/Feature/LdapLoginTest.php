<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LdapLoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_ldap_login()
    {
        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation ($ldapInput: LdapLoginInput!) {
                ldapLogin(input: $ldapInput) {
                    access_token
                }
            }
        ', [
            'ldapInput' => [
                'username' => "hi",
                'password' => "hi"
            ]
        ]);

        dd($response->json());


    }
}
