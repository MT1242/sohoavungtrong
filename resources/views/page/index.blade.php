<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ Voyager::setting('site.title') }}</title>

    <link rel="stylesheet" href="https://unpkg.com/maplibre-gl/dist/maplibre-gl.css" />
    <link rel="stylesheet" href="{{ asset('Test/src/css/style.css') }}" />
    <script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>
</head>

<body>
    <!-- Buttons -->
    <button id="completeDrawing" style="position: absolute; top: 10px; right: 10px; z-index: 1000;">Hoàn thành</button>
    <button id="cancelDrawing" style="position: absolute; top: 50px; right: 10px; z-index: 1000;">Hủy</button>

    <!-- Dropdown Menu -->
    <div class="dropdown">
        <button onclick="myFunction()" class="dropbtn">Danh sách các hành động</button>
        <div id="myDropdown" class="dropdown-content">
            <a href="#" id="drawPolygon">Vẽ Hình</a>
            <a href="#">Link 2</a>
            <a href="#">Link 3</a>
        </div>
    </div>

    <!-- Modal Box -->
    <div id="infoBox" style="display:none; z-index:100; position:fixed; //*top:30%*//; left:50%; transform:translate(-50%, -50%); background:#fff; padding:20px; border-radius:8px; box-shadow: 0 0 10px rgba(0,0,0,0.3); width: max-content;">
        <h3>Nhập thông tin vùng trồng</h3>

        <label for="regionName">Tên vùng:</label>
        <input type="text" id="regionName" /><br>

        <label for="soilType">Loại đất:</label>
        <select id="soilType">
            <option value="">Chọn loại đất</option>
            <option value="Đất thịt">Đất thịt</option>
            <option value="Đất cát">Đất cát</option>
            <option value="Đất sét">Đất sét</option>
        </select><br>

        <label for="manager">Người quản lý:</label>
        <select id="manager">
            <option value="">Chọn người quản lý</option>
            @foreach ($managers as $manager)
                <option value="{{ $manager->id }}">{{ $manager->name }}</option>
            @endforeach
        </select><br><br>

        <label for="regionColor">Chọn màu vùng:</label>
        <input type="color" id="regionColor" value="#008000"><br><br>

        <label for="regionInfo">Thông tin thêm:</label>
    <textarea id="regionInfo" rows="4" cols="50"></textarea>

       <div class="btn_container" style="display: flex">
        <button id="saveRegion" style="margin-right: 10px">Lưu</button>
        <button id="closeModal" style="background: #f44336; color: white; border: none; padding: 5px 10px; font-size: 14px; cursor: pointer; border-radius: 5px;">Cancel</button>
       </div>
    </div>
    <div class="modal-overlay"></div>
    <!-- Map Container -->
    <div id="map"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('Test/src/js/getlocation.js') }}"></script>
    <script>
        var regions = @json($regions);
    </script>
    <script src="{{ asset('Test/src/js/vitri.js') }}"></script>
    <script src="{{ asset('Test/src/js/vehinh.js') }}"></script>

    <script>
        document.getElementById("closeModal").addEventListener("click", function() {
            document.getElementById("infoBox").style.display = "none";
            location.reload();
        });
    </script>
</body>

</html>
