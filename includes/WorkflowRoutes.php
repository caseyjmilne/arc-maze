<?php

namespace ARC\Maze;

class WorkflowRoutes
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerRoutes()
    {
        register_rest_route('arc-maze/v1', '/workflows', [
            'methods' => 'GET',
            'callback' => [$this, 'getWorkflows'],
            'permission_callback' => [$this, 'checkPermission']
        ]);

        register_rest_route('arc-maze/v1', '/workflows/(?P<name>[a-zA-Z0-9_-]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'getWorkflow'],
            'permission_callback' => [$this, 'checkPermission']
        ]);

        register_rest_route('arc-maze/v1', '/transition', [
            'methods' => 'POST',
            'callback' => [$this, 'transition'],
            'permission_callback' => [$this, 'checkPermission']
        ]);

        register_rest_route('arc-maze/v1', '/message', [
            'methods' => 'POST',
            'callback' => [$this, 'handleMessage'],
            'permission_callback' => [$this, 'checkPermission']
        ]);
    }

    public function getWorkflows($request)
    {
        $maze = Maze::getInstance();
        return rest_ensure_response($maze->getAllWorkflows());
    }

    public function getWorkflow($request)
    {
        $name = $request->get_param('name');
        $maze = Maze::getInstance();
        $workflow = $maze->getWorkflow($name);

        if (!$workflow) {
            return new \WP_Error('workflow_not_found', 'Workflow not found', ['status' => 404]);
        }

        return rest_ensure_response($workflow);
    }

    public function transition($request)
    {
        $entity = $request->get_param('entity');
        $fromState = $request->get_param('from_state');
        $toState = $request->get_param('to_state');
        $context = $request->get_param('context') ?? [];

        $maze = Maze::getInstance();
        $result = $maze->transition($entity, $fromState, $toState, $context);

        if (is_wp_error($result)) {
            return $result;
        }

        return rest_ensure_response($result);
    }

    public function handleMessage($request)
    {
        $message = $request->get_param('message');

        if (empty($message)) {
            return new \WP_Error('empty_message', 'Message cannot be empty', ['status' => 400]);
        }

        // TODO: Implement Anthropic API call here

        return rest_ensure_response([
            'success' => true,
            'message' => 'Message received successfully'
        ]);
    }

    public function checkPermission($request)
    {
        return current_user_can('manage_options');
    }
}
