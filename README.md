# Meilisearch implementation for WordPress

## WP-CLI

This plugin add a wp-cli command for save all post

```
wp meilisearch reindex_post
```

## update filterable fields

```
wp meilisearch update_filterable
```

## Hook

Update index of meilisearch when a post has added, updated or deleted

## Config

Add in wp-config.php :

```
define('MEILISEARCH_URL', 'XXX');
define('MEILISEARCH_KEY_PUBLIC', 'XXX');
define('MEILISEARCH_KEY_SECRET', 'XXX');

```

## JS

```
npm add @meilisearch/instant-meilisearch instantsearch.js
```

The vars `MEILISEARCH_URL`, `MEILISEARCH_KEY_PUBLIC` and `MEILISEARCH_APP_INDEX` are shared across the page for use in javascript script.

Example of use :

```html
<div id="searchbox"></div>
<div id="hits"></div>
<div id="tags-list"></div>
```
or pattern
```html
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:group {"anchor":"searchbox","layout":{"type":"constrained","className": "wp-search-algolia"}} -->
  <div id="searchbox" class="wp-block-group"></div>
  <!-- /wp:group -->

  <!-- wp:group {"anchor":"tag-list","layout":{"type":"constrained", "className": "tag-list"}} -->
  <div id="tag-list" class="wp-block-group"></div>
  <!-- /wp:group -->

  <!-- wp:group {"anchor":"hits","layout":{"type":"constrained", "className": "search-hits"}} -->
  <div id="hits" class="wp-block-group"></div>
  <!-- /wp:group -->
</div>
<!-- /wp:group -->
```


```js
import { instantMeiliSearch } from '@meilisearch/instant-meilisearch'
import instantsearch from 'instantsearch.js'
import { searchBox, hits, refinementList } from 'instantsearch.js/es/widgets'

const initSearch = () => {
  const search = instantsearch({
    indexName: MEILISEARCH_APP_INDEX,
    numberLocale: 'fr',
    searchClient: instantMeiliSearch(MEILISEARCH_URL, MEILISEARCH_KEY_PUBLIC),
    searchFunction(helper) {
      // Ensure we only trigger a search when there's a query
      if (helper.state.query && helper.state.query !== '') {
        document.getElementById('hits').removeAttribute('hidden')
        helper.search()
      } else {
        document.getElementById('hits').setAttribute('hidden', true)
      }
    },
  })

  search.addWidgets([
    searchBox({
      container: '#searchbox',
    }),
    refinementList({
      container: '#tag-list',
      attribute: 'tags',
      limit: 5,
      showMore: true,
    }),
    hits({
      container: '#hits',
      templates: {
        item: `
      <article>
        <a href="{{ url }}">
          <strong>
            {{#helpers.highlight}}
              { "attribute": "title", "highlightedTagName": "mark" }
            {{/helpers.highlight}}
          </strong>
        </a>
        {{#content}}
          <p>{{#helpers.highlight}}{ "attribute": "excerpt", "highlightedTagName": "mark" }{{/helpers.highlight}}</p>
        {{/content}}
      </article>
    `,
      },
    }),
  ])

  search.start()
}

export default initSearch
```

## Doc

- [https://github.com/meilisearch/instant-meilisearch](https://github.com/meilisearch/instant-meilisearch)
