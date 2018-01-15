<?php namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommandController extends Controller
{
    protected $request;
    protected $client;

    /**
     * Constructor.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->client = $this->request->user();
    }

    /**
     * Checks that the request's client name matches the given name.
     *
     * For routes accessible by the receiver, check the name in the route matches.
     * For routes accessible by the sender, check the name of the command's sender.
     *
     * @param string $name
     */
    protected function checkClient(string $name): void
    {
        if ($this->client->name != $name) abort(403);
    }

    /**
     * Used by $name to get its own list of Commands
     *
     * @param  string $name
     * @return Collection
     */
    public function index(string $name): Collection
    {
        $this->checkClient($name);
        return $this->client->commands;
    }

    /**
     * Used by $name to get the next command in its queue.
     *
     * @param  string $name
     * @return Command
     */
    public function next(string $name): Command
    {
        $this->checkClient($name);
        $command = $this->client->commands()->where(['status' => 'enqueued'])->first();
        if (empty($command)) $command = $this->client->commands()->where('status', '!=', 'complete')->first();
        return $command;
    }

    /**
     * Adds a new command for $name
     *
     * @param  string $name
     * @return Command
     */
    public function store(string $name): Command
    {
        if ($this->client->type != 'sender') abort(403);
        // @todo Add ability for receivers to specify allowed senders

        $receiver = Client::where(['type' => 'receiver', 'name' => $name])->firstOrFail();
        $command = $receiver->commands()->make($this->request->request->all());
        $command->sender_id = $this->client->id;
        $command->save();

        return $command;
    }

    /**
     * Returns a command. This is used by the sender to check the status.
     *
     * @param  string $name
     * @param  int $id
     * @return Command
     */
    public function show(string $name, int $id): Command
    {
        if ($this->client->type != 'sender') abort(403);
        $command = Command::findOrFail($id);
        $this->checkClient($command->sender->name);

        return $command;
    }

    public function replace(string $name, int $id): Command
    {
        return "replacing command $id for $name";
    }

    public function destroy(string $name, int $id): Response
    {
        return "deleting command $id for $name";
    }

    public function update(string $name, int $id): Command
    {
        return "$name is updating command $id";
    }
}
