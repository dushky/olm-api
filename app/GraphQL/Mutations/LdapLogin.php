<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use http\Exception\BadUrlException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\AuthenticationException;
use Joselfonseca\LighthouseGraphQLPassport\GraphQL\Mutations\Register;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Safe\Exceptions\LdapException;
use Symfony\Component\Routing\Exception\NoConfigurationException;

class LdapLogin extends Register
{
    protected $ldapConnection;
    protected $ldapDn;
    protected $ldapUsernameSufix;

    public function __construct()
    {
        $ldapHost = config('auth.ldap.host');
        if (is_null($ldapHost)) {
            throw new NoConfigurationException('Missing LDAP_HOST configuration');
        }

        $ldapDn = config("auth.ldap.base_distinguished_name");
        if (is_null($ldapDn)) {
            throw new NoConfigurationException('Missing LDAP_BASE_DISTINGUISHED_NAME configuration');
        }
        $this->ldapDn = $ldapDn;

        $ldapConnection = ldap_connect($ldapHost);
        if (!$ldapConnection) {
            throw new BadUrlException("Bad syntactic LDAP host URI");
        }
        $this->ldapConnection = $ldapConnection;

        $this->ldapUsernameSufix = config('auth.ldap.username_suffix');

        ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);

    }

    /**
     * @param $rootValue
     * @param array                                                    $args
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext|null $context
     * @param \GraphQL\Type\Definition\ResolveInfo                     $resolveInfo
     *
     * @throws \Exception
     *
     * @return array
     */
    public function resolve($rootValue, array $args, GraphQLContext $context = null, ResolveInfo $resolveInfo)
    {
        $username = $args['username'];
        $password = $args['password'];

        $args['username'] = "$username$this->ldapUsernameSufix";

        if (!@ldap_bind($this->ldapConnection, "uid=$username,$this->ldapDn", $password)) {
            throw new AuthenticationException(__('invalid_grant') ?? '', __('The user credentials were incorrect.'));
        }

        if (!User::query()->where(config('lighthouse-graphql-passport.username'), $args['username'])->exists()) {
            $this->registerNewUser($username, $password);
        }

        return app(\Joselfonseca\LighthouseGraphQLPassport\GraphQL\Mutations\Login::class)
            ->resolve($rootValue, $args, $context, $resolveInfo);
    }

    private function registerNewUser($username, $password)
    {
        $result = ldap_search(
            $this->ldapConnection,
            $this->ldapDn,
            "uid=$username",
            attributes: array("givenname", "surname"),
            sizelimit: 1);

        if (!$result) {
            throw new LdapException("No result find for uid=$username");
        }
        $entry = ldap_get_entries($this->ldapConnection, $result)[0];
        $newUser[config('lighthouse-graphql-passport.username')] = "$username$this->ldapUsernameSufix";
        $newUser["password"] = $password;
        $newUser["name"] = "{$entry['givenname'][0]} {$entry['sn'][0]}";
        $model = $this->createAuthModel($newUser);
        $this->validateAuthModel($model);
        $registeredUser = $model
            ->query()
            ->where(config('lighthouse-graphql-passport.username'), $newUser[config('lighthouse-graphql-passport.username')])
            ->first();
        $registeredUser->assignRole('Student');
    }
}
