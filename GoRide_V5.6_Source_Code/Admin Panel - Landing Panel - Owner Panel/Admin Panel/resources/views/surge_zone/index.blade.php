@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.surge_zone_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.surge_zone')}}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div id="map" style="height: 600px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>

var database = firebase.firestore();
var mapInstance = null;
var mapZones = [];
var mapType = 'OFFLINE';
var default_lat = getCookie('default_latitude') || 22.3039;
var default_lng = getCookie('default_longitude') || 70.8022;

// Fetch all zones
async function fetchZones() {
    const snapshot = await database.collection('surge_zones').get();
    const zones = [];
    snapshot.forEach(doc => zones.push(doc.data()));
    return zones;
}

// Initialize map
async function initMap() {
    const settingsSnap = await database.collection('settings').doc('globalValue').get();
    const data = settingsSnap.data();
    if(data && data.selectedMapType && data.selectedMapType.toLowerCase() === 'osm'){
        mapType = "OFFLINE";
    } else {
        mapType = "ONLINE";
    }
    if (mapType === "ONLINE") {
        mapInstance = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,
            center: {lat: default_lat, lng: default_lng},
            mapTypeControl: false
        });
    } else {
        mapInstance = L.map('map').setView([default_lat, default_lng], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(mapInstance);
    }
}

// Add a single zone to the map
async function addZoneToMap(zone){

    const multiplier = zone.surgeMultiplier || 1;
    const color = surgeColor(multiplier);
    const lat = zone.latitude;
    const lng = zone.longitude;
    const radius = parseFloat(zone.radiusKm) * 1000;
    const zoneName = zone.name?.find(n => n.type === 'en')?.name || 'Zone';
    const editUrl = `/surge-zone/edit/${zone.id}`; 
    const onlineDrivers = zone.onlineDrivers;
    const activeRequests = zone.activeRequests;

    if(mapType === "ONLINE"){
        // Google Maps circle
        const circle = new google.maps.Circle({
            strokeColor: color,
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: color,
            fillOpacity: 0.35,
            map: mapInstance,
            center: {lat, lng},
            radius: radius
        });

        // Info window label
        const label = new google.maps.InfoWindow({
            content: `<b>Zone: ${zone.name?.find(n => n.type === 'en')?.name || 'Zone'}</b><br>Surge: ${multiplier}x<br>Active Requests: ${activeRequests}<br>Online Drivers: ${onlineDrivers}`,
            position: {lat, lng}
        });
        label.open(mapInstance);

        circle.addListener('click', function() {
            window.location.href = editUrl;
        });

        mapZones.push({circle, label});
    } else {
        // Leaflet circle
        const circle = L.circle([lat, lng], {
            color: color,
            fillColor: color,
            fillOpacity: 0.35,
            radius: radius,
            weight: 2
        }).addTo(mapInstance);

        // Label
        const label = L.marker([lat, lng], {
            icon: L.divIcon({
                className: 'surge-label',
                html: `<div style="background: rgba(255,255,255,0.9); padding: 4px 8px; border-radius: 4px; font-weight: bold; border: 1px solid ${color}">Zone: ${zone.name?.find(n => n.type === 'en')?.name || 'Zone'}<br>Surge: ${multiplier}x<br>Active Requests: ${activeRequests}<br>Online Drivers: ${onlineDrivers}</div>`,
                iconSize: [200,30],
                iconAnchor: [50,15]
            }),
            interactive: false
        }).addTo(mapInstance);

        circle.on('click', function() {
            window.location.href = editUrl;
        });

        mapZones.push({circle, label});
    }

    if(zone.orderIds && zone.orderIds.length > 0){

        const batch = []; 
        const orders = [];

        for(const orderId of zone.orderIds){
            const orderSnap = await database.collection('orders').doc(orderId).get();
            if(orderSnap.exists){
                const order = orderSnap.data();
                orders.push(order);
                batch.push(database.collection('users').doc(order.userId).get());
            }
        }

        const usersSnapshots = await Promise.all(batch);

        orders.forEach((order, index) => {
            const userSnap = usersSnapshots[index];
            const user = userSnap.exists ? userSnap.data() : { fullName: 'Unknown User' };
            const oLat = order.sourceLocationLAtLng.latitude;
            const oLng = order.sourceLocationLAtLng.longitude;

            const popupContent = `
                <b>Order ID:</b> ${order.id}<br>
                <b>User Name:</b> ${user.fullName || 'Unknown'}<br>
                <b>User Address:</b> ${order.sourceLocationName || 'N/A'}
            `;

            if(mapType === "ONLINE"){
                const marker = new google.maps.Marker({
                    position: {lat: oLat, lng: oLng},
                    map: mapInstance,
                    title: order.sourceLocationName || ''
                });

                const infoWindow = new google.maps.InfoWindow({ content: popupContent });
                marker.addListener('click', () => infoWindow.open(mapInstance, marker));

            } else {
                L.marker([oLat, oLng])
                .addTo(mapInstance)
                .bindPopup(popupContent);
            }
        });
    }
}

// Fit map bounds to all zones
function fitMapBounds() {
    if(mapZones.length === 0) return;
    if(mapType === "ONLINE"){
        const bounds = new google.maps.LatLngBounds();
        mapZones.forEach(z => bounds.union(z.circle.getBounds()));
        mapInstance.fitBounds(bounds);
    } else {
        const group = L.featureGroup(mapZones.map(z => z.circle));
        mapInstance.fitBounds(group.getBounds());
    }
}

$(document).ready(async function(){
    const zones = await fetchZones();
    await initMap();
    zones.forEach(zone => addZoneToMap(zone));
    fitMapBounds();
});

// Color based on surge multiplier
function surgeColor(multiplier){
    multiplier = parseFloat(multiplier);
    if(multiplier <= 1.0) return "#00FF00";                      // Green - Normal
    if(multiplier > 1.0 && multiplier <= 1.5) return "#ADFF2F";  // GreenYellow - Low Surge
    if(multiplier > 1.5 && multiplier <= 2.0) return "#FFFF00";  // Yellow - Medium Surge
    if(multiplier > 2.0 && multiplier <= 2.5) return "#FFA500";  // Orange - High Surge
    if(multiplier > 2.5) return "#FF0000";                       // Red - Extreme Surge
}

</script>

@endsection
