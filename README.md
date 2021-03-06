# controllr

**This project is no longer under development.**

**After working on this for a while, I decided that I was just reinventing the wheel. This is basically no different than a million different message-broker applications.**

**I'm just leaving this here for posterity, I suppose.**

Controllr provides a way to remotely control your devices, be they a desktop computer, a tablet, or a phone. Controllr contains three or more components: the server, the sending device, and the receiving device.

The server acts as a command broker. It accepts commands from a sender, and issues commands to a receiver. To be more exact, it supplies commands to a receiver upon request, as it has no push capability.

When a sender issues a command for a receiver to run, the command goes into a queue for that receiver. At some point, the receiver will request the queue of commands from the server, and then run those commands, sending data back to the server which the sender can then read (if it so chooses).

This application does not give full command-line access to a receiver. The receiver only executes pre-defined commands, and can refuse to execute any command for any reason.

The server is written in PHP. There are multiple options for the receiver. Currently in development are receivers in `BASH`, `PHP`, `Python`, and Tasker on Android. Senders can be written in any language (currently in development: `PHP`, `Python`, and Tasker), or `curl` can be used on the Unix command-line.

## Deploy the server

The server is built using [Lumen](http://lumen.laravel.com/), which, in turn, is based on [Laravel](http://laravel.com/). It can be deployed to any host that supports PHP7, and can use multiple database types. Deployment via [Heroku](https://www.heroku.com/) is recommended. The `Makefile` includes steps in deploy to heroku, and to set environment variables. There are instructions for deploying [Laravel on Heroku](https://devcenter.heroku.com/articles/getting-started-with-laravel) that are a good basis to start.

## Interacting with the server

### Authentication

Each client (sender/receiver) will have a randomly generated client id and client secret. When a request is made, an Authorization header should be added. The key given will be a base64-encoded version of the following, appended together: the client id, the client secret, and the request body, separated by a pipe (`|`).

So, if the client id is 2, and the client secret is bob, and the request is `{"status":"in progress"}`, then the key passed would be `2|bob|{"status":"in progress"}`, which would then be base64-encoded, so the final header would be `Authorization: Bearer Mnxib2J8eyJzdGF0dXMiOiJpbiBwcm9ncmVzcyJ9`. If there is no request body (for a `GET` or `DELETE`, e.g.), then only the id and secret are used.

### Routes

#### Sender Routes

* `POST /commands/foobar`   
  This would create a new command for `foobar`. The server would respond with `202 Accepted` and the response body would contain the original body, with an additional id, and a secret `key`. This should be used as authentication somehow in subsequent requests for the same command.
* `GET /commands/foobar/123`   
  After a successful POST, this would check the status of command `123` on `foobar`.
  - If the command was still unprocessed by `foobar`, the server would respond with `200 OK` and the response body would contain the original `POST`, with a `status` of `enqueued`. At this point, the command can still be modified or deleted.
  - If the command is currently being processed by `foobar`, the server would respond with `200 OK` and the response body would  contain the original `POST`, with a `status` of `in progress`.. It's too late, at this point, to delete or alter the command. It may also include an `eta` in seconds.
  - If the command has been run by `foobar`, the status will be `complete`. There will be either an `output`, `status_code`, or both.
* `PUT /commands/foobar/123`   
  - The sender can modify the command, if it's not yet in progress.
* `DELETE /commands/foobar/123`    

#### Receiver Routes

* `GET /commands/foobar/next`
  Returns the next command in the queue. This should be authenticated in some way. `foobar` needs a secret key known only to the server. The server should also send an additional secret key for this command.
* `GET /commands/foobar`
  Returns the entire command queue for `foobar`. This should also be authenticated as described above.
* `PATCH /commands/foobar/123`
  This is used by `foobar` to update the status of command `123`. It should include, as authentication, `foobar`'s secret key, and the key for `123` as well.
  - If `foobar` is now executing `123`, it should set the `status` to `in progress`. If an estimate on execution time is available, it may also include an `eta` in number of seconds.
  - If `foobar` has completed executing `123`, it should set the `status` to `complete`, and should also send a `return_code`, an `output`, or both. At this point, the server will redirect further request for the status of this command to `/output/foobar/123`.
  - If `foobar` has decided not to execute the command at this time, it should set the `status` to `postponed`. This might occur if `foobar` has receivers in multiple languages, and the command is only valid for a different language (it might be a `Python` command, but currently the `PHP` receiver is running).
  - If `foobar` refuses to execute `123` (e.g., it's an invalid command), it should set the `status` to `refused`. Optionally, `output` may include a reason for the refusal (e.g., "invalid command" or "incomplete input").
