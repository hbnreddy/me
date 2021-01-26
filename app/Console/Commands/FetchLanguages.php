<?php

namespace App\Console\Commands;

use App\Eloquent\Currency;
use App\Eloquent\Language;
use App\Http\Api\Musement\MusementApi;
use Illuminate\Console\Command;

class FetchLanguages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:languages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch languages from Musement API and Sygic API.';

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
     *
     * @return mixed
     */
    public function handle()
    {
        $sygicSupportedLanguages = [
            'ar', 'cs', 'da', 'de', 'el', 'en', 'es', 'fi', 'fr', 'he', 'hu',
            'it', 'ja', 'ko', 'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'sk', 'sv',
            'th', 'tr', 'uk', 'zh',
        ];

        $musementApi = resolve(MusementApi::class);

        $languages = json_decode($musementApi->getLanguages(), true);

        Language::query()->delete();
        foreach ($languages as $language) {
            if (in_array($language['code'], $sygicSupportedLanguages)) {
                Language::query()
                    ->create($language);
            }
        }
    }
}
