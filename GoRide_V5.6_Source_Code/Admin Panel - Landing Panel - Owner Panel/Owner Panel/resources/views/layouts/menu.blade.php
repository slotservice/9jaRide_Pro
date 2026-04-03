
<div class="sidebar-search">
    <input type="text" id="sideBarSearchInput" placeholder="{{trans('lang.search_menu')}}" autocomplete="one-time-code"
        onkeyup="filterMenu()">
</div>
<nav class="sidebar-nav">

    <ul id="sidebarnav">

        <li>
            <a class="waves-effect waves-dark" href="{!! url('dashboard') !!}" aria-expanded="false">
                <i class="mdi mdi-home"></i>
                <span class="hide-menu">{{trans('lang.dashboard')}}</span>
            </a>
        </li>

    </ul>

    <p class="web_version"></p>

</nav>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-storage.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-database.js"></script>
<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>

<script>
    function filterMenu() {
        const searchInput=document.getElementById('sideBarSearchInput').value.toLowerCase();
        const menuItems=document.getElementById('sidebarnav').getElementsByTagName('li');
        for(let i=0;i<menuItems.length;i++) {
            const item=menuItems[i];
            const itemText=item.textContent.toLowerCase();
            if(itemText.indexOf(searchInput)===-1) {
                item.style.display='none';
            } else {
                item.style.display='';
            }
        }
    }    
</script>