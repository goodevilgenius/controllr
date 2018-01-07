<?php namespace App\Console\Commands;

use App\Models\Receiver;
use Illuminate\Console\Command;

class MakeReceiver extends Command
{
    protected $signature = 'controllr:make:receiver ' .
                         '{--slug= : Slug for the receiver}';

    protected $description = 'Makes a new Receiver on this controllr';

    /**
     * {@inheritdoc}
     */
    public function handle(): int
    {
        $this->line('Making new receiver');

        $slug = $this->option('slug');

        if (empty($slug)) {
            $slug = $this->ask('Slug (name) for this receiver?');
        }

        $slug = trim($slug);

        if (empty($slug)) {
            $this->error('Invalid slug provided');
            return 1;
        }

        $receiver = Receiver::create(['slug' => $slug]);

        if (empty($receiver) || $receiver->isDirty()) {
            $this->error('Failed to create new receiver');
            return 1;
        }

        $this->line("Made receiver {$receiver->slug}");

        return 0;
    }
}
