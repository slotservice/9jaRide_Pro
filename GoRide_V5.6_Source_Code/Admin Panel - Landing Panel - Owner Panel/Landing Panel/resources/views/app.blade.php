<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="{{ asset('img/fav.png') }}">
    <title>{{ config('app.name','9jaRide Pro') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <style>
        @keyframes fadeInUp { from { opacity:0; transform:translateY(40px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeInLeft { from { opacity:0; transform:translateX(-40px); } to { opacity:1; transform:translateX(0); } }
        @keyframes fadeInRight { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
        @keyframes scaleIn { from { opacity:0; transform:scale(0.9); } to { opacity:1; transform:scale(1); } }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        .anim-hidden { opacity:0; }
        .anim-fadeInUp { animation: fadeInUp 0.8s ease forwards; }
        .anim-fadeInLeft { animation: fadeInLeft 0.8s ease forwards; }
        .anim-fadeInRight { animation: fadeInRight 0.8s ease forwards; }
        .anim-scaleIn { animation: scaleIn 0.6s ease forwards; }
        .anim-fadeIn { animation: fadeIn 1s ease forwards; }
        .feature-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .feature-card:hover { transform: translateY(-8px); box-shadow: 0 8px 25px rgba(0,0,0,0.12) !important; }
        .vision-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .vision-card:hover { transform: translateY(-4px); box-shadow: 0 6px 20px rgba(0,0,0,0.1) !important; }
        .step-circle { transition: transform 0.3s ease; }
        .step-circle:hover { transform: scale(1.15); }
        .gold-btn { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .gold-btn:hover { transform: scale(1.05); box-shadow: 0 4px 15px rgba(212,175,55,0.4); }
    </style>
</head>

<body class="fixed-bottom-bar">

    <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
        {{trans('lang.processing')}}
    </div>
    
    <div id="header-template"></div>
    
    <main id="body-template">
        @yield('content')
    </main>

    <div id="footer-template"></div>

    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-storage-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>
    <script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
    <script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
    <script src="{{ asset('js/crypto-js.js') }}"></script>
    <script src="{{ asset('js/jquery.cookie.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.js') }}"></script>

    <script type="text/javascript">

        var database = firebase.firestore();

        var headerRef= database.collection('settings').doc('headerTemplate');
        var footerRef= database.collection('settings').doc('footerTemplate');
        
        $(document).ready(function () {

            jQuery("#data-table_processing").show();

            $(document.body).on('click', '.redirecttopage', function () {
                var url = $(this).attr('data-url');
                window.location.href = url;
            });

            header = document.getElementById('header-template');
            header.innerHTML = '';
            headerRef.get().then(async function (snapshots) {
                html = '';
                var data = snapshots.data();
                html = data.headerTemplate;
                if (html != '') {
                    header.innerHTML = html;
                }
            });

            footer = document.getElementById('footer-template');
            footer.innerHTML = '';

            footerRef.get().then(async function (snapshots) {
                html = '';
                var data = snapshots.data();
                html = data.footerTemplate;
                if (html != '') {
                    footer.innerHTML = html;
                }
                jQuery("#data-table_processing").hide();
            });

        });
    </script>

    @yield('scripts')

    <script>
        function initScrollAnimations() {
            var animEls = document.querySelectorAll('.anim-hidden');
            if (animEls.length === 0) return;
            var anims = ['anim-fadeInUp','anim-fadeInLeft','anim-fadeInRight','anim-scaleIn'];
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var el = entry.target;
                        var parent = el.parentElement;
                        var siblings = parent ? parent.querySelectorAll('.anim-hidden') : [];
                        var idx = Array.from(siblings).indexOf(el);
                        if (idx < 0) idx = 0;
                        var anim = anims[idx % anims.length];
                        el.style.animationDelay = (idx * 0.15) + 's';
                        el.classList.add(anim);
                        el.classList.remove('anim-hidden');
                        observer.unobserve(el);
                    }
                });
            }, { threshold: 0.1 });
            animEls.forEach(function(el) { observer.observe(el); });
        }

        // Run after Firebase content loads (with retry)
        var animRetries = 0;
        var animInterval = setInterval(function() {
            var hidden = document.querySelectorAll('.anim-hidden');
            if (hidden.length > 0 || animRetries > 20) {
                clearInterval(animInterval);
                if (hidden.length > 0) initScrollAnimations();
            }
            animRetries++;
        }, 500);
    </script>

</body>

</html>