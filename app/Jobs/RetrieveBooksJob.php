<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Book;

class RetrieveBooksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $startIndex;
    protected $maxResults = 40;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($startIndex = 0)
    {
        $this->startIndex = $startIndex;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', 'https://www.googleapis.com/books/v1/volumes', [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'query' => [
                'q' => 'subject:fiction',
                'startIndex' => $this->startIndex,
                'maxResults' => $this->maxResults,
                'key' => config('api-resources.google-api-key'),
            ]
        ]);

        logger()->info('Retrieved books from Google API');
        logger()->info('startIndex: ' . $this->startIndex);
        logger()->info('maxResults: ' . $this->maxResults);

        $body = json_decode($response->getBody());

        if($body && $response->getStatusCode() == 200) {

            foreach($body->items as $item) {

                // if(!empty($item->id)) {
                    Book::updateOrCreate([
                        'uuid' => $item->id,
                    ], [
                        'title' => $item->volumeInfo->title,
                        'isbn_10' => !empty($item->volumeInfo->industryIdentifiers[0]->identifier) ? $item->volumeInfo->industryIdentifiers[0]->identifier : null,
                        'isbn_13' => !empty($item->volumeInfo->industryIdentifiers[1]->identifier)? $item->volumeInfo->industryIdentifiers[1]->identifier : null,
                        'page_count' => !empty($item->volumeInfo->pageCount) ? $item->volumeInfo->pageCount : null,
                        'authors' => !empty($item->volumeInfo->authors) ? json_encode($item->volumeInfo->authors) : null,
                        'description' => !empty($item->volumeInfo->description) ? $item->volumeInfo->description : null,
                        'cover_url' => !empty($item->volumeInfo->imageLinks->thumbnail) ? $item->volumeInfo->imageLinks->thumbnail : null,
                        'small_cover_url' => !empty($item->volumeInfo->imageLinks->smallThumbnail) ? $item->volumeInfo->imageLinks->smallThumbnail : null,
                        'info_link' => $item->volumeInfo->infoLink,
                        'preview_link' => $item->volumeInfo->previewLink,
                        'average_rating' => !empty($item->volumeInfo->averageRating) ? $item->volumeInfo->averageRating : 0,
                        'ratings_count' => !empty($item->volumeInfo->ratingsCount) ? $item->volumeInfo->ratingsCount : 0,
                        'category' => !empty($item->volumeInfo->categories[0]) ? $item->volumeInfo->categories[0] : null,
                        'language' => $item->volumeInfo->language,
                        'publisher' => !empty($item->volumeInfo->publisher) ? $item->volumeInfo->publisher : '',
                        'published_at' => !empty($item->volumeInfo->publishedDate) ? $item->volumeInfo->publishedDate : null,
                    ]);
                // }
            }

            // push next job
            if($body->totalItems > $this->startIndex + $this->maxResults) {
                logger()->info('RetrieveBooksJob::handle() - push next job');
                logger()->info('RetrieveBooksJob::handle() - startIndex: ' . ($this->startIndex + $this->maxResults));

                $nextStartIndex = $this->startIndex + $this->maxResults;

                logger()->info('RetrieveBooksJob::handle() - nextStartIndex: ' . $nextStartIndex);

                dispatch(new RetrieveBooksJob($nextStartIndex));
            }
        } else {
            logger()->error('RetrieveBooksJob::handle() - body: ' . print_r($body, true));
        }
    }
}
