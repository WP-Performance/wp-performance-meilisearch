<?php

namespace WPPerfomance\Search\Inc;

use function WPPerfomance\Search\wp_perf_post_to_record;

if (!(defined('WP_CLI') && \WP_CLI)) {
    return;
}

class WP_perf_Search_Command
{

    public function update_filterable()
    {
        $search = SearchClient::getInstance();
        $index = $search->index(\WPPerfomance\Search\wp_perf_search_index_name());

        $index->updateFilterableAttributes(['tags', 'categories']);
        \WP_CLI::success("filterable updated in Meilisearch");
    }

    public function reindex_post($args, $assoc_args)
    {

        $search = SearchClient::getInstance();
        $index = $search->index(\WPPerfomance\Search\wp_perf_search_index_name());

        $index->deleteAllDocuments();

        $paged = 1;
        $count = 0;

        do {
            $posts = new \WP_Query([
                'posts_per_page' => 100,
                'paged' => $paged,
                'post_type' => \WPPerfomance\Search\getPostTypes(),
            ]);
            if (!$posts->have_posts()) {
                break;
            }

            $records = [];

            foreach ($posts->posts as $post) {
                if (!empty($assoc_args['verbose'])) {
                    \WP_CLI::line('Serializing [' . $post->post_title . ']');
                }
                $record = (array) wp_perf_post_to_record($post);
                $records[] = $record;
                $count++;
            }

            if (!empty($assoc_args['verbose'])) {
                \WP_CLI::line('Sending batch');
            }

            $index->addDocuments($records);

            $paged++;
        } while (true);

        \WP_CLI::success("$count posts indexed in Meilisearch");
    }
}


\WP_CLI::add_command('meilisearch', __NAMESPACE__ . '\WP_perf_Search_Command');
