<!doctype html>
<html lang="en" >
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="//localhost:8010/assets/bootstrap/css/6?v=ce558abb65444f0f343e40e3088643beb0e507409cbc461c2e26e7b12147242ebaef1880b0dcbdf061098f172cdcb04a" integrity="sha384-zlWKu2VETw80PkDjCIZDvrDlB0CcvEYcLibnsSFHJC667xiAsNy98GEJjxcs3LBK" crossorigin="anonymous">
	<style>
      .page { display: flex; flex-direction: column; height: 100vh; height: 100dvh; overflow: hidden; }
      .page > nav, .page > footer { flex: 0 0 auto; }
      .page > div.container-fluid { display: flex; flex-direction: column; flex: 1 1 0; min-height: 0; }
      .page > div.container-fluid > .row { flex: 1 1 0; flex-wrap: nowrap; min-height: 0; }
      .page > div.container-fluid > .row > aside,
      .page > div.container-fluid > .row > main { min-height: 0; min-width: 0; overflow: auto; }
      @media (width < 768px) {
        .page > div.container-fluid > .row { flex-direction: column; }
        .page > div.container-fluid > .row > aside { flex: 1 1 0; }
        .page > div.container-fluid > .row > main { flex: 2 1 0; }
      }
      @media print {
        .page { display: block; height: auto; overflow: visible; }
        .page > div.container-fluid, .page > div.container-fluid > .row { display: block; }
        .page > div.container-fluid > .row > main { overflow: visible; width: 100%; }
        [data-role="content-secondary"] { display: none !important; }
      }
      html {
        scrollbar-gutter: auto !important;
      }
      </style>
	<link rel="stylesheet" href="//localhost:8010/assets/bootstrap/icons">
	<script src="//localhost:8010/assets/jquery/4"></script>
	<script src="//localhost:8010/assets/brayworth/js"></script>
	<script src="//localhost:8010/assets/brayworth/dopo"></script>
	<title>Example System</title>

</head>
<body>
<div class="page">

<nav class="navbar bg-1 fg-2">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">Navbar</span>
  </div>
</nav>
	<div class="container-fluid"><div class="row">
		<aside class="md:col-3 py-3 pb-4 fg-secondary bg-1" data-role="content-secondary">
			<div class="sidebar pt-3 pb-5"><!-- theme start -->

<ul class="nav flex-column">
  <li class="nav-item">
    <a class="nav-link active" aria-current="page" href="#">Active</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">Link</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">Link</a>
  </li>
  <li class="nav-item">
    <a class="nav-link disabled" aria-disabled="true">Disabled</a>
  </li>
</ul>

			</div><!-- theme end -->

		</aside>
		<main class="col " data-role="content-primary">

<h1>Hello World</h1>

<h2>Icons</h2>

<ul>
  <li><i class="bi bi-alarm"></i> Alarm Icon</li>
</ul>
		</main>

	</div></div>

<footer class="container-fluid border-top py-3 text-center fg-2">
	<small>Bootstrap 6 demo</small>
</footer>
	<script type="module" src="//localhost:8010/assets/bootstrap/js/6?v=4360f1972805ced3974b1121f0c8ed6df7bac24546bde5130c87af8a11f457c82b1a4bdef7c3f1c850cbe48bf97f0869" integrity="sha384-Q2DxlygFztOXSxEh8Mjtbfe6wkVGveUTDIevihH0V8grGkve98PxyFDL5Iv5fwhp" crossorigin="anonymous"></script>

</div>
</body>

</html>
