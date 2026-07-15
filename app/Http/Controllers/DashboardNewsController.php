<?php

namespace App\Http\Controllers;

class DashboardNewsController extends Controller
{
    public static function latest()
    {
        $url="https://news.google.com/rss/search?q=supply+chain&hl=en-US&gl=US&ceid=US:en";

        $xml=simplexml_load_file($url);

        if(!$xml){
            return [];
        }

        $news=[];

        foreach($xml->channel->item as $item){

            $news[]=[

                'title'=>(string)$item->title,

                'link'=>(string)$item->link,

                'date'=>date(
                    'd M Y H:i',
                    strtotime($item->pubDate)
                )

            ];

            if(count($news)>=6){
                break;
            }
        }

        return $news;
    }
}