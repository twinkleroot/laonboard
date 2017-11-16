<?php

namespace App\Models;

use App\Models\Write;
use App\Models\Board;
use Carbon\Carbon;
use Cache;
use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;

class RssFeed
{
    /**
    * Return the content of the RSS feed
    */
    public function getRSS($boardName)
    {
        if (Cache::has('rss-feed-'. $boardName)) {
            return Cache::get('rss-feed-'. $boardName);
        }

        $rss = $this->buildRssData($boardName);
        Cache::add('rss-feed-'. $boardName, $rss, 120);

        return $rss;
    }

    /**
    * Return a string with the feed data
    *
    * @return string
    */
    protected function buildRssData($boardName)
    {
        $board = Board::getBoard($boardName, 'table_name');
        $group = $board->group;
        $writeModel = new Write();
        $writeModel->setTableName($board->table_name);
        $title1 = $this->specialcharsReplace($group->subject, 255);
        $title2 = $this->specialcharsReplace($board->subject);
        $now = Carbon::now();

        $feed = new Feed();
        $channel = new Channel();
        $channel
          ->title( $this->specialcharsReplace(config('rss.title'). ' &gt; '. $title1. ' &gt; '. $title2) )
          ->description(config('rss.description'))
          ->url(route('board.index', $boardName))
          ->language('en')
          ->lastBuildDate($now->timestamp)
          ->appendTo($feed);

        $writes = $writeModel
          ->whereRaw("option not like '%secret%'")
          ->orWhereNull('option')
          ->where('is_comment', 0)
          ->orderByRaw('num, reply')
          ->take($board->page_rows)
          ->get();
        foreach ($writes as $write) {
          $item = new Item();
          $writeUrl = route('board.view', ['boardId' => $boardName, 'writeId' => $write->id]);
          $html = 0;
          if(strstr($write->option, 'html')) {
              $html = 1;
          }
          $item
            ->title($write->subject)
            ->preferCdata(1)
            ->description(convertContent($write->content, $html))
            ->url($writeUrl)
            ->pubDate($write->created_at->timestamp)
            ->guid($writeUrl, true)
            ->author($this->specialcharsReplace($write->name))
            ->appendTo($channel);
        }

        $feed = $feed->__toString();

        // Replace a couple items to make the feed more compliant
        $feed = str_replace(
          '<rss version="2.0">',
          '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">',
          $feed
        );
        $feed = str_replace(
          '<channel>',
          '<channel>'."\n".'    <atom:link href="'.route('rss', $boardName).
          '" rel="self" type="application/rss+xml" />',
          $feed
        );

        return $feed;
    }

    private function specialcharsReplace($str, $len=0) {
        if ($len) {
            $str = mb_substr($str, 0, $len, "UTF-8");
        }
        $str = str_replace(array("&", "<", ">"), array("&amp;", "&lt;", "&gt;"), $str);

        return $str;
    }
}
