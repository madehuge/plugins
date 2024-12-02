<?php
class News_Post_Type {
    public function __construct() {
        add_action('init', [$this, 'register_news_post_type']);
    }

    public function register_news_post_type() {
        $args = [
            'public' => true,
            'label'  => 'News',
            'supports' => ['title', 'editor', 'thumbnail'],
            'has_archive' => true,
            'rewrite' => ['slug' => 'news'],
            'show_ui' => true, // Ensure the custom post type is shown in the admin
            'show_in_menu' => true, // Ensure it's shown in the sidebar menu
            'menu_icon' => 'dashicons-admin-post', // Optional: Set a custom icon for the menu
            'menu_position' => 5, // Optional: Position in the sidebar (5 is the default for posts)
            'capability_type' => 'post', // Optional: Define capabilities (default is 'post')
            'show_in_rest' => true, // Important for Gutenberg compatibility
        ];
        
        register_post_type('news', $args);
    }
}
