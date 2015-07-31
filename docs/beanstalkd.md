# Using Beanstalkd

The Beanstalkd adapter uses the [Pheanstalk](https://github.com/pda/pheanstalk) library.

You need to require the library in composer:

```json
{
    "require": {
        "pda/pheanstalk": "~2.0"
    }
}
```

On your backend:

```php
// Connect to the Beanstalkd server
$connection = new Pheanstalk_Pheanstalk('127.0.0.1');
// Use the following tube
$tube = 'my_tube';

$workDispatcher = new \Messaging\Beanstalkd\Dispatcher($connection, $tube);

// Run a task in background
$message = new MyMessage();
$workDispatcher->send($message);
```

On the worker side (this script is meant to be run continuously as a deamon):

```php
// Connect to the beanstalkd server
$connection = new Pheanstalk_Pheanstalk('127.0.0.1');
// Use the following tube
$tube = 'my_tube';

$worker = new \Messaging\Beanstalkd\Worker($connection, $tube);
$worker->registerMessageHandler('MyMessage', new MyTaskExecutor());

// Execute tasks
$worker->work();
```