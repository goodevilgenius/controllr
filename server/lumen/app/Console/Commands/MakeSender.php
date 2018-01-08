<?php namespace App\Console\Commands;

use App\Models\Sender;
use Illuminate\Console\Command;

class MakeSender extends Command
{
    protected $signature = 'controllr:make:sender ' .
                         '{--slug= : Slug for the sender}';

    protected $description = 'Makes a new Sender on this controllr';

    /**
     * {@inheritdoc}
     */
    public function handle(): int
    {
        $this->line('Making new sender');

        $slug = $this->option('slug');

        if (empty($slug)) {
            $slug = $this->ask('Slug (name) for this sender?');
        }

        $slug = trim($slug);

        if (empty($slug)) {
            $this->error('Invalid slug provided');
            return 1;
        }

        $sender = Sender::create(['slug' => $slug]);

        if (empty($sender) || $sender->isDirty()) {
            $this->error('Failed to create new sender');
            return 1;
        }

        $this->line("Made sender {$sender->slug}");

        return 0;
    }
}
