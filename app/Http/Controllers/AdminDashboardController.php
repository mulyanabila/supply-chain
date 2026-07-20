<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use App\Models\RiskScore;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Counts
        $totalCountries = Country::count();
        $portsTracked = Port::count();

        $highRiskCount = RiskScore::where('risk_level', 'High Risk')->count();
        $mediumRiskCount = RiskScore::where('risk_level', 'Medium Risk')->count();
        $lowRiskCount = RiskScore::where('risk_level', 'Low Risk')->count();

        $newsCount = Article::count();
        $totalUsers = User::count();

        // Top 5 High Risk Countries (from DB if available, else mock)
        $dbTopHighRisk = RiskScore::with('country')
            ->orderByDesc('total_score')
            ->limit(5)
            ->get();

        $topHighRisk = [];
        foreach ($dbTopHighRisk as $item) {
            $topHighRisk[] = [
                'name' => $item->country->country_name ?? 'Unknown',
                'code' => strtolower($item->country->country_code ?? 'un'),
                'region' => $item->country->region ?? 'Unknown',
                'score' => $item->total_score,
                'trend' => rand(1, 5)
            ];
        }

        // Map data from database
        $mapData = Country::with('riskScore')->get()->map(function($country) {
            return [
                'name' => $country->country_name,
                'lat' => $country->latitude,
                'lng' => $country->longitude,
                'risk_level' => $country->riskScore->risk_level ?? 'Low Risk',
                'risk_score' => $country->riskScore->total_score ?? 0,
            ];
        })->filter(function($country) {
            return !empty($country['lat']) && !empty($country['lng']);
        })->values();

        // Risk Score Trend Data
        $dates = ['2026-01-17', '2026-02-17', '2026-03-17', '2026-04-17', '2026-05-17', '2026-06-17', '2026-07-17'];
        $highRiskTrend = [];
        $mediumRiskTrend = [];
        $lowRiskTrend = [];
        $trendDates = [];
        
        foreach ($dates as $date) {
            $trendDates[] = date('d M', strtotime($date));
            $highRiskTrend[] = RiskScore::where('recorded_date', $date)->where('risk_level', 'High Risk')->count();
            $mediumRiskTrend[] = RiskScore::where('recorded_date', $date)->where('risk_level', 'Medium Risk')->count();
            $lowRiskTrend[] = RiskScore::where('recorded_date', $date)->where('risk_level', 'Low Risk')->count();
        }
        
        $riskTrendDates = json_encode($trendDates);
        $highRiskTrendData = json_encode($highRiskTrend);
        $mediumRiskTrendData = json_encode($mediumRiskTrend);
        $lowRiskTrendData = json_encode($lowRiskTrend);

        return view('admin.dashboard', compact(
            'totalCountries',
            'portsTracked',
            'highRiskCount',
            'mediumRiskCount',
            'lowRiskCount',
            'newsCount',
            'totalUsers',
            'topHighRisk',
            'mapData',
            'riskTrendDates',
            'highRiskTrendData',
            'mediumRiskTrendData',
            'lowRiskTrendData'
        ));
    }

    public function users()
    {
        // Counts
        $totalUsersCount = User::count();
        $activeUsersCount = User::count(); 
        $adminsCount = User::where('role', 'admin')->count();
        $newUsersCount = User::where('created_at', '>=', now()->startOfMonth())->count();

        // Fetch users from DB
        $dbUsers = User::all();
        $usersList = [];
        $index = 1;
        
        // Map db users
        foreach ($dbUsers as $user) {
            $role = ucfirst($user->role ?: 'user');
            if ($role === 'User') {
                $roleColor = 'green';
            } elseif ($role === 'Admin') {
                $roleColor = 'purple';
            } else {
                $roleColor = 'blue'; // Analyst
            }
            
            $usersList[] = [
                'id' => $index++,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role,
                'role_color' => $roleColor,
                'country' => 'Indonesia',
                'flag' => '🇮🇩',
                'last_login' => '18 Jul 2026 22:10',
                'status' => 'Active',
                'status_color' => 'emerald',
                'created_date' => $user->created_at ? $user->created_at->format('d M Y') : '18 Jul 2026'
            ];
        }

        // Recent Activity based on actual DB users
        $recentActivities = [];
        if (count($dbUsers) > 0) {
            $lastUser = $dbUsers->last();
            $recentActivities[] = [
                'type' => 'login',
                'title' => 'User logged in',
                'name' => $lastUser->name,
                'time' => '10 minutes ago'
            ];
            
            if (count($dbUsers) > 1) {
                $secondUser = $dbUsers[count($dbUsers) - 2];
                $recentActivities[] = [
                    'type' => 'register',
                    'title' => 'New user registered',
                    'name' => $secondUser->name,
                    'time' => '2 hours ago'
                ];
            }
            
            $firstUser = $dbUsers->first();
            $recentActivities[] = [
                'type' => 'login',
                'title' => 'User logged in',
                'name' => $firstUser->name,
                'time' => '1 day ago'
            ];
        }

        // Registration Trend Dates (Last 7 Days)
        $trendDates = [];
        $trendCounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateStr = now()->subDays($i)->format('Y-m-d');
            $label = now()->subDays($i)->format('d M');
            $count = User::whereDate('created_at', $dateStr)->count();
            $trendDates[] = $label;
            $trendCounts[] = $count;
        }

        $regTrendDates = json_encode($trendDates);
        $regTrendCounts = json_encode($trendCounts);

        return view('admin.users', compact(
            'totalUsersCount',
            'activeUsersCount',
            'adminsCount',
            'newUsersCount',
            'usersList',
            'recentActivities',
            'regTrendDates',
            'regTrendCounts'
        ));
    }

    public function ports(Request $request)
    {
        // 1. Ports Counts
        $totalPorts = Port::count();
        $activePorts = Port::count(); 
        $countriesCovered = Port::distinct('country_id')->count();

        // 2. Query with Filters
        $portsQuery = Port::with('country');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $portsQuery->where(function($q) use ($search) {
                $q->where('port_name', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('country', function($cq) use ($search) {
                      $cq->where('country_name', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('country') && $request->get('country') !== 'All Countries') {
            $portsQuery->whereHas('country', function($q) use ($request) {
                $q->where('country_name', $request->get('country'));
            });
        }

        // Paginated Ports List
        $portsList = $portsQuery->paginate(10)->withQueryString();

        // Map list details
        $formattedPorts = [];
        foreach ($portsList as $port) {
            $type = 'Sea Port';
            if (stripos($port->type, 'River') !== false || stripos($port->type, 'Canal') !== false || stripos($port->type, 'Lake') !== false) {
                $type = 'River Port';
            }
            
            $formattedPorts[] = [
                'id' => 'POR-' . str_pad($port->id, 4, '0', STR_PAD_LEFT),
                'name' => $port->port_name,
                'country' => $port->country->country_name ?? 'Unknown',
                'flag' => $port->country->flag ?? '🏳️',
                'city' => $port->city ?: ($port->country->capital ?? 'Unknown'),
                'latitude' => $port->latitude,
                'longitude' => $port->longitude,
                'type' => $type,
                'status' => 'Active',
                'last_updated' => $port->updated_at ? $port->updated_at->format('d M Y H:i') : '18 Jul 2026 22:15'
            ];
        }

        // 3. Top 10 Countries by Port Count for Bar Chart
        $topCountriesQuery = Port::selectRaw('country_id, count(*) as count')
            ->groupBy('country_id')
            ->orderByDesc('count')
            ->limit(10)
            ->with('country')
            ->get();

        $topCountriesNames = [];
        $topCountriesCounts = [];
        foreach ($topCountriesQuery as $tc) {
            $topCountriesNames[] = $tc->country->country_name ?? 'Unknown';
            $topCountriesCounts[] = $tc->count;
        }

        // 4. Port Types Distribution
        $seaPortsCount = Port::where(function($q) {
            $q->where('type', 'like', '%Coastal%')
              ->orWhere('type', 'like', '%Open%');
        })->count();
        $riverPortsCount = Port::where('type', 'like', '%River%')
            ->orWhere('type', 'like', '%Canal%')
            ->orWhere('type', 'like', '%Lake%')
            ->count();
        $dryPortsCount = $totalPorts - ($seaPortsCount + $riverPortsCount);

        // 5. Unique Countries for Filter Dropdown
        $countriesList = Country::whereHas('ports')->orderBy('country_name')->pluck('country_name');

        // 6. Map markers (pass coordinates of top 100 ports for rendering)
        $mapPoints = Port::with('country')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->limit(100)
            ->get()
            ->map(function($port) {
                $type = 'Sea Port';
                if (stripos($port->type, 'River') !== false || stripos($port->type, 'Canal') !== false) {
                    $type = 'River Port';
                }
                return [
                    'name' => $port->port_name,
                    'lat' => $port->latitude,
                    'lng' => $port->longitude,
                    'type' => $type,
                    'country' => $port->country->country_name ?? 'Unknown'
                ];
            });

        return view('admin.ports', compact(
            'totalPorts',
            'activePorts',
            'countriesCovered',
            'portsList',
            'formattedPorts',
            'topCountriesNames',
            'topCountriesCounts',
            'seaPortsCount',
            'riverPortsCount',
            'dryPortsCount',
            'countriesList',
            'mapPoints'
        ));
    }

    public static function getArticleSentiment($title)
    {
        $titleLower = strtolower($title);
        $positiveWords = ['growth', 'increase', 'profit', 'expand', 'recovery', 'strong', 'record', 'investment', 'success', 'positive', 'ease', 'normal'];
        $negativeWords = ['decline', 'loss', 'decrease', 'weak', 'disruption', 'tension', 'drop', 'crisis', 'negative', 'storm', 'earthquake', 'flood', 'inflation', 'congestion', 'escalate', 'shortage'];
        
        $posCount = 0;
        $negCount = 0;
        
        foreach ($positiveWords as $word) {
            if (strpos($titleLower, $word) !== false) $posCount++;
        }
        foreach ($negativeWords as $word) {
            if (strpos($titleLower, $word) !== false) $negCount++;
        }
        
        if ($posCount > $negCount) return 'Positive';
        if ($negCount > $posCount) return 'Negative';
        return 'Neutral';
    }

    public function articles(Request $request)
    {
        // 1. Articles Counts
        $totalArticles = Article::count();
        $publishedArticles = Article::whereNotNull('published_at')->count();
        $draftArticles = Article::whereNull('published_at')->count();
        
        // 2. Query with Filters
        $articlesQuery = Article::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $articlesQuery->where(function($q) use ($search) {
                $q->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('content', 'LIKE', '%' . $search . '%')
                  ->orWhere('author', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->filled('category') && $request->get('category') !== 'All Categories') {
            $articlesQuery->where('category', $request->get('category'));
        }

        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'Published') {
                $articlesQuery->whereNotNull('published_at');
            } elseif ($status === 'Draft') {
                $articlesQuery->whereNull('published_at');
            }
        }

        // Paginated Articles List
        $articlesList = $articlesQuery->paginate(10)->withQueryString();

        // Map list details and calculate sentiments dynamically
        $formattedArticles = [];
        $sentimentDistribution = ['Positive' => 0, 'Negative' => 0, 'Neutral' => 0];
        
        // Query all articles for distribution charts (so charts are accurate for the whole DB)
        $allArticlesForStats = Article::all();
        $articlesByCategory = [];
        
        foreach ($allArticlesForStats as $art) {
            // Sentiment
            $s = self::getArticleSentiment($art->title);
            $sentimentDistribution[$s]++;
            
            // Category count
            $cat = $art->category ?: 'General';
            if (!isset($articlesByCategory[$cat])) {
                $articlesByCategory[$cat] = 0;
            }
            $articlesByCategory[$cat]++;
        }
        
        foreach ($articlesList as $art) {
            $s = self::getArticleSentiment($art->title);
            
            // Map country
            $countryName = 'Global';
            $flag = '🌍';
            $titleLower = strtolower($art->title);
            $contentLower = strtolower($art->content);
            
            $countriesMap = [
                'united states' => ['name' => 'United States', 'flag' => '🇺🇸'],
                'germany' => ['name' => 'Germany', 'flag' => '🇩🇪'],
                'japan' => ['name' => 'Japan', 'flag' => '🇯🇵'],
                'egypt' => ['name' => 'Egypt', 'flag' => '🇪🇬'],
                'taiwan' => ['name' => 'Taiwan', 'flag' => '🇹🇼'],
                'india' => ['name' => 'India', 'flag' => '🇮🇳'],
                'china' => ['name' => 'China', 'flag' => '🇨🇳'],
                'australia' => ['name' => 'Australia', 'flag' => '🇦🇺'],
                'uk' => ['name' => 'United Kingdom', 'flag' => '🇬🇧'],
                'united kingdom' => ['name' => 'United Kingdom', 'flag' => '🇬🇧'],
                'canada' => ['name' => 'Canada', 'flag' => '🇨🇦'],
                'singapore' => ['name' => 'Singapore', 'flag' => '🇸🇬'],
                'brazil' => ['name' => 'Brazil', 'flag' => '🇧🇷'],
                'france' => ['name' => 'France', 'flag' => '🇫🇷'],
            ];
            
            foreach ($countriesMap as $key => $details) {
                if (strpos($titleLower, $key) !== false || strpos($contentLower, $key) !== false) {
                    $countryName = $details['name'];
                    $flag = $details['flag'];
                    break;
                }
            }
            
            // Map thumbnail
            $thumbnail = 'https://images.unsplash.com/photo-1518241353330-0f7941c2d9b5?auto=format&fit=crop&q=80&w=150'; // default graph
            $catLower = strtolower($art->category);
            if (strpos($catLower, 'ship') !== false || strpos($catLower, 'port') !== false) {
                $thumbnail = 'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?auto=format&fit=crop&q=80&w=150';
            } elseif (strpos($catLower, 'trade') !== false) {
                $thumbnail = 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&q=80&w=150';
            } elseif (strpos($catLower, 'risk') !== false || strpos($catLower, 'disrupt') !== false) {
                $thumbnail = 'https://images.unsplash.com/photo-1594897030264-ab7d87efc473?auto=format&fit=crop&q=80&w=150';
            } elseif (strpos($catLower, 'tech') !== false) {
                $thumbnail = 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&q=80&w=150';
            }
            
            $formattedArticles[] = [
                'id' => $art->id,
                'title' => $art->title,
                'content_summary' => \Illuminate\Support\Str::limit(strip_tags($art->content), 85),
                'category' => $art->category ?: 'General',
                'country' => $countryName,
                'flag' => $flag,
                'author' => $art->author ?: 'Staff Writer',
                'published_date' => $art->published_at ? date('d M Y H:i', strtotime($art->published_at)) : 'Draft',
                'sentiment' => $s,
                'status' => $art->published_at ? 'Published' : 'Draft',
                'thumbnail' => $thumbnail
            ];
        }

        // Count of AI Sentiment Analyzed
        $aiSentimentAnalyzedCount = Article::count(); // assume all are analyzed

        // Monthly Published Trend
        $publishedTrend = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
        foreach ($months as $idx => $m) {
            $monthNum = $idx + 1;
            $publishedTrend[] = Article::whereMonth('published_at', $monthNum)
                ->whereYear('published_at', date('Y'))
                ->count();
        }
        
        $publishedTrendJson = json_encode($publishedTrend);
        $publishedMonthsJson = json_encode($months);

        // Fetch categories list for filter dropdown
        $categoriesList = Article::whereNotNull('category')->distinct()->pluck('category');

        return view('admin.articles', compact(
            'totalArticles',
            'publishedArticles',
            'draftArticles',
            'aiSentimentAnalyzedCount',
            'articlesList',
            'formattedArticles',
            'sentimentDistribution',
            'articlesByCategory',
            'publishedTrendJson',
            'publishedMonthsJson',
            'categoriesList'
        ));
    }
}