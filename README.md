# Bashilbers\Messagng is a work queue library letting you run tasks in background using a generic abstraction

Its intent is to be compatible with classic work queue solutions (RabbitMQ, Beanstalkd, â€¦) while offering a high level abstraction.

Current adapter implementations:

- [memory](doc/inMemory.md)
- [RabbitMQ](docs/rabbitMQ.md)
- [Beanstalkd](docs/beanstalkd.md)
- [Mysql](docs/mysql.md)

Extended documentation / guides:
- [Listening to events](doc/events.md)

## todo
Replace the `EventDispatcherTrait` on the Dispatcher with something more stable, like `symfony/event-dispatcher`.
Let's use docker instead of vagrant, because that's what all the cool kids do!
Install rabbitmq, beanstalk etc as service in travisCI
Write makefile
Allow a worker to handle multiple messages at the same time by using generators?
Extract the `serialize` out of messages..? (separation of concerns)
Rename `dispatcher` to `producer`.. it should help with creating and sending messages for the given adapter
Rename `Worker` to `consumer`.. it's only concern is to receive messages and pass it on to 1 or more workers
Messages should be enriched with metadata e.g. time of creation, ip address etc..
Add processing middleware so you can get automatic logging, retry etc on your consumer and producer

Write adapters for the following queues:
- AmazonSQS
- IronMQ
- Memcache
- MongoDB
- Resque
- Windows Azure service bus
- ZeroMQ
- Gearman

## Testing

Some functional tests need external programs like RabbitMQ or Beanstalkd. For practical reasons,
you can boot a VM very quickly using Vagrant and the included configuration.
You can then run the tests in the VM:

```shell
$ vagrant up
$ vagrant ssh
$ cd /vagrant
$ composer install
$ make test
```