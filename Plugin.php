<?php
/**
 * Plugin Name: ARC Maze
 * Description: Workflow and state management for ARC Framework
 * Version: 1.0.0
 * Author: ARC Software Group
 * Requires PHP: 7.4
 */

namespace ARC\Maze;

if (!defined('ABSPATH')) {
    exit;
}

define('ARC_MAZE_VERSION', '1.0.0');
define('ARC_MAZE_PATH', plugin_dir_path(__FILE__));
define('ARC_MAZE_URL', plugin_dir_url(__FILE__));

// Load Composer autoloader
if (file_exists(ARC_MAZE_PATH . 'vendor/autoload.php')) {
    require_once ARC_MAZE_PATH . 'vendor/autoload.php';
}

// Load environment variables
if (class_exists('Dotenv\Dotenv')) {
    try {
        $dotenv = \Dotenv\Dotenv::createImmutable(ARC_MAZE_PATH);
        $dotenv->load();
    } catch (\Exception $e) {
        error_log('ARC Maze: Failed to load .env: ' . $e->getMessage());
    }
}

class Maze
{
    private static $instance = null;
    private static $workflows = [];

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->registerAutoloader();
        $this->loadHelpers();
        $this->init();
    }

    private function registerAutoloader()
    {
        spl_autoload_register(function ($class) {
            // Only autoload classes in our namespace
            if (strpos($class, 'ARC\\Maze\\') !== 0) {
                return;
            }

            // Remove namespace prefix
            $class = str_replace('ARC\\Maze\\', '', $class);

            // Convert namespace separators to directory separators
            $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

            // Build the file path
            $file = ARC_MAZE_PATH . 'includes' . DIRECTORY_SEPARATOR . $class . '.php';

            // If the file exists, require it
            if (file_exists($file)) {
                require_once $file;
            }
        });
    }

    private function loadHelpers()
    {
        $helpersFile = ARC_MAZE_PATH . 'includes/helpers.php';
        if (file_exists($helpersFile)) {
            require_once $helpersFile;
        }
    }

    private function init()
    {
        // Initialize workflow engine
        WorkflowEngine::init();

        // Initialize state manager
        StateManager::init();

        // Initialize REST API routes
        new WorkflowRoutes();

        // Initialize admin page
        new AdminPage();

        do_action('arc_maze_loaded');
    }

    public function registerWorkflow($name, $workflow)
    {
        self::$workflows[$name] = $workflow;
        do_action('arc_maze_workflow_registered', $name, $workflow);
        return $this;
    }

    public function getWorkflow($name)
    {
        return self::$workflows[$name] ?? null;
    }

    public function getAllWorkflows()
    {
        return self::$workflows;
    }

    public function transition($entity, $fromState, $toState, $context = [])
    {
        $workflow = $this->getWorkflow($entity);

        if (!$workflow) {
            return new \WP_Error('workflow_not_found', "Workflow for {$entity} not found");
        }

        return $workflow->transition($fromState, $toState, $context);
    }

    public function canTransition($entity, $fromState, $toState, $context = [])
    {
        $workflow = $this->getWorkflow($entity);

        if (!$workflow) {
            return false;
        }

        return $workflow->canTransition($fromState, $toState, $context);
    }

    public function getAvailableTransitions($entity, $currentState, $context = [])
    {
        $workflow = $this->getWorkflow($entity);

        if (!$workflow) {
            return [];
        }

        return $workflow->getAvailableTransitions($currentState, $context);
    }
}

Maze::getInstance();
