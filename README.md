# controllr

Controllr provides a way to remotely control your devices, be they a desktop computer, a tablet, or a phone. Controllr contains three or more components: the server, the sending device, and the receiving device.

The server acts as a command broker. It accepts commands from a sender, and issues commands to a receiver. To be more exact, it supplies commands to a receiver upon request, as it has no push capability.

When a sender issues a command for a receiver to run, the command goes into a queue for that receiver. At some point, the receiver will request the queue of commands from the server, and then run those commands, sending data back to the server which the sender can then read (if it so chooses).

This application does not give full command-line access to a receiver. The receiver only executes pre-defined commands, and can refuse to execute any command for any reason.

The server is written in PHP. There are multiple options for the receiver. Currently in development are receivers in `BASH`, `PHP`, `Python`, and Tasker on Android. Senders can be written in any language (currently in development: `PHP`, `Python`, and Tasker), or `curl` can be used on the Unix command-line.

## Just some notes for now

The path should be /{action}/{receiver-id}/{action-id}, in general.

Authentication should go like this:

Each client (sender/receiver) will have a randomly generated client id and client secret. When a request is made, an Authorization header should be added. The key given will be a base64-encoded version of the following, appended together: the client id, the client secret, and the request body.

So, if the client id is 2, and the client secret is bob, and the request is `{"status":"in progress"}`, then the key passed would be `2bob{"status":"in progress"}`, which would then be base64-encoded, so the final header would be `Authorization: Bearer MmJvYnsic3RhdHVzIjoiaW4gcHJvZ3Jlc3MifQ==`. If there is no request body (for a `GET` or `DELETE`, e.g.), then only the id and secret are used.

Some request examples:

* `POST /commands/foobar`   
  This would create a new command for `foobar`. The server would respond with `202 Accepted` and the response body would contain the original body, with an additional id, and a secret `key`. This should be used as authentication somehow in subsequent requests for the same command.
* `GET /commands/foobar/123`   
  After a successful POST, this would check the status of command `123` on `foobar`.
  - If the command was still unprocessed by `foobar`, the server would respond with `200 OK` and the response body would contain the original `POST`, with a `status` of `enqueued`. At this point, the client can still send `DELETE /commands/foobar/123` and remove the command. The client can also send a `PUT /commands/foobar/123` with different data. Both `DELETE` and `PUT` would need to be authenticated.
  - If the command is currently being processed by `foobar`, the server would respond with `200 OK` and the response body would  contain the original `POST`, with a `status` of `in progress`.. It's too late, at this point, to delete or alter the command.
  - If the command has been run by `foobar`, the server will respond with `303 See Other` with a `Location` header such as `/output/foobar/123`.
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

### `PATCH`es.

The `PATCH` sent by each receiver is a simple key-value pair. It should include a `status` and optionally, an `eta` if `status` is `in progress`. It should look like this:

    {
	    "status": "in progress",
		"eta": 30
	}

