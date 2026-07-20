<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
    body { background: #f5f7fb; font-family: 'Inter', sans-serif; }
    .sidebar { position: fixed; left: 0; top: 0; width: 240px; height: 100vh; background: #0B3C5D; color: white; padding: 30px; }
    .sidebar .logo { font-size: 24px; font-weight: bold; margin-bottom: 40px; }
    .sidebar ul { list-style: none; padding: 0; }
    .sidebar a { color: white; display: block; padding: 14px; text-decoration: none; border-radius: 10px; margin-bottom: 8px; transition: 0.2s; }
    .sidebar a:hover, .sidebar .active a { background: rgba(255,255,255,.15); }
    .sidebar i { margin-right: 10px; }
    
    .content { margin-left: 240px; padding: 30px; }
    .card { border-radius: 12px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
    
    /* Top Metrics */
    .metric-card { padding: 20px; display: flex; align-items: center; gap: 15px; }
    .metric-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; }
    
    /* Headlines */
    .headline-card { display: flex; gap: 15px; padding: 15px; border-bottom: 1px solid #f0f0f0; transition: 0.2s; }
    .headline-card:hover { background: #fafafa; }
    .headline-card:last-child { border-bottom: none; }
    .headline-img { width: 140px; height: 90px; border-radius: 8px; object-fit: cover; flex-shrink: 0; }
    
    /* Badge Colors */
    .badge-logistics { background: #e0f2fe; color: #0284c7; }
    .badge-trade { background: #f3e8ff; color: #9333ea; }
    .badge-shipping { background: #dcfce7; color: #16a34a; }
    .badge-economy { background: #ffedd5; color: #ea580c; }
    
    .badge-positive { background: #dcfce7; color: #16a34a; }
    .badge-neutral { background: #fef3c7; color: #d97706; }
    .badge-negative { background: #fee2e2; color: #dc2626; }
    
    /* Trending */
    .trending-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
    .trending-item:last-child { border-bottom: none; }
    
    /* Breaking Timeline */
    .timeline { position: relative; padding-left: 20px; }
    .timeline::before { content: ''; position: absolute; left: 4px; top: 0; bottom: 0; width: 2px; background: #eee; }
    .timeline-item { position: relative; margin-bottom: 15px; }
    .timeline-item::before { content: ''; position: absolute; left: -20px; top: 4px; width: 10px; height: 10px; border-radius: 50%; background: #dc2626; border: 2px solid #fff; box-shadow: 0 0 0 1px #dc2626; }
    
    /* Table */
    .table-sm th { font-size: 12px; color: #6c757d; font-weight: 600; text-transform: uppercase; padding: 12px; }
    .table-sm td { font-size: 13px; padding: 12px; vertical-align: middle; }
    
    #loadingOverlay { position: fixed; top: 0; left: 240px; right: 0; bottom: 0; background: rgba(248, 249, 250, 0.8); z-index: 2000; display: flex; flex-direction: column; align-items: center; justify-content: center; backdrop-filter: blur(5px); }
</style>

<div id="loadingOverlay">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
    <div class="mt-3 fw-bold text-muted">Analyzing global news feeds...</div>
</div>

<div class="sidebar">
    <div class="logo">
        🌍 GSC RISK 
        INTELLIGENCE
    </div>
    <ul>
        <li><a href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="{{ route('countries') }}"><i class="bi bi-globe2"></i> Countries</a></li>
        <li><a href="{{ route('ports') }}"><i class="bi bi-geo-alt"></i> Ports</a></li>
        <li><a href="{{ route('shipment') }}"><i class="bi bi-truck"></i> Shipment</a></li>
        <li><a href="{{ route('weather.monitoring') }}"><i class="bi bi-cloud-sun"></i> Weather</a></li>
        <li class="active"><a href="{{ route('news.index') }}"><i class="bi bi-newspaper"></i> News</a></li>
        <li class="active"><a href="{{ route('watchlist.index') }}"><i class="bi bi-bookmark-star"></i> Watchlist Country</a></li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background:none; border:none; color:white; padding:14px; text-align:left; width:100%; border-radius:10px;">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</div>

<div class="content">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0">News Intelligence</h4>
            <span class="text-muted small">Stay updated with the latest global supply chain, trade, shipping, logistics and economy news.</span>
        </div>
    </div>

    <!-- Filters Row -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex gap-2" id="category-filters">
            <span class="text-muted small me-2 align-self-center">Category</span>
            <button class="btn btn-sm btn-primary rounded px-3 cat-btn" data-category="All">All</button>
            <button class="btn btn-sm btn-light bg-white border rounded px-3 cat-btn" data-category="Logistics"><i class="bi bi-truck me-1"></i> Logistics</button>
            <button class="btn btn-sm btn-light bg-white border rounded px-3 cat-btn" data-category="Trade"><i class="bi bi-arrow-left-right me-1"></i> Trade</button>
            <button class="btn btn-sm btn-light bg-white border rounded px-3 cat-btn" data-category="Shipping"><i class="bi bi-ship me-1"></i> Shipping</button>
            <button class="btn btn-sm btn-light bg-white border rounded px-3 cat-btn" data-category="Economy"><i class="bi bi-graph-up me-1"></i> Economy</button>
        </div>
        <div class="d-flex gap-2">
            <div class="input-group input-group-sm border rounded bg-white shadow-sm" style="width:250px;">
                <input type="text" class="form-control border-0 bg-transparent" placeholder="Search news...">
                <span class="input-group-text border-0 bg-transparent"><i class="bi bi-search"></i></span>
            </div>
            <button class="btn btn-sm btn-light bg-white border rounded px-3 shadow-sm"><i class="bi bi-funnel"></i> Filter</button>
        </div>
    </div>

    <!-- Top 5 Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col">
            <div class="card h-100 metric-card">
                <div class="metric-icon" style="background:#e0f2fe; color:#0284c7;"><i class="bi bi-file-text"></i></div>
                <div>
                    <div class="text-muted small fw-bold">Total News Today</div>
                    <div class="fw-bold fs-3 lh-1" id="stat-total">0</div>
                    <div class="text-success small" style="font-size:11px;"><i class="bi bi-arrow-up"></i> 18% vs yesterday</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 metric-card">
                <div class="metric-icon" style="background:#dcfce7; color:#16a34a;"><i class="bi bi-emoji-smile"></i></div>
                <div>
                    <div class="text-muted small fw-bold">Positive News</div>
                    <div class="fw-bold fs-3 lh-1" id="stat-pos">0</div>
                    <div class="text-success small" style="font-size:11px;"><i class="bi bi-arrow-up"></i> 12%</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 metric-card">
                <div class="metric-icon" style="background:#fef3c7; color:#d97706;"><i class="bi bi-emoji-neutral"></i></div>
                <div>
                    <div class="text-muted small fw-bold">Neutral News</div>
                    <div class="fw-bold fs-3 lh-1" id="stat-neu">0</div>
                    <div class="text-danger small" style="font-size:11px;"><i class="bi bi-arrow-down"></i> 5%</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 metric-card">
                <div class="metric-icon" style="background:#fee2e2; color:#dc2626;"><i class="bi bi-emoji-frown"></i></div>
                <div>
                    <div class="text-muted small fw-bold">Negative News</div>
                    <div class="fw-bold fs-3 lh-1" id="stat-neg">0</div>
                    <div class="text-danger small" style="font-size:11px;"><i class="bi bi-arrow-up"></i> 8%</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 metric-card">
                <div class="metric-icon" style="background:#f3e8ff; color:#9333ea;"><i class="bi bi-lightning-charge"></i></div>
                <div>
                    <div class="text-muted small fw-bold">Breaking News</div>
                    <div class="fw-bold fs-3 lh-1" id="stat-break">0</div>
                    <div class="small" style="font-size:11px;"><a href="#" class="text-decoration-none">View all &rarr;</a></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="row g-4 mb-4">
        <!-- Left: Top Headlines -->
        <div class="col-md-8">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold m-0">Top Headlines</h6>
                    <a href="#" class="text-decoration-none small">View all news &rarr;</a>
                </div>
                
                <div id="headlines-container">
                    <!-- Populated by JS -->
                </div>
            </div>
        </div>
        
        <!-- Right: Sidemenu stuff -->
        <div class="col-md-4">
            
            <!-- Trending Topics -->
            <div class="card p-3 mb-4">
                <h6 class="fw-bold mb-3" style="font-size:13px;">Trending Topics</h6>
                <div id="trending-container">
                    <!-- Populated by JS -->
                </div>
            </div>
            
            <!-- Latest Breaking News -->
            <div class="card p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold m-0" style="font-size:13px;">Latest Breaking News</h6>
                    <a href="#" class="text-decoration-none" style="font-size:11px;">View all &rarr;</a>
                </div>
                <div class="timeline" id="breaking-container">
                    <!-- Populated by JS -->
                </div>
            </div>
            
            <!-- News by Category (Donut) -->
            <div class="card p-3 mb-4">
                <h6 class="fw-bold mb-3" style="font-size:13px;">News by Category</h6>
                <div id="categoryChart"></div>
            </div>
            
            <!-- Sentiment Analysis (Bar) -->
            <div class="card p-3">
                <h6 class="fw-bold mb-3" style="font-size:13px;">Sentiment Analysis (7 Days)</h6>
                <div id="sentimentChart"></div>
            </div>
            
        </div>
    </div>

</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const countryName = "{{ $country->country_name }}";
    const countryCode = "{{ strtolower($country->country_code) }}";
    
    function getBadgeClass(cat) {
        if(cat === 'Logistics') return 'badge-logistics';
        if(cat === 'Trade') return 'badge-trade';
        if(cat === 'Shipping') return 'badge-shipping';
        return 'badge-economy';
    }

    let allArticles = [];

    function renderHeadlinesAndTable(articlesToRender) {
        // Top Headlines (Take first 4)
        let hlHtml = '';
        articlesToRender.slice(0, 4).forEach(a => {
            hlHtml += `
            <div class="headline-card">
                <img src="${a.image}" class="headline-img">
                <div class="w-100 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-1" style="font-size:14px;">${a.title}</h6>
                        <p class="text-muted m-0" style="font-size:12px;">${a.snippet}</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-2">
                        <div class="text-muted" style="font-size:11px;">
                            <span class="fw-semibold text-dark">${a.source}</span> &bull; ${a.date}
                            <span class="badge ${getBadgeClass(a.category)} border-0 ms-2">${a.category}</span>
                        </div>
                        <span class="badge badge-${a.sentiment.toLowerCase()} border-0">${a.sentiment}</span>
                    </div>
                </div>
            </div>`;
        });
        if(articlesToRender.length === 0) hlHtml = '<div class="text-muted p-4">No news found for this category.</div>';
        document.getElementById('headlines-container').innerHTML = hlHtml;
    }

    // Category Filter Click Event
    document.querySelectorAll('.cat-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Update button styles
            document.querySelectorAll('.cat-btn').forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-light', 'bg-white', 'border');
            });
            this.classList.remove('btn-light', 'bg-white', 'border');
            this.classList.add('btn-primary');

            const cat = this.getAttribute('data-category');
            if (cat === 'All') {
                renderHeadlinesAndTable(allArticles);
            } else {
                const filtered = allArticles.filter(a => a.category === cat);
                renderHeadlinesAndTable(filtered);
            }
        });
    });

    // Fetch News Data from our new API
    fetch(`/api/news/${countryName}`)
        .then(res => res.json())
        .then(data => {
            const stats = data.stats;
            allArticles = data.articles;
            
            // 1. Update Stats
            document.getElementById('stat-total').innerText = stats.total;
            document.getElementById('stat-pos').innerText = stats.positive;
            document.getElementById('stat-neu').innerText = stats.neutral;
            document.getElementById('stat-neg').innerText = stats.negative;
            document.getElementById('stat-break').innerText = stats.breaking;
            
            // 2. Top Headlines (Take first 4)
            // 2. Top Headlines
            renderHeadlinesAndTable(allArticles);
            
            // 3. Trending Topics
            let trHtml = '';
            for (const [cat, count] of Object.entries(data.categories)) {
                if(count > 0) {
                    trHtml += `
                    <div class="trending-item">
                        <span class="text-primary fw-semibold"># ${cat}</span>
                        <span class="text-muted">${count} articles</span>
                    </div>`;
                }
            }
            document.getElementById('trending-container').innerHTML = trHtml;
            
            // 4. Breaking News Timeline
            let brHtml = '';
            data.breaking_news.forEach(b => {
                brHtml += `
                <div class="timeline-item">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold" style="font-size:11px;">Recent</span>
                        <span class="badge bg-danger text-white rounded-1" style="font-size:9px;">BREAKING</span>
                    </div>
                    <div class="fw-semibold lh-sm mb-1" style="font-size:12px;">${b.title}</div>
                    <div class="text-end"><span class="badge ${getBadgeClass(b.category)} border-0" style="font-size:9px;">${b.category}</span></div>
                </div>`;
            });
            if(data.breaking_news.length === 0) {
                brHtml = `<div class="text-muted small">No breaking news at the moment.</div>`;
            }
            document.getElementById('breaking-container').innerHTML = brHtml;
            
            // 5. Category Donut Chart
            let catLabels = []; let catSeries = [];
            for (const [cat, count] of Object.entries(data.categories)) {
                catLabels.push(cat); catSeries.push(count);
            }
            new ApexCharts(document.querySelector("#categoryChart"), {
                series: catSeries,
                labels: catLabels,
                chart: { type: 'donut', height: 220 },
                colors: ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b'],
                plotOptions: { donut: { size: '70%' } },
                dataLabels: { enabled: false },
                legend: { position: 'right', fontSize: '11px' }
            }).render();
            
            // 6. Sentiment Bar Chart
            new ApexCharts(document.querySelector("#sentimentChart"), {
                series: [
                    { name: "Positive", data: data.trend.positive },
                    { name: "Neutral", data: data.trend.neutral },
                    { name: "Negative", data: data.trend.negative }
                ],
                chart: { type: 'bar', height: 200, stacked: false, toolbar: {show: false} },
                colors: ['#10b981', '#f59e0b', '#ef4444'],
                plotOptions: { bar: { columnWidth: '50%', borderRadius: 2 } },
                dataLabels: { enabled: false },
                xaxis: { categories: data.trend.dates, labels: {style: {fontSize: '9px'}} },
                yaxis: { show: false },
                grid: { show: false },
                legend: { position: 'top', fontSize: '10px', markers: {radius: 2} }
            }).render();
            

            // Hide Loader
            setTimeout(() => {
                document.getElementById('loadingOverlay').style.display = 'none';
            }, 500);
            
        })
        .catch(err => {
            console.error("API Error", err);
            alert("Failed to fetch news data");
            document.getElementById('loadingOverlay').style.display = 'none';
        });

</script>

</body>
</html>