# Message Queue Library

This is simple documentation on how to use *Tiny Message Queue Library*, basically this library only wrapper for another library, just like an interface, so it'll allow you to change to any queue technology.

This library only tested on Phalcon 3.x.x, so if there is any concern or any question how to use it in another framework please open issue or you can give contribution to this project.

## How to install

### Put Service on DI

You need to add di service like this on your phalcon DI :

```php
$di->setShared('queue', function() use ($config) {
    $connection = new \Tiny\Services\Queue('queue_engine',
        [
            'host' => 'host_address',
            'port' => 1233,
            'username' => 'user',
            'password' => 'pass',
        ]
    );

    return $connection;
});
```

Supported `queue_engine` value :

* `rabbitmq` for RabbitMQ.

### Use it on your Model

#### RabbitMQ Engine

Initialize some engine data needs :

```php
// Set engine model type
$this->getDI()->get('queue')->getConnection()->setType($this->getSource()); // Queue engine will also include type on every data sent

// Set queue model caller
$this->getDI()->get('queue')->setModelCaller($this);
```

## Troubleshooting

There is no issue raised by now.

## TODO

* `¯\_(ツ)_/¯`
