


<div class="container-upload">
  <div class="header-upload">
    <h1 data-i18n="uploadTitle">Upload File</h1>
    <p data-i18n="uploadSubtitle">Drag & drop files or click to browse</p>
  </div>
  <div class="dropbox" id="dropbox">
    <div class="dropbox-icon">📁</div>
    <div class="dropbox-text">Drop files here</div>
    <div class="dropbox-subtext">Supported: JPG, PNG, PDF, DOCX, ZIP, STL, OBJ, GLB, etc.</div>
    <button type="button" class="browse-btn" tabindex="0">Browse Files</button>
    <input type="file" id="fileInput" multiple hidden accept="*/*">
  </div>
  <div class="upload-stats" id="uploadStats" style="display:none"></div>
  <div class="empty-state" id="emptyState">No files uploaded yet</div>
  <div class="file-previews" id="filePreviews"></div>
  <div id="viewer3D" style="display:none;width:100%;max-width:450px;margin:auto;margin-top:2rem;"></div>
  <div id="viewer3DInfo" style="text-align:center;margin-bottom:2rem;"></div>
</div>

<button id="realUploadBtn" class="browse-btn" style="margin:30px auto 0 auto;display:block;width:170px;font-size:1.05rem;">
  <i class="bi bi-cloud-arrow-up"></i> Upload Files
</button>

<div class="upload-notification" id="notification">
  <span id="notificationText">File uploaded successfully!</span>
  <div class="notification-progress"></div>
</div>


    <script>
        const canvas = document.getElementById('preview3d');
        canvas.width = 800;
        canvas.height = 600;

        let renderer, scene, camera, controls;

        document.getElementById('input_stp').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(event) {
                // Limpia la escena si hay una previa
                if (renderer) renderer.dispose();

                scene = new THREE.Scene();
                camera = new THREE.PerspectiveCamera(75, canvas.width / canvas.height, 0.1, 1000);
                renderer = new THREE.WebGLRenderer({canvas: canvas, antialias: true});
                renderer.setClearColor(0x222222);
                renderer.setSize(canvas.width, canvas.height);

                // Luz
                const light = new THREE.DirectionalLight(0xffffff, 1);
                light.position.set(10, 10, 10);
                scene.add(light);

                // Leer y parsear archivo STEP
                const content = event.target.result;
                const model = window.STEP.parse(content);

                // Para cada shape, conviértelo a Mesh y agrégalo a la escena
                model.shapes.forEach(shape => {
                    // Material simple
                    const material = new THREE.MeshPhongMaterial({color: 0x0080ff, shininess: 80});
                    // shape es un BufferGeometry
                    const mesh = new THREE.Mesh(shape, material);
                    scene.add(mesh);
                });

                camera.position.set(0, 0, 100);

                function animate() {
                    requestAnimationFrame(animate);
                    scene.rotation.y += 0.01;
                    renderer.render(scene, camera);
                }
                animate();
            };
            reader.readAsText(file);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.149.0/build/three.min.js"></script>
<script type="module">
  import { STEPLoader } from 'https://cdn.jsdelivr.net/npm/three@0.149.0/examples/jsm/loaders/STEPLoader.js';
  window.STEPLoader = STEPLoader;
</script>

<!-- Three.js -->
<script src="https://cdn.jsdelivr.net/npm/three@0.149.0/build/three.min.js"></script>
<!-- three-step-parser (MUST BE BEFORE preview3d.js) -->
<script src="https://cdn.jsdelivr.net/npm/three-step-parser@1.0.1/dist/step.min.js"></script>
<!-- Tu script principal de preview -->
<script src="js/preview3d.js"></script>



