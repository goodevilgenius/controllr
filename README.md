# controllr

Remote control for your devices

## Just some notes for now

The path should be /{receiver-id}/{action}/{action-id}, in general.

Some examples:

* `POST /foobar/commands/`   
  This would create a new command for `foobar`. The server would respond with
  `202 Accepted` and the response body would contain the original body, with an
  additional id, and a secret `key`. This should be used as authentication
  somehow in subsequent requests for the same command.
* `GET /foobar/commands/123`   
  After a successful POST, this would check the status of command `123` on
  `foobar`.
  - If the command was still unprocessed by `foobar`, the server would
	respond with `200 OK` and the response body would contain the original
	`POST`, with a `status` of `enqueued`. At this point, the client can still
	send `DELETE /foobar/commands/123` and remove the command. The client can
	also send a `PUT /foobar/commands/123` with different data. Both `DELETE`
	and `PUT` would need to be authenticated.
  - If the command is currently being processed by `foobar`, the server would
    respond with `200 OK` and the response body would  contain the original
	`POST`, with a `status` of `in progress`.. It's too late, at this point, to
    delete or alter the command.
  - If the command has been run by `foobar`, the server will respond with
	`303 See Other` with a `Location` header such as `/foobar/output/123`.
* `GET /foobar/queue/next`
  Returns the next command in the queue. This should be authenticated in some
  way. `foobar` needs a secret key known only to the server. The server should
  also send an additional secret key for this command.
* `GET /foobar/queue/`
  Returns the entire command queue for `foobar`. This should also be
  authenticated as described above.
* `PATCH /foobar/commands/123`
  This is used by `foobar` to update the status of command `123`. It should
  include, as authentication, `foobar`'s secret key, and the key for `123` as
  well.
  - If `foobar` is now executing `123`, it should set the `status` to `in
    progress`. If an estimate on execution time is available, it may also include
    an `eta` in number of seconds.
  - If `foobar` has completed executing `123`, it should set the `status` to
    `complete`, and should also send a `return_code`, an `output`, or both. At
    this point, the server will redirect further request for the status of this
    command to `/foobar/output/123`.

### `PATCH`es.

The `PATCH` sent by each receiver is a simple key-value pair. It should include
a `status` and optionally, an `eta` if `status` is `in progress`. If the format
is XML, the `PATCH` might look like this:

    <command>
      <status>in progress</status>
	  <eta>30</eta>
    </command>

The same in JSON, would look like this:

    {
	    "status": "in progress",
		"eta": 30
	}

