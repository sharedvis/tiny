# Resftful Query Builder

This is basic function for building query for Resftful model.

## Resftful Relation

You can use this class for load relation for current data, but you still need to define the relation on your `initialize` function inside your extended ResftfulModel class Model. When you want to include the relation data you can either only give the name of relation :

```php
$model = MyModel::findFirstById($id);

// Assign include relation and sub_relation by give relation key
$model->with(['relation' => ['sub_relation'], 'relation', 'relation']);
```

or, if you want to give some conditional to your relation you could use `Tiny\ResftfulRelation` to define your relation :

```php
$model = MyModel::findFirstById($id);

// Assign include relation and sub_relation by give relation key
$model->with([
    'relation' => ['sub_relation'],
    \Tiny\ResftfulRelation::init(
        'relation-name',
        [
            'conditions' => 'some_key = :param:',
            'bind' = [
                'param' => 'some_value'
            ]
        ],
        ['sub-relation']
    )
]);
```

example
```php
$rfq = Rfq::findFirstById(1);
$company_product_id = 5;
$data = $rfq->with([
    RestfulRelation::init(
        'rfq_items',
        [
            'conditions' => "company_product_id = $company_product_id",
        ]
    )
])->toArray();
dd($data);
```
**Note: This relation function could not work with search engine query.**
