<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>
    <link href="https://unpkg.com/maplibre-gl/dist/maplibre-gl.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('Test/src/css/style.css') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ Voyager::setting('site.title') }}</title>
</head>

<body>
    <button id="completeDrawing" style="position: absolute; top: 10px; right: 10px; z-index: 1000;">Hoàn thành</button>
    <button id="cancelDrawing" style="position: absolute; top: 50px; right: 10px; z-index: 1000;">Hủy</button>

    <div class="dropdown">
        <button onclick="myFunction()" class="dropbtn">Danh sách các hành động </button>
        <div id="myDropdown" class="dropdown-content">
            <a href="#" id="drawPolygon">Vẽ Hình</a>
            <a href="#">Link 2</a>
            <a href="#">Link 3</a>
        </div>
    </div>
    <div id="infoBox"
        style="display:none;z-index:100; position:fixed; top:30%; left:50%; transform:translate(-50%, -50%); background:#fff; padding:20px; border-radius:8px; box-shadow: 0 0 10px rgba(0,0,0,0.3);">
        <h3>Nhập thông tin vùng trồng</h3>
        <label>Tên vùng:</label>
        <input type="text" id="regionName" /><br>
        <label>Loại đất:</label>
        <input type="text" id="soilType" /><br>
        <label>Người quản lý:</label>
        <input type="text" id="manager" /><br><br>
        <button id="saveRegion">Lưu</button>
    </div>

    <div id="map"></div>
    <script src="{{ asset('Test/src/js/getlocation.js') }}"></script>
    <script>
        var regions = @json($regions);

        map.on('load', () => {
            regions.forEach(region => {
                if (region.coordinates) {
                    var coords = JSON.parse(region.coordinates);

                    // Đóng vòng nếu chưa có
                    if (coords.length > 2) {
                        coords.push(coords[0]);
                    }

                    // Kiểm tra và thêm Polygon
                    if (coords.length > 2) {
                        let sourceId = `region-${region.id}`;
                        let fillId = `fill-${region.id}`;
                        let lineId = `line-${region.id}`;

                        map.addSource(sourceId, {
                            type: 'geojson',
                            data: {
                                type: 'Feature',
                                geometry: {
                                    type: 'Polygon',
                                    coordinates: [coords.map(coord => [coord.lng, coord.lat])]
                                }
                            }
                        });

                        map.addLayer({
                            id: fillId,
                            type: 'fill',
                            source: sourceId,
                            paint: {
                                'fill-color': '#008000', // Màu xanh lá
                                'fill-opacity': 0.4
                            }
                        });

                        map.addLayer({
                            id: lineId,
                            type: 'line',
                            source: sourceId,
                            layout: {
                                'line-join': 'round',
                                'line-cap': 'round'
                            },
                            paint: {
                                'line-color': '#ff0000',
                                'line-width': 2
                            }
                        });
                    }
                }
            });
        });
    </script>

    <script>
        let drawing = false;
        let coordinates = [];
        let tempLine = null;
        let markers = [];

        document.getElementById("drawPolygon").addEventListener("click", startDrawing);
        document.getElementById("saveRegion").addEventListener("click", saveRegion);
        document.addEventListener("keydown", handleKeydown);

        map.on("click", addPoint);
        map.on("dblclick", completeDrawing);

        function startDrawing() {
            drawing = true;
            coordinates = [];
            clearMarkersAndLines();
            alert("Nhấp để vẽ, nhấn đúp để hoàn tất, ESC để hủy.");
        }

        function addPoint(e) {
            if (!drawing) return;

            let point = {
                lat: e.lngLat.lat,
                lng: e.lngLat.lng
            };
            coordinates.push(point);

            let marker = new maplibregl.Marker({
                    color: "blue"
                })
                .setLngLat([point.lng, point.lat])
                .addTo(map);
            markers.push(marker);

            drawTemporaryLine();
        }

        function drawTemporaryLine() {
            if (coordinates.length < 2) return;

            removeTempLine();

            let lineCoords = coordinates.map(coord => [coord.lng, coord.lat]);

            map.addSource("temp-line", {
                type: "geojson",
                data: {
                    type: "Feature",
                    geometry: {
                        type: "LineString",
                        coordinates: lineCoords
                    }
                },
            });

            map.addLayer({
                id: "temp-line",
                type: "line",
                source: "temp-line",
                layout: {},
                paint: {
                    "line-color": "#ff0000",
                    "line-width": 2
                }
            });

            tempLine = "temp-line";
        }

        function completeDrawing() {
            if (!drawing || coordinates.length < 3) return;

            drawing = false;
            coordinates.push(coordinates[0]); // Đóng vùng

            removeTempLine();
            clearMarkersAndLines();

            let polygonId = "polygon-" + Date.now();
            let sourceId = "source-" + Date.now();

            map.addSource(sourceId, {
                type: "geojson",
                data: {
                    type: "Feature",
                    geometry: {
                        type: "Polygon",
                        coordinates: [coordinates.map(coord => [coord.lng, coord.lat])]
                    }
                }
            });

            // Lớp fill (Nền xanh lá cây)
            map.addLayer({
                id: polygonId,
                type: "fill",
                source: sourceId,
                layout: {},
                paint: {
                    "fill-color": "#008000", // Màu nền xanh lá
                    "fill-opacity": 0.5
                }
            });

            // Lớp line (Viền đỏ)
            map.addLayer({
                id: polygonId + "-border",
                type: "line",
                source: sourceId,
                layout: {},
                paint: {
                    "line-color": "#ff0000", // Viền màu đỏ
                    "line-width": 2
                }
            });

            document.getElementById("infoBox").style.display = "block";
        }

        function handleKeydown(e) {
            if (e.key === "Escape") cancelDrawing();
        }

        function cancelDrawing() {
            drawing = false;
            coordinates = [];
            clearMarkersAndLines();
            document.getElementById("infoBox").style.display = "none";
            alert("Đã hủy vẽ.");
        }

        function saveRegion() {
            let name = document.getElementById("regionName").value;
            let soilType = document.getElementById("soilType").value;
            let manager = document.getElementById("manager").value;

            if (!name || !soilType || !manager) {
                alert("Vui lòng nhập đầy đủ thông tin!");
                return;
            }

            fetch("/save-region", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    },
                    body: JSON.stringify({
                        name,
                        soilType,
                        manager,
                        coordinates
                    }),
                })
                .then(response => response.json())
                .then(() => {
                    alert("Lưu thành công!");
                    document.getElementById("infoBox").style.display = "none";
                })
                .catch(error => console.error("Lỗi khi lưu:", error));
        }

        function clearMarkersAndLines() {
            markers.forEach(marker => marker.remove());
            markers = [];
            removeTempLine();
        }

        function removeTempLine() {
            if (tempLine) {
                map.removeLayer(tempLine);
                map.removeSource(tempLine);
                tempLine = null;
            }
        }
    </script>
</body>

</html>
