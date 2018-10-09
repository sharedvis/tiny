# Elasticsearch Query Builder

## Functions

### orWhere

Add `where` conditions with `or` operator :

```php
$queryBuilder = new \Tiny\Services\Helper\ElasticSearch\QueryBuilder;

$queryBuilder->orWhere(string $column, string $value);
```

### andWhere

Add `where` conditions with `and` operator :

```php
$queryBuilder = new \Tiny\Services\Helper\ElasticSearch\QueryBuilder;

$queryBuilder->andWhere(string $column, string $value);
```

### inWhere

Add `where in` conditions :

```php
$queryBuilder = new \Tiny\Services\Helper\ElasticSearch\QueryBuilder;

$queryBuilder->inWhere(string $column, array $condition);
```

### rangeWhere

Add `where between` conditions :

```php
$queryBuilder = new \Tiny\Services\Helper\ElasticSearch\QueryBuilder;

$queryBuilder->rangeWhere(string $column, mixed $from, mixed $to, $operator = 'and')
```

### order

Add result order sorting :

```php
$queryBuilder = new \Tiny\Services\Helper\ElasticSearch\QueryBuilder;

$queryBuilder->order(string $column, $order = 'asc');
```

### toArray

You can convert all query that you already added to array, so you can send it through Resftful request
to another services :

```php
$queryBuilder = new \Tiny\Services\Helper\ElasticSearch\QueryBuilder;

$queryBuilder->order(string $column, $order = 'asc');
$queryBuilder->andWhere(string $column, string $value);

$queryArray = $queryBuilder->toArray();
```

### processToElastic

This functions is to process the query that already added or process an array that sent from another service, it'll return an array contain an object instance of `\Elastica\Query\*` e.g `\Elastica\Query\QueryString` and an array for elastic arguments:

Directly process the query :

```php
$queryBuilder = new \Tiny\Services\Helper\ElasticSearch\QueryBuilder;

$queryBuilder->order(string $column, $order = 'asc');
$queryBuilder->andWhere(string $column, string $value);

list($queryObject, $queryArguments) = $queryBuilder->processToElastic();
```

From an array that passed from another services :

```php
$queryBuilder = new \Tiny\Services\Helper\ElasticSearch\QueryBuilder;

// You need to set your search engine
$searchEngine = $model->getSearchEngine()->getConnection();

// Set limit per page
$searchEngine->setLimit(10);

// Get query that sent from another service
$query = $this->request->getQuery('query', null, []); // Query must be an array

list($queryObject, $queryArguments) = $queryBuilder->processToElastic($query);
$results = $searchEngine->find($queryObject, (int) $page, $queryArguments);

// Check if the result is not empty
if ($results->count())
{
    foreach ($results as $result)
    {
        // Get single row data from document
        $data[] = $result->getData();
    }
}

// Get pagination
$pagination = $searchEngine->getPagination();
```
