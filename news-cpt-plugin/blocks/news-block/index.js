// blocks/news-block/index.js
import { registerBlockType } from '@wordpress/blocks';
import { useSelect } from '@wordpress/data';
import { Spinner } from '@wordpress/components';
import { RichText } from '@wordpress/editor';

// Register the block
registerBlockType('news-plugin/news-block', {
    title: 'News Block',
    description: 'A block to display latest news posts.',
    category: 'common',
    icon: 'megaphone',
    edit: () => {
        // Fetch the latest 'news' posts using the WordPress data store
        const posts = useSelect((select) => {
            return select('core').getEntityRecords('postType', 'news', { per_page: 5 });
        }, []);

        if (!posts) {
            return <Spinner />;
        }

        return (
            <div className="news-block">
                <h3>Latest News</h3>
                <ul>
                    {posts.length === 0 ? (
                        <li>No news posts found.</li>
                    ) : (
                        posts.map((post) => (
                            <li key={post.id}>
                                <RichText.Content tagName="h4" value={post.title.rendered} />
                            </li>
                        ))
                    )}
                </ul>
            </div>
        );
    },
    save: () => {
        // Save is handled dynamically by WordPress
        return null;
    },
});
