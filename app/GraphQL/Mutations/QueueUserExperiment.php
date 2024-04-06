<?php

namespace App\GraphQL\Mutations;

use App\Models\Experiment;
use App\Models\Schema;
use App\Models\Demo;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class QueueUserExperiment
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
        $experiment = Experiment::findOrFail($args['experiment_id']);
        $schema = isset($args['schema_id']) ? Schema::findOrFail($args['schema_id']) : null;
        $demo = isset($args['demo_id']) ? Demo::findOrFail($args['demo_id']) : null;

        return app(\App\Actions\QueueUserExperiment::class)
            ->execute($experiment, $args['input'][0]['script_name'], $args['input'][0]['input'], $schema, $demo);
    }
}
