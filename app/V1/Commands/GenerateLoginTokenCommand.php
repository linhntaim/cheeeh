<?php

namespace App\V1\Commands;

use App\V1\Models\SysToken;

class GenerateLoginTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:login_token {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Login Token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sysToken = SysToken::create([
            'type' => SysToken::TYPE_LOGIN,
        ]);
        $this->warn(json_encode([
            'token' => $sysToken->token,
            'id' => $this->argument('user'),
        ]));
    }
}
