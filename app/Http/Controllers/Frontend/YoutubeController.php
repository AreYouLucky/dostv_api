<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class YoutubeController extends Controller
{
    public function topVideos(Request $request, int $year)
    {
        if ($year < 2005 || $year > now()->year + 1) { 
            return response()->json([
                'message' => 'Invalid year value.',
            ], 422);
        }

        $apiKey    = config('services.youtube.key');
        $channelId = config('services.youtube.channel_id');

        if (empty($apiKey) || empty($channelId)) {
            return response()->json([
                'message' => 'YouTube API key or channel ID is not configured.',
            ], 500);
        }

        $cacheKey = "youtube_top_videos_{$channelId}_{$year}";
        $cached = Cache::remember($cacheKey, now()->addHours(24), function () use ($apiKey, $channelId, $year) {
            $publishedAfter  = Carbon::create($year, 1, 1, 0, 0, 0, 'UTC')->toIso8601String();
            $publishedBefore = Carbon::create($year, 12, 31, 23, 59, 59, 'UTC')->toIso8601String();

            $allVideos = collect();
            $pageToken = null;
            $maxPages  = 30;
            $pageCount = 0;

            do {
                $pageCount++;
                $searchResponse = Http::get('https://www.googleapis.com/youtube/v3/search', [
                    'key'             => $apiKey,
                    'channelId'       => $channelId,
                    'part'            => 'id',
                    'order'           => 'date',
                    'type'            => 'video',
                    'maxResults'      => 50,
                    'publishedAfter'  => $publishedAfter,
                    'publishedBefore' => $publishedBefore,
                    'pageToken'       => $pageToken,
                ]);

                if (! $searchResponse->successful()) {
                    // Throw here so controller can catch / handle if you want
                    // For cache callback, we can just break and return what we have
                    break;
                }

                $searchData = $searchResponse->json();

                $videoIds = collect($searchData['items'] ?? [])
                    ->pluck('id.videoId')
                    ->filter()
                    ->values();

                if ($videoIds->isNotEmpty()) {
                    // 2. Get statistics (viewCount) + snippet for those video IDs
                    $videosResponse = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                        'key'  => $apiKey,
                        'id'   => $videoIds->join(','),
                        'part' => 'snippet,statistics',
                    ]);

                    if ($videosResponse->successful()) {
                        $videosData = $videosResponse->json();

                        foreach ($videosData['items'] ?? [] as $item) {
                            $allVideos->push([
                                'video_id'     => $item['id'] ?? null,
                                'title'        => $item['snippet']['title'] ?? '',
                                'description'  => $item['snippet']['description'] ?? '',
                                'published_at' => $item['snippet']['publishedAt'] ?? null,
                                'view_count'   => isset($item['statistics']['viewCount'])
                                    ? (int) $item['statistics']['viewCount']
                                    : 0,
                                'thumbnail'    => $item['snippet']['thumbnails']['high']['url']
                                    ?? $item['snippet']['thumbnails']['default']['url']
                                    ?? null,
                            ]);
                        }
                    }
                }

                $pageToken = $searchData['nextPageToken'] ?? null;
                if ($pageCount >= $maxPages) {
                    break;
                }
            } while ($pageToken);
            $topVideos = $allVideos
                ->sortByDesc('view_count')
                ->take(10)
                ->values()
                ->all();
            return [
                'total_videos_found' => $allVideos->count(),
                'top_10_most_viewed' => $topVideos,
            ];
        });

        return response()->json([
            'year'               => $year,
            'channel_id'         => $channelId,
            'total_videos_found' => $cached['total_videos_found'] ?? 0,
            'top_10_most_viewed' => $cached['top_10_most_viewed'] ?? [],
        ]);
    }
}


