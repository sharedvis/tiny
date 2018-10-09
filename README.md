# Tiny

## Knowledge about Tiny

![Tiny](/tiny.jpg?raw=true "Tiny")

Tiny is a melee strength hero with powerful ganking and killing potential. Although he starts off vulnerable in lane with his pitiful mana pool and almost non-existent armor, with a few levels, he gets considerably stronger. His killing power in the early and midgame comes from combo-ing his two active abilities. Avalanche engulfs an area in a wave of stones, dealing respectable damage and stunning enemies, while Toss allows Tiny to pick up the closest unit near himself and launch it at the designated location, dealing damage to all enemies at the location as well as additional damage to the thrown unit if an enemy. It can be used to displace an ally within the enemy team, but is mostly used on enemies themselves to deliver massive damage. If chained immediately with Avalanche, it also doubles the damage the target takes from Avalanche, allowing Tiny to easily dispatch fragile enemy heroes in a matter of seconds. Although his nuking potential with his two active abilities is already considerable, his passives, Craggy Exterior and Grow turn Tiny into a formidable physical combatant as well. Craggy Exterior provides Tiny with some much-needed armor and gives him a chance to stun enemies that attack him from too close, making Tiny a potent counter to fast-attacking melee heroes. Grow increases Tiny's size and provides him with a massive boost to his attack damage, movement speed, and Toss damage, at the cost of some attack speed. Aghanim's Scepter is an almost essential item on Tiny, since it allows him to permanently equip a tree, giving him extra attack range as well as a powerful cleaving attack. Although Tiny initially lives up to his name by starting off small and weak, much like an avalanche, he quickly grows in size and strength until he becomes a hulking behemoth with enormous health and damage output. This gives Tiny the potential to become one of the strongest carries in late game situations.

*Source: http://dota2.gamepedia.com/Tiny*

## What is this?

