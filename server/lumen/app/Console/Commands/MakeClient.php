<?php namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;

class MakeClient extends Command
{
    protected $signature = 'controllr:make:client ' .
                         '{--slug= : Slug for the client}' .
                         '{--sender} {--receiver}';

    protected $description = 'Makes a new Client on this controllr';

    /**
     * {@inheritdoc}
     */
    public function handle(): int
    {
        $this->line('Making new client');

        $slug = $this->option('slug');

        if (empty($slug)) {
            $slug = $this->ask('Slug (name) for this client?');
        }

        $slug = trim($slug);

        if (empty($slug)) {
            $this->error('Invalid slug provided');
            return 1;
        }

        $kind = $this->option('sender') ? 'sender' :
              ($this->option('receiver') ? 'receiver' :
               $this->choice('Sender or Receiver?', ['sender', 'receiver'])
              );

        $client = Client::create(['slug' => $slug, 'kind' => $kind]);

        if (empty($client) || $client->isDirty()) {
            $this->error('Failed to create new client');
            return 1;
        }

        $this->line("Made {$client->kind} {$client->slug}");
        $this->line("Secret: {$client->secret}");

        return 0;
    }
}
