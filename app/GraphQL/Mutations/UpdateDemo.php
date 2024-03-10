<?php

namespace App\GraphQL\Mutations;

use App\Actions\SyncDemoArguments;
use App\Models\Demo;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateDemo
{
    /**
     * Return a value for the field.
     *
     * @param  @param  null  $root Always null, since this field has no parent.
     * @param  array<string, mixed>  $args The field arguments passed by the client.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Shared between all fields.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Metadata for advanced query resolution.
     * @return mixed
     */
    public function __invoke($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $demo = Demo::findOrFail($args['id']);

        $demo->update($args);

        if(isset($args['demo']) && $args['demo']->isValid()) {
            $demo->addMedia($args['demo'])->toMediaCollection('demo');
        }

        app(SyncDemoArguments::class)->execute($demo, $args['arguments'] ?? []);

        return $demo;
    }
}
