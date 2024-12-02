<?php
class News_Taxonomies {
    public function __construct() {
        add_action('init', [$this, 'register_news_taxonomies']);
    }

    public function register_news_taxonomies() {
        $args = [
            'hierarchical' => true,
            'label' => 'Categories',
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'news-category'],
        ];

        register_taxonomy('news_category', 'news', $args);
    }   
}
