<?php

class News_Shortcode {
    public function __construct() {
        add_shortcode('display_news', [$this, 'display_news']);
    }

    public function display_news($atts) {
        // Set default attributes
        $atts = shortcode_atts([
            'posts_per_page' => 5,
        ], $atts, 'display_news');
    
        $args = [
            'post_type' => 'news',
            'posts_per_page' => $atts['posts_per_page'], // Use the passed attribute
        ];
    
        $query = new WP_Query($args);
    
        $output = '<div class="news-posts">';
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $output .= '<div class="news-post">';
                $output .= '<h2>' . get_the_title() . '</h2>';
                $output .= '<p>' . get_the_excerpt() . '</p>';
                $output .= '</div>';
            }
        } else {
            $output .= '<p>No news posts found.</p>';
        }
        wp_reset_postdata();
        $output .= '</div>';
    
        return $output;
    }
}