This is SDK for [Tiny API](https://bitbucket.org/sharedvis/tusk), this SDK will handle how to send a request to API and also handle all security matters, security it self will be handled with HMAC technique.

This library is also include wrapper class for some service (elasticsearch, rabbitmq, etc), some helpers, etc.

# Installation

1. Add this to your `composer.json` file :
    ```json
    {
        "minimum-stability": "dev",
        ...
        "require": {
            ...
            "sharedvis/tiny": "dev-master"
            ...
        },
        "repositories": [
            {
                "type": "vcs",
                "url":  "git@github.com:sharedvis/tiny.git"
            }
        ]
        ...
    }
    ```

2. Run `composer update sharedvis/tiny` to install and include all required vendors.
3. Set `tiny` to your DI by adding this script :
```php
$di->set('tiny', function () use ($config) {
    return new Tiny\ApiRequest($config->tiny->toArray());
});
```

4. Add this to your `config.php` :
```php
'tiny' => [
    'app_id' => '{your_app_id}',
    'app_secret' => '{your_app_secret}',
    'host' => 'http://api.mbiz.dev',
    'hmac' => [
        'num-first-iterations' => 10,
        'num-second-iterations' => 10,
        'num-final-iterations' => 10
    ]
],
```

# How to use

All function in this library is *ONLY* tested on *Phalcon Framework*, maybe need some modification if you want to
use it on another framework.

Another separated documentation :

* [Search Engine Library](./docs/SearchEngine.md)
* [Queue Engine Library](./docs/QueueEngine.md)

## RestfulModel Class

This package could handle simple CRUD action, you just need extend your model class with class `Tiny\RestfulModel`.

### Define Model

This is simple example of your restful model :

```php
class Brands extends \Tiny\RestfulModel
{
    /**
     * Whitelist when do save or create using mass assignment
     * @var array
     */
    protected $_whitelist = [
        'name', 'description', 'created_by', 'status'
    ];

    /**
     * Construct function
     */
    public function __construct()
    {
        $this->setServiceHost('http://service.domain.url');
        $this->setServicePort('80');

        parent::__construct();
    }

    /**
     * Returns base endpoint path of current model url request
     *
     * @return string
     */
    public function getSource()
    {
        return 'base/path';
    }
}

```

For more information and options about this you could see [Tiny\ResfulModel](./src/RestfulModel.php).

#### Define Model getSource With Pattern

You can also set path with pattern in model, e.g you want to add pattern like this :

```php
class YourModel
{
    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'someof/{property_id:[0-9]+}/endpoint';
    }
}
```

Right before restful request been made, it'll get property `property_id` on current object and replace `{property_id:[0-9]+}` with the result, so after initiate the model you need to assign the property :

```php
$model = new YourModel;
$model->property_id = 123; // This will append to restful request uri

$model->find(); // The url will become http://host:port/someof/123/endpoint
```

#### Assign data for create or update in model

You can use `$model->assign(["key" => "value"])` when set data to update or create, or you can also set the property :

```php
$model = new Model;

// First sample
$model->assign([
    "column" => "value"
]);

// Second example
$model->another_column = "another value";

$model->create(); // or update
```

#### Send data request as Json

You can use `$model->sendAsJson()` when you want to send current data as JSON (this only works with `POST` or `PUT` request) :

```php
$model = new Model;

// First sample
$model->assign([
    "column" => "value"
]);

// Second example
$model->another_column = "another value";

// When using PUT or POST it'll automatically convert data to json
$model->sendAsJson();

$model->create(); // or update
```

## Restful Transaction Manager

Transaction on this class is not real "mysql transaction", its only create revert action for any CRUD action that
you do, e.g if you delete record it'll make create task that will create the record that you currently delete, so
if you are not using [Tiny\Phalcon\Behavior\SoftDelete](./src/Phalcon/Behavior/SoftDelete.php) behavior the primary
key or id for the record will be changed because practically it's another different record.

First you need to define transaction service on your phalcon DI :

```php
/**
 * Restful transaction
 */
$di->setShared('rest_transaction', function () {
    return new \Tiny\RestfulTransactionsManager(
        'redis_host_url',
        'redis_port',
        'redis_db'
    );
});
```

On your controller when you do some update or create or delete action you could do this :

```php
    $txManager = $this->rest_transaction;       // Get rest transaction manager
    $model = new Model();                       // Initiate your model
    $transaction = $txManager->get($model);     // Initiate transaction based on your model

    try {
        $model->setTransaction($transaction);   // Link your transaction to your model object,
                                                // Model should be extended class RestfulModel
        ...

        if ($model->create())
        {
            // Success create and commit data
            $transaction->commit();
        } else {
            // Failed to create, do rollback action
            $transaction->rollback();
        }

    } catch (\Exception $e) {
        // Something goes wrong, do rollback action
        $transaction->rollback();
    }
```

## SoftDelete Behavior

Add this on your model `initialize` function for handle request from restful model :

```php
    // Soft delete behavior
    $this->addBehavior(
        new \Tiny\Phalcon\Behavior\SoftDelete(
            array(
                'field' => 'status',    // Target field
                'value' => '0'          // Deleted status value
            )
        )
    );
```

Note : Your model should be extend `\Phalcon\Mvc\Model`

## SoftCreate Behavior

Add this on your model `initialize` function for handle request from restful model :

```php
    // Soft delete behavior
    $this->addBehavior(
        new \Tiny\Phalcon\Behavior\SoftCreate(
            array(
                'field' => 'status',    // Target field
                'value' => '0'          // Default status value when revert value if status field is not sent
            )
        )
    );
```

Note : Your model should be extend `\Phalcon\Mvc\Model`

## By DI Service

If you already set `tiny` to your DI then you can freely call the class all over the controller.

### GET Request

How to send HTTP GET request :

```php
// Get tiny from DI
$tiny = $this->getDI()->get('tiny');

$tiny->sendGet('/endpoint', $data = array());
```

### POST Request

How to send HTTP GET request :

```php
// Get tiny from DI
$tiny = $this->getDI()->get('tiny');

$tiny->sendPost('/endpoint', $data = array());
```

### PUT Request

How to send HTTP GET request :

```php
// Get tiny from DI
$tiny = $this->getDI()->get('tiny');

$tiny->sendPut('/endpoint', $data = array());
```

### DELETE Request

How to send HTTP GET request :

```php
// Get tiny from DI
$tiny = $this->getDI()->get('tiny');

$tiny->sendDelete('/endpoint', $data = array());
```

### User Authentication

Special for this endpoint it has its own function, so the SDK will catch the token and save it to session, and if the token exists it'll be automatically included to every request header.

```php
// Get tiny from DI
$tiny = $this->getDI()->get('tiny');

$tiny->userAuth($user_email, $user_password);
```

# TODO

* Testing

# Issues

If you find any issue please open a ticket at [Issues](https://github.com/sharedvis/tiny/issues).

# License

This is only for private use in MBiz project.
