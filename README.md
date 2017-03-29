# Flow Meter

A simple tool for metering broker dependent applications

# !!! Note
This is a tool still under development. It has no proper error handling, no tests, no correctly commented
# Config example

config/config.yml
```
test:
    start:
        name: start_queue_alias_name
    end:
        name: end_queue_alias_name
        min_wait_time: 10   # How long should it wait until we can validate if the test is done
        max_wait_time: 180  # How long should it wait for the test to be over
connections:
    connection_foo:
        host: rbt-host
        port: 5672
        http_port: 15672
        user: guest
        pass: guest
        vhost: fo
    connection_bar:
        host: rbt-host
        port: 5672
        http_port: 15672
        user: guest
        pass: guest
        vhost: bar
queues:
    start_queue_alias_name:
        name: my.queue.name
        passive: true
        durable: true
        exclusive: false
        auto_delete: false
        nowait: false
        # Use connection
        connection: connection_foo
    intermediate_queue_to_monitor:
        name: my.un-cuploed.queue.name
        passive: true
        durable: true
        exclusive: false
        auto_delete: false
        nowait: false
        # Use connection
        connection: connection_foo
    end_queue_alias_name:
        name: my.queue.name
        passive: true
        durable: true
        exclusive: false
        auto_delete: false
        nowait: false
        # Use connection
        connection: connection_bar
```

input.json
```
[
    {"my_first_message_json": {"..."}},
    {"my_second_message_json": {"..."}},
    {"my_n_message_json": {"..."}}
]
```
Run
```
php bin/bootstrap.php -c config/config.yaml -i input_data.json
```