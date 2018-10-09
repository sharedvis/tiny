# Search Engine Library

This is simple documentation on how to use *Tiny Search Engine Library*, basically this library only wrapper for another library, just like an interface, so it'll allow you to change to any search engine technology.

This library only tested on Phalcon 3.x.x, so if there is any concern or any question how to use it in another framework please open issue or you can give contribution to this project.

## How to install

### Use it on your Model

You must set search engine on your model if you want to have activity to search engine from your model.

#### Elasticsearch Engine

Your model or class need to use trait [Tiny\Services\Traits\SearchEngineTraits](./src/Services/Traits/SearchEngineTraits.php).
You can define this property to your model :

```php
/**
 * Search engine whitelist column
 * any column thats not registered here will be ignored
 * when create search engine document
 * @var array
 */
protected $_search_engine_whitelist = ['col1', 'col2'];

/**
 * Search engine mapping
 * this is predefined mapping for current model data
 * @var array
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html
 */
protected $_search_engine_data_mapping = [
    'id' => ['type' => 'long'],
    'name' => ['type' => 'string']
];
```

Initialize some engine data needs :

```php
// Set search engine
$this->setSearchEngine($this->getDI()->get('searchEngine'));

// Set search and queue engine model type
$this->getSearchEngine()->setType($this->getSource()); // Type on elasticsearch will use model source value
```

## Helper

### Elastic Search Query Builder

This helper will help you to generate query that you can use to directly request to elasticsearch server, or just generate the query for passing it to another services.

See : [ElasticSearchQueryBuilder.md](ElasticSearchQueryBuilder.md).

## Troubleshooting

There is no issue raised by now.

## TODO

* Aggregations document
* Document scoring
