<x-app-layout>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>

body{
    background:#f5f7fb;
}

/* Sidebar */

.sidebar{

    position:fixed;

    left:0;

    top:0;

    width:240px;

    height:100vh;

    background:#18864B;

    color:white;

    padding:30px;

}

.sidebar h3{

    font-weight:bold;

    margin-bottom:40px;

}

.sidebar a{

    color:white;

    display:block;

    padding:14px;

    text-decoration:none;

    border-radius:10px;

    margin-bottom:8px;

}

.sidebar a:hover{

    background:rgba(255,255,255,.15);

}

/* Content */

.content{

    margin-left:260px;

    padding:35px;

}

/* Card */

.summary-card{

    background:white;

    border:none;

    border-radius:20px;

    padding:25px;

    box-shadow:0 10px 25px rgba(0,0,0,.08);

    transition:.3s;

}

.summary-card:hover{

    transform:translateY(-5px);

}

.summary-icon{

    width:60px;

    height:60px;

    border-radius:50%;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:26px;

    color:white;

}

.green{

    background:#18864B;

}

.blue{

    background:#0d6efd;

}

.orange{

    background:#fd7e14;

}

.red{

    background:#dc3545;

}

.title{

    color:#666;

    margin-top:15px;

}

.value{

    font-size:34px;

    font-weight:bold;

}

</style>

<div class="sidebar">

<h3>🌍 SCRI</h3>

<a href="{{ route('dashboard') }}">
<i class="bi bi-speedometer2"></i>
Dashboard
</a>

<a href="{{ route('countries') }}">
<i class="bi bi-globe2"></i>
Countries
</a>

<a href="{{ route('economic.index') }}">
<i class="bi bi-graph-up"></i>
Economic
</a>

<a href="#">
<i class="bi bi-cloud-sun"></i>
Weather
</a>

<a href="#">
<i class="bi bi-newspaper"></i>
News
</a>

</div>

<div class="content">

<div class="mb-4">

<h2 class="fw-bold">

Global Supply Chain Risk Intelligence Dashboard

</h2>

<p class="text-secondary">

Last Update :
{{ now()->format('d F Y H:i') }}

</p>

</div>

<div class="row g-4">

<div class="col-md-3">

<div class="summary-card">

<div class="summary-icon green">

<i class="bi bi-globe2"></i>

</div>

<div class="title">

Countries

</div>

<div class="value">

{{ $totalCountries }}

</div>

</div>

</div>

<div class="col-md-3">

<div class="summary-card">

<div class="summary-icon blue">

<i class="bi bi-geo-alt-fill"></i>

</div>

<div class="title">

Ports

</div>

<div class="value">

{{ $totalPorts }}

</div>

</div>

</div>

<div class="col-md-3">

<div class="summary-card">

<div class="summary-icon orange">

<i class="bi bi-exclamation-triangle-fill"></i>

</div>

<div class="title">

Average Risk

</div>

<div class="value">

{{ $averageRisk }}

</div>

</div>

</div>

<div class="col-md-3">

<div class="summary-card">

<div class="summary-icon red">

<i class="bi bi-shield-fill-exclamation"></i>

</div>

<div class="title">

Highest Risk

</div>

<div class="value">

{{ $highRiskCountry->country->country_name ?? '-' }}

</div>

</div>

</div>

<div class="col-md-3">

<div class="summary-card">

<div class="summary-icon green">

<i class="bi bi-shield-check"></i>

</div>

<div class="title">

Lowest Risk

</div>

<div class="value">

{{ $lowRiskCountry->country->country_name ?? '-' }}

</div>

</div>

</div>

<div class="col-md-3">

<div class="summary-card">

<div class="summary-icon blue">

<i class="bi bi-graph-up-arrow"></i>

</div>

<div class="title">

Economic Records

</div>

<div class="value">

{{ $economicRecords }}

</div>

</div>

</div>

<div class="col-md-3">

<div class="summary-card">

<div class="summary-icon orange">

<i class="bi bi-cloud-lightning-rain"></i>

</div>

<div class="title">

Weather Alerts

</div>

<div class="value">

{{ $weatherAlerts }}

</div>

</div>

</div>

<div class="col-md-3">

<div class="summary-card">

<div class="summary-icon red">

<i class="bi bi-newspaper"></i>

</div>

<div class="title">

News

</div>

<div class="value">

{{ $newsCount }}

</div>

</div>

</div>

</div>

<div class="mt-5">

    <h3 class="fw-bold mb-4">
        Latest Global Supply Chain News
    </h3>

    @foreach($latestNews as $news)

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <h4 class="fw-bold">

                {{ $news['title'] }}

            </h4>

            <small class="text-muted">

                {{ $news['date'] }}

            </small>

            <br><br>

            <a href="{{ $news['link'] }}"
               target="_blank"
               class="btn btn-primary">

                Read More

            </a>

        </div>

    </div>

    @endforeach

</div>

</div>

</x-app-layout>