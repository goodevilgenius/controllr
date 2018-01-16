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
     * Checks that the request's client slug matches the given slug.
     *
     * For routes accessible by the receiver, check the slug in the route matches.
     * For routes accessible by the sender, check the slug of the command's sender.
     *
     * @param string $slug
     */
    protected function checkClient(string $slug): void
    {
        logger("checking that {$this->client->slug} is $slug");
        if ($this->client->slug != $slug) abort(403);
    }

    /**
     * Checks a command if the client is the sender.
     *
     * @param string $slug
     * @param int    $id
     * @return Command
     */
    protected function checkCommandSender(string $slug, int $id): Command
    {
        if ($this->client->type != 'sender') abort(403);
        $command = Command::findOrFail($id);
        $this->checkClient($command->sender->slug);
        if ($command->receiver->slug != $slug) abort(404);

        return $command;
    }

    /**
     * Checks a command if the client is the receiver.
     *
     * @param string $slug
     * @param int    $id
     * @return Command
     */
    protected function checkCommandReceiver(string $slug, int $id): Command
    {
        if ($this->client->type != 'receiver') abort(403);
        $command = Command::findOrFail($id);
        $this->checkClient($slug);
        if ($command->receiver->slug != $slug) abort(404);

        return $command;
    }

    /**
     * Used by $slug to get its own list of Commands
     *
     * @param  string $slug
     * @return Collection
     */
    public function index(string $slug): Collection
    {
        $this->checkClient($slug);
        return $this->client->commands;
    }

    /**
     * Used by $slug to get the next command in its queue.
     *
     * @param  string $slug
     * @return Command
     */
    public function next(string $slug): Command
    {
        $this->checkClient($slug);
        $command = $this->client->commands()->where(['status' => 'enqueued'])->first();
        if (empty($command)) $command = $this->client->commands()->where('status', '!=', 'complete')->first();
        return $command;
    }

    /**
     * Adds a new command for $slug
     *
     * @param  string $slug
     * @return Command
     */
    public function store(string $slug): Command
    {
        if ($this->client->type != 'sender') abort(403);
        // @todo Add ability for receivers to specify allowed senders

        $receiver = Client::where(['type' => 'receiver', 'slug' => $slug])->firstOrFail();
        $command = $receiver->commands()->make($this->request->request->all());
        $command->sender_id = $this->client->id;
        $command->save();

        return $command;
    }

    /**
     * Returns a command. This is used by the sender to check the status.
     *
     * @param  string $slug
     * @param  int $id
     * @return Command
     */
    public function show(string $slug, int $id): Command
    {
        $command = $this->checkCommandSender($slug, $id);

        return $command;
    }

    /**
     * Sender updates a command.
     *
     * @param  string $slug
     * @param  int $id
     * @return Command
     */
    public function replace(string $slug, int $id): Command
    {
        $command = $this->checkCommandSender($slug, $id);
        $command->update($this->request->request->all());

        return $command;
    }

    /**
     * Sender deletes a command.
     *
     * @param  string $slug
     * @param  int $id
     * @return Response
     */
    public function destroy(string $slug, int $id): Response
    {
        $command = $this->checkCommandSender($slug, $id);
        $command->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Receiver updates a command.
     *
     * @param  string $slug
     * @param  int $id
     * @return Command
     */
    public function update(string $slug, int $id): Command
    {
        $command = $this->checkCommandReceiver($slug, $id);
        $command->update($this->request->request->all());

        return $command;
    }
}
