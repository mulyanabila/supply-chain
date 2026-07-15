<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    public function getNews($country)
    {
        $url = "https://news.google.com/rss/search?q=" .
            urlencode($country . " economy OR trade OR supply chain") .
            "&hl=en-US&gl=US&ceid=US:en";

        $response = Http::withoutVerifying()->get($url);

        if (!$response->successful()) {
            return response()->json([]);
        }

        $xml = simplexml_load_string($response->body());

        if (!$xml) {
            return response()->json([]);
        }

        $news = [];

        // ===========================
        // Sentiment Counter
        // ===========================

        $positive = 0;
        $negative = 0;

        foreach ($xml->channel->item as $item) {

            $title = strtolower((string)$item->title);

            // ===========================
            // Daftar kata positif
            // ===========================

            $positiveWords = [
                'growth',
                'increase',
                'profit',
                'expand',
                'recovery',
                'strong',
                'record',
                'investment',
                'success'
            ];

            // ===========================
            // Daftar kata negatif
            // ===========================

            $negativeWords = [
                'war',
                'crisis',
                'inflation',
                'recession',
                'conflict',
                'strike',
                'earthquake',
                'flood',
                'shortage',
                'decline',
                'loss'
            ];

            foreach ($positiveWords as $word) {

                if (str_contains($title, $word)) {
                    $positive++;
                }

            }

            foreach ($negativeWords as $word) {

                if (str_contains($title, $word)) {
                    $negative++;
                }

            }

            // ===========================
            // Simpan berita
            // ===========================

            $news[] = [

                'title' => (string)$item->title,

                'link' => (string)$item->link,

                'date' => date(
                    'd M Y H:i',
                    strtotime($item->pubDate)
                ),

            ];

            if (count($news) >= 5) {
                break;
            }

        }

        // ===========================
        // Hitung Sentiment
        // ===========================

        $sentiment = "Neutral";

        if ($negative > $positive) {

            $sentiment = "Negative";

        } elseif ($positive > $negative) {

            $sentiment = "Positive";

        }

        return response()->json([

            'sentiment' => $sentiment,

            'articles' => $news

        ]);
    }

    public static function getSentiment($country)
{
    $controller = new self();

    $response = $controller->getNews($country);

    $json = json_decode($response->getContent(),true);

    return $json['sentiment'] ?? "Neutral";
}
}