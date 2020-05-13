<?php


namespace App\Console\Parsers\DataProviders;

use App\Console\Parsers\DataProviders\Contract\ParserInterface;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Image;
use Illuminate\Support\Facades\Storage;
use App\Article;
use Illuminate\Database\Eloquent\Model;
use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Exception\InvalidArgumentException;

class RBK extends BaseProvider {

    protected const NEWSFEED_URL = 'https://www.rbc.ru/v10/ajax/get-news-feed/project/rbcnews/lastDate/%d/limit/%d';
    protected const LIMIT = 15;

    protected const IGNORE_NEWS_URL_REGEX = '/stayhome\./';

    protected const ITEM_SELECTOR = '.news-feed__item';
    protected const TEXT_AREA_SELECTOR = '.article__text';
    protected const TEXT_SUMMARY_SELECTOR = '.article__text__overview';
    protected const IMAGE_SELECTOR = '.article__main-image';
    protected const TITLE_TAG = 'span';

    protected string $title;
    protected ?string $url;
    protected string $id;
    protected Model $articleModel;
    protected Crawler $crawler;

    public function fetchFeed(): \Generator {
        $preparedUrl = sprintf(static::NEWSFEED_URL, time(), static::LIMIT);
        $news = Http::get($preparedUrl)->json();
        foreach ($news['items'] as $item) {
            yield $item;
        }
    }

    public function parseNews(): void {
        foreach ($this->fetchFeed() as $news) {
            if (array_key_exists('html', $news)) {
                $this->crawler = new Crawler($news['html']);
                try {
                    $this->title = $this->crawler->filter(static::TITLE_TAG)->first()->text();
                    $this->url = $this->crawler->filter(static::ITEM_SELECTOR)->link()->getUri();
                    $this->id = static::getIdFromURL($this->url);
                } catch (\Throwable $exception) {
                    continue;
                }
                $this->articleModel = Article::query()->findOrNew($this->id);
                $this->articleModel->id = $this->id;
                $this->articleModel->title = $this->title;
                if (!$this->checkActual()) {
                    continue;
                }
                $html = Http::get($this->url)->body();
                $this->crawler = new Crawler($html);
                $this->saveNewsImage();
                if (!$this->saveText()) {
                    $this->articleModel->content = $this->title;
                }
                $this->saveSummary();
                $this->articleModel->save();
            }
        }
    }

    public function checkActual(): bool {
        try {
            $id = new ObjectId($this->articleModel->id);
            echo $id . PHP_EOL;
        } catch (\Throwable $exception) {
            return false;
        }

        return (
            !(bool)preg_match_all(static::IGNORE_NEWS_URL_REGEX, $this->url)
            && !$this->articleModel->exists
        );
    }

    protected static function getIdFromURL(string $urlRaw): string {
        $urlParts = explode('/', $urlRaw);
        return explode('?', end($urlParts))[0];
    }

    protected function saveText(): bool {
        $textParts = [];
        $summary = null;
        $this->crawler->filter(static::TEXT_AREA_SELECTOR)->filter('p')->each(
            static function(Crawler $paragraph) use (&$textParts, &$summary): void {
                if (count($textParts) === 0) {
                    $summary = $paragraph->text();
                }
                $textParts[] = '<p>'.$paragraph->text().'</p>';
            }
        );
        if (count($textParts) !== 0) {
            $this->articleModel->content = implode('', $textParts);
            $this->articleModel->summary = $summary;
            return true;
        }
        return false;
    }

    protected function saveSummary(): void {
        try {
            $summary = $this->crawler->filter(static::TEXT_AREA_SELECTOR)->filter(static::TEXT_SUMMARY_SELECTOR)->text();
        } catch (\Throwable $exception) {
            $summary = false;
        }
        if ($summary) {
            $this->articleModel->summary = $summary;
        }
    }

    protected function saveNewsImage(): bool {
        $imgUrl = $this->crawler->filter(static::IMAGE_SELECTOR)->filter('img')->first()->extract(['src']);
        $imgExists = count($imgUrl) > 0;
        if ($imgExists) {
            $imgUrl = reset($imgUrl);
            $name = pathinfo($imgUrl)['basename'];
            $contents = file_get_contents($imgUrl);
            Storage::put('public/'.$name, $contents);
            $this->articleModel->image = $name;
        }
        return $imgExists;
    }
}
