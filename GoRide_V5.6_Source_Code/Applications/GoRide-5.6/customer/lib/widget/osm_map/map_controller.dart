import 'dart:async';
import 'dart:convert';

import 'package:customer/utils/utils.dart';
import 'package:customer/widget/osm_map/place_model.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:geolocator/geolocator.dart';
import 'package:get/get.dart';
import 'package:http/http.dart' as http;
import 'package:latlong2/latlong.dart';

class OSMMapController extends GetxController {
  final mapController = MapController();
  // Store only one picked place instead of multiple
  var pickedPlace = Rxn<PlaceModel>(); // Use Rxn to hold a nullable value
  var searchResults = [].obs;
  var isSearching = false.obs;
  // True after a search completed with no matches — used to prompt the user to
  // tap the map instead.
  var searchedNoResult = false.obs;

  Timer? _debounce;

  // Called on every keystroke. Debounced so we don't hammer Nominatim (its
  // public API rate-limits to ~1 request/second; firing per keystroke returned
  // empty results and left the user stuck on "No Location Picked").
  void onSearchChanged(String query) {
    _debounce?.cancel();
    final q = query.trim();
    if (q.length < 3) {
      searchResults.clear();
      searchedNoResult.value = false;
      return;
    }
    _debounce = Timer(const Duration(milliseconds: 600), () => searchPlace(q));
  }

  Future<void> searchPlace(String query) async {
    query = query.trim();
    if (query.length < 3) {
      searchResults.clear();
      return;
    }
    isSearching.value = true;
    searchedNoResult.value = false;
    try {
      // First bias the search near the current map position; if nothing is
      // found (common for smaller towns), retry across all of Nigeria.
      List data = await _nominatimSearch(query, withViewbox: true);
      if (data.isEmpty) {
        data = await _nominatimSearch(query, withViewbox: false);
      }
      searchResults.value = data;
      searchedNoResult.value = data.isEmpty;
    } catch (e) {
      searchResults.clear();
      searchedNoResult.value = true;
    } finally {
      isSearching.value = false;
    }
  }

  Future<List> _nominatimSearch(String query, {required bool withViewbox}) async {
    final center = pickedPlace.value?.coordinates;
    final viewboxParam = (withViewbox && center != null)
        ? '&viewbox=${center.longitude - 2},${center.latitude + 2},${center.longitude + 2},${center.latitude - 2}'
        : '';
    final url = Uri.parse(
        'https://nominatim.openstreetmap.org/search?q=${Uri.encodeComponent(query)}&format=json&addressdetails=1&limit=10&countrycodes=ng$viewboxParam');

    final response = await http.get(url, headers: {
      'User-Agent': 'NjaRidePro/1.0 (com.njaridepro.customer)',
    }).timeout(const Duration(seconds: 20));

    if (response.statusCode == 200) {
      final decoded = json.decode(response.body);
      return decoded is List ? decoded : [];
    }
    return [];
  }

  void selectSearchResult(Map<String, dynamic> place) {
    final lat = double.parse(place['lat'].toString());
    final lon = double.parse(place['lon'].toString());
    final address = place['display_name'] ?? '';
    final addr = place['address'] is Map ? place['address'] as Map : const {};
    final city = addr['city'] ?? addr['town'] ?? addr['village'] ?? addr['state_district'] ?? '';

    // Store only the selected place
    pickedPlace.value = PlaceModel(coordinates: LatLng(lat, lon), address: address.toString(), city: (city ?? '').toString());
    searchResults.clear();
    searchedNoResult.value = false;
  }

  void addLatLngOnly(LatLng coords) async {
    final address = await _getAddressFromLatLng(coords);
    final addr = address['address'] is Map ? address['address'] as Map : const {};
    final city = addr['city'] ?? addr['town'] ?? addr['village'] ?? addr['state_district'] ?? '';
    pickedPlace.value = PlaceModel(coordinates: coords, address: (address['display_name'] ?? 'Unknown location').toString(), city: (city ?? '').toString());
    searchedNoResult.value = false;
  }

  Future<dynamic> _getAddressFromLatLng(LatLng coords) async {
    final url = Uri.parse('https://nominatim.openstreetmap.org/reverse?lat=${coords.latitude}&lon=${coords.longitude}&format=json&addressdetails=1');

    try {
      final response = await http.get(url, headers: {
        'User-Agent': 'NjaRidePro/1.0 (com.njaridepro.customer)',
      }).timeout(const Duration(seconds: 20));

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return data;
      }
    } catch (e) {
      // fall through to empty
    }
    return {};
  }

  void clearAll() {
    pickedPlace.value = null; // Clear the selected place
  }

  @override
  void onClose() {
    _debounce?.cancel();
    super.onClose();
  }

  @override
  void onInit() {
    // TODO: implement onInit
    super.onInit();
    getCurrentLocation();
  }

  getCurrentLocation() async {
    Position? location = await Utils.getCurrentLocation();
    LatLng latlng = LatLng(location.latitude, location.longitude);
    addLatLngOnly(LatLng(location.latitude, location.longitude));
    mapController.move(latlng, mapController.camera.zoom);
  }
}
