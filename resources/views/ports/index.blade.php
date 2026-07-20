<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<link
rel="stylesheet"
href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<style>

    .sidebar{
    width:240px;
    height:100vh;
    background:#0B3C5D;
    color:white;
    position:fixed;
    left:0;
    top:0;
    padding:30px 20px;
}

.logo{
    text-align:center;
    font-size:28px;
    font-weight:bold;
    margin-bottom:40px;
}

.sidebar ul{
    list-style:none;
    padding:0;
}

.sidebar li{
    padding:15px;
    margin-bottom:10px;
    border-radius:10px;
    transition:.3s;
}

.sidebar li:hover{
    background:rgba(255,255,255,.15);
}

.sidebar li.active{
    background:white;
}

.sidebar li.active a{
    color:#18864B;
    font-weight:bold;
}

.sidebar a{
    color:white;
    text-decoration:none;
    display:block;
}

.sidebar a:hover{
    color:white;
}

.sidebar button{
    width:100%;
    border:none;
    background:none;
    color:white;
    text-align:left;
}

.content{
    margin-left:260px;
    padding:35px;
}

.content{
    margin-left:260px;
    padding:35px;
}

.summary-card{
    border:none;
    border-radius:20px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
    transition:.3s;
}

.summary-card:hover{
    transform:translateY(-5px);
}

.icon-circle{
    width:55px;
    height:55px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
}

</style>

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
        <li class="active"><a href="{{ route('weather.monitoring') }}"><i class="bi bi-cloud-sun"></i> Weather</a></li>
        <li><a href="{{ route('news.index') }}"><i class="bi bi-newspaper"></i> News</a></li>
        <li><a href="{{ route('watchlist.index') }}"><i class="bi bi-bookmark-star"></i> Watchlist country</a></li>
        <li><a href="{{ route('comparison.index') }}"><i class="bi bi-bar-chart"></i>Country Comparison</a></li>
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

<div class="container-fluid">

<span class="badge bg-primary mb-3">

🌍 Global Export Analysis

</span>

<h1 class="fw-bold">

Port Network Analysis

</h1>

<p class="text-secondary mb-4">

Explore global ports and logistics networks that support international export activities.

</p>

<div class="mb-3 text-end">
    <a href="{{ route('ports.sync') }}"
       class="btn btn-success">
        <i class="bi bi-arrow-repeat"></i>
        Sync Ports
    </a>
</div>
<div class="card shadow border-0 mb-4">

<div class="card-body">

<form method="GET">

<div class="row">

<div class="col-md-5">

<label class="form-label">

Filter Country

</label>

<select
class="form-select"
name="country">

<option value="">

All Countries

</option>

@foreach($countries as $country)

<option
value="{{ $country->country_name }}"
{{ request('country')==$country->country_name?'selected':'' }}>

{{ $country->country_name }}

</option>

@endforeach

</select>

</div>

<div class="col-md-5">

<label class="form-label">

Search Port

</label>

<input
type="text"
class="form-control"
name="search"
value="{{ request('search') }}"
placeholder="Search Port">

</div>

<div class="col-md-2 d-flex align-items-end">

<button class="btn btn-primary w-100">

Search

</button>

</div>

</div>

</form>

</div>

</div>
<div class="row mb-4">

<div class="col-md-3">

<div class="card summary-card">

<div class="card-body">

<div class="icon-circle bg-info-subtle mb-3">

<i class="bi bi-water"></i>

</div>
<h5>Total Ports</h5>

<h2>
{{ $totalPorts }}
</h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card summary-card">

<div class="card-body">

<div class="icon-circle bg-success-subtle mb-3">

<i class="bi bi-globe2"></i>

</div>

<h5>Countries</h5>

<h2>
{{ $totalCountries }}
</h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card summary-card">

<div class="card-body">

<div class="icon-circle bg-primary-subtle mb-3">

<i class="bi bi-geo-alt"></i>

</div>

<h5>Locations</h5>

<h2>
{{ $totalLocations }}
</h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card summary-card">

<div class="card-body">

<div class="icon-circle bg-warning-subtle mb-3">

<i class="bi bi-diagram-3"></i>

</div>

<h5>Network</h5>

<h2>

Global

</h2>

</div>

</div>

</div>

</div>
<div class="card shadow border-0 mb-4">

<div class="card-body">

<h3 class="fw-bold mb-3">

Global Port Map

</h3>

<div
id="map"
style="height:500px;border-radius:20px;">

</div>

</div>

</div>

<div class="card shadow border-0">

<div class="card-body">

<h3 class="fw-bold mb-4">

Port List

</h3>

<div class="table-responsive">

<table class="table">

<thead>

<tr>

<th>Port</th>

<th>Country</th>

<th>City</th>

<th>Status</th>

<th>Type</th>

</tr>

</thead>

<tbody>

@if($ports->count())

@foreach($ports as $port)

<tr>

<td>{{ $port->port_name }}</td>

<td>{{ $port->country->country_name }}</td>

<td>{{ $port->city }}</td>

<td>{{ $port->status }}</td>

<td>{{ $port->type }}</td>

</tr>

@endforeach

@else

<tr>

<td colspan="5" class="text-center text-muted py-5">

No ports found.

</td>

</tr>

@endif

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>

@if(request('country') && $ports->count())

var map = L.map('map').setView([

{{ $ports->first()->latitude }},

{{ $ports->first()->longitude }}

],5);

@else

var map = L.map('map').setView([20,0],2);

@endif

L.tileLayer(
'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
{
    maxZoom:18
}
).addTo(map);

@foreach($ports as $port)

L.marker([
    {{ $port->latitude }},
    {{ $port->longitude }}
]).addTo(map)

.bindPopup(`
<b>{{ $port->port_name }}</b>
<br>

Country :
{{ $port->country->country_name }}

<br>

City :
{{ $port->city }}

<br>

Status :
{{ $port->status }}

`);

@endforeach

var group = new L.featureGroup();

map.eachLayer(function(layer){

    if(layer instanceof L.Marker){

        group.addLayer(layer);

    }

});

if(group.getLayers().length){

    map.fitBounds(group.getBounds().pad(.2));

}

</script>

</div>

</div>

</body>
</html>