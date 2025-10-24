<?php

namespace ARC\Maze;

class AdminPage
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'registerMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function registerMenu()
    {
        add_menu_page(
            'ARC Maze',
            'ARC Maze',
            'manage_options',
            'arc-maze',
            [$this, 'renderPage'],
            'dashicons-randomize',
            30
        );
    }

    public function enqueueAssets($hook)
    {
        // Only enqueue on our admin page
        if ($hook !== 'toplevel_page_arc-maze') {
            return;
        }

        $asset_file = ARC_MAZE_PATH . 'apps/maze/build/index.asset.php';

        if (!file_exists($asset_file)) {
            return;
        }

        $asset = require $asset_file;

        wp_enqueue_script(
            'arc-maze-admin',
            ARC_MAZE_URL . 'apps/maze/build/index.js',
            $asset['dependencies'],
            $asset['version'],
            true
        );

        wp_enqueue_style(
            'arc-maze-admin',
            ARC_MAZE_URL . 'apps/maze/build/style-index.css',
            [],
            $asset['version']
        );

        wp_localize_script('arc-maze-admin', 'arcMaze', [
            'apiUrl' => rest_url('arc-maze/v1'),
            'nonce' => wp_create_nonce('wp_rest')
        ]);
    }

    public function renderPage()
    {
        ?>
        <div class="wrap">
            <h1>ARC Maze - Workflow Manager</h1>
            <div id="arc-maze-admin-root"></div>
        </div>
        <?php
    }
}
