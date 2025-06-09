// preview3d.js
// preview3d.js

// al inicio del fichero

window.occtImportWasmUrl =
  "https://cdn.jsdelivr.net/npm/@xeokit/occt-import@2.6.1/dist/occt-import-js.wasm";

(function uploaderModule() {
  const dropbox = document.getElementById("dropbox");
  if (!dropbox) return;

  const fileInput = document.getElementById("fileInput");
  const filePreviews = document.getElementById("filePreviews");
  const notification = document.getElementById("notification");
  const browseBtn = document.querySelector(".browse-btn");
  const uploadStats = document.getElementById("uploadStats");
  const emptyState = document.getElementById("emptyState");
  const viewer3D = document.getElementById("viewer3D");
  let fileCount = 0,
    totalSize = 0,
    pendingFiles = [];

  filePreviews.innerHTML = "";
  if (uploadStats) uploadStats.innerHTML = "";
  if (viewer3D) viewer3D.innerHTML = "";
  fileCount = 0;
  totalSize = 0;
  pendingFiles = [];

  function updateDisplay() {
    if (fileCount > 0) {
      uploadStats.innerHTML = `
        <div class="stats-title">Upload Summary</div>
        <div class="stats-info">
          <div class="stat-item">
            <div class="stat-value">${fileCount}</div>
            <div class="stat-label">Files</div>
          </div>
          <div class="stat-item">
            <div class="stat-value">${formatFileSize(totalSize)}</div>
            <div class="stat-label">Total Size</div>
          </div>
        </div>
        <div class="progress-bar">
          <div class="progress-fill" id="progressFill" style="width:100%"></div>
        </div>
      `;
      uploadStats.style.display = "flex";
      emptyState.style.display = "none";
    } else {
      uploadStats.style.display = "none";
      emptyState.style.display = "block";
    }
  }

  dropbox.addEventListener("dragover", (e) => {
    e.preventDefault();
    dropbox.classList.add("dragover");
  });
  dropbox.addEventListener("dragleave", () =>
    dropbox.classList.remove("dragover")
  );
  dropbox.addEventListener("drop", (e) => {
    e.preventDefault();
    dropbox.classList.remove("dragover");
    handleFiles(e.dataTransfer.files);
  });
  dropbox.addEventListener("click", () => fileInput.click());
  browseBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    fileInput.click();
  });
  fileInput.addEventListener("change", (e) => handleFiles(e.target.files));

  function handleFiles(files) {
    Array.from(files).forEach((file) => {
      pendingFiles.push(file);
      displayFilePreview(file);
      if (is3DFile(file.name)) setTimeout(() => show3DPreview(file), 250);
    });
  }

  function displayFilePreview(file) {
    const reader = new FileReader();
    const fileId = `file-${Date.now()}-${Math.random()}`;
    reader.onload = function (event) {
      const preview = document.createElement("div");
      preview.classList.add("file-preview");
      preview.id = fileId;

      let previewContent = "";
      if (file.type.startsWith("image/")) {
        previewContent = `<img src="${event.target.result}" class="preview-img">`;
      } else if (isPdf(file.name)) {
        previewContent = `<div class="file-icon">📄</div>`;
      } else if (isWord(file.name)) {
        previewContent = `<div class="file-icon">📝</div>`;
      } else if (is3DFile(file.name)) {
        // Si es .stp o .step muestra un mini canvas de preview
        if (/\.(stp|step)$/i.test(file.name)) {
          const canvasId = `canvas-${fileId}`;
          previewContent = `<canvas id="${canvasId}" width="70" height="70" style="background:#eee;border-radius:8px;"></canvas>`;
          setTimeout(() => renderStepMiniPreview(file, canvasId), 50);
        } else {
          previewContent = `<div class="file-icon">🧊</div>`;
        }
      } else {
        previewContent = `<div class="file-icon">📦</div>`;
      }

      preview.innerHTML = `
      <div class="preview-img-container">${previewContent}</div>
      <div class="file-info">
        <div class="file-name">${file.name}</div>
        <div class="file-size">${formatFileSize(file.size)}</div>
        <div class="file-actions">
          <button class="remove-btn">Remove</button>
        </div>
      </div>
    `;
      preview
        .querySelector(".remove-btn")
        .addEventListener("click", () => removeFile(fileId, file.size, file));
      filePreviews.appendChild(preview);

      fileCount++;
      totalSize += file.size;
      updateDisplay();
    };
    reader.readAsDataURL(file);
  }

  function removeFile(fileId, size, file) {
    const el = document.getElementById(fileId);
    if (el) {
      el.remove();
      fileCount--;
      totalSize -= size;
      pendingFiles = pendingFiles.filter((f) => f !== file);
      updateDisplay();
      showNotification("File removed", "success");
    }
    fileInput.value = "";
  }

  function formatFileSize(bytes) {
    if (bytes === 0) return "0 Bytes";
    const k = 1024,
      sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${(bytes / Math.pow(k, i)).toFixed(2)} ${sizes[i]}`;
  }

  function showNotification(message, type = "success") {
    clearTimeout(notification.timeout);
    notification.className = `upload-notification show ${type}`;
    document.getElementById("notificationText").textContent = message;
    const bar = notification.querySelector(".notification-progress");
    bar.style.transition = "transform 3s linear";
    bar.style.transform = "scaleX(1)";
    notification.timeout = setTimeout(
      () => notification.classList.remove("show"),
      3000
    );
  }

  function isPdf(name) {
    return /\.pdf$/i.test(name);
  }
  function isWord(name) {
    return /\.(doc|docx)$/i.test(name);
  }
  function is3DFile(name) {
    return /\.(stl|obj|gltf|glb|stp|step)$/i.test(name);
  }

  function show3DPreview(file) {
    viewer3D.innerHTML = "";
    viewer3D.style.display = "block";

    const width = viewer3D.clientWidth || 450;
    const height = 320;
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0xf5f6fa);

    const camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
    camera.position.set(0, 0, 160);

    scene.add(new THREE.AmbientLight(0xffffff, 1.1));
    const dirLight = new THREE.DirectionalLight(0xffffff, 0.7);
    dirLight.position.set(1, 2, 3);
    scene.add(dirLight);

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(width, height);
    viewer3D.appendChild(renderer.domElement);

    const ext = file.name.split(".").pop().toLowerCase();
    if (ext === "stp" || ext === "step") {
      const reader = new FileReader();
      reader.onload = function (e) {
        try {
          const text = e.target.result;
          // Usa three-step-parser
          const model = window.STEP.parse(text);
          if (model.shapes && model.shapes.length) {
            model.shapes.forEach((shape) => {
              const material = new THREE.MeshPhongMaterial({
                color: 0x0080ff,
                shininess: 80,
              });
              const mesh = new THREE.Mesh(shape, material);
              scene.add(mesh);
            });
          }
          // Animar y renderizar
          let frame = 0;
          function animate() {
            scene.rotation.y += 0.01;
            renderer.render(scene, camera);
            if (frame < 300) {
              frame++;
              requestAnimationFrame(animate);
            }
          }
          animate();
        } catch (err) {
          viewer3D.innerHTML = `
          <div style="color:red;text-align:center;padding:1em">
            Error rendering STEP:<br>${err.message || err}
          </div>`;
        }
      };
      reader.readAsText(file);
    } else {
      viewer3D.innerHTML =
        '<div class="text-center" style="padding:45px;">No preview available.</div>';
    }
  }

  updateDisplay();

  document
    .getElementById("realUploadBtn")
    .addEventListener("click", function () {
      if (pendingFiles.length === 0) {
        showNotification("No files to upload.", "error");
        return;
      }
      const form = new FormData();
      pendingFiles.forEach((f) => form.append("files[]", f));
      fetch("../question_2/views/upload_file.php", {
        method: "POST",
        body: form,
      })
        .then((r) => r.json())
        .then((data) => {
          if (data.success) {
            showNotification("Files uploaded!", "success");
            pendingFiles = [];
            filePreviews.innerHTML = "";
            fileCount = 0;
            totalSize = 0;
            updateDisplay();
          } else {
            showNotification(data.error || "Error uploading files", "error");
          }
        })
        .catch(() => showNotification("Error uploading files", "error"));
    });
})();
function renderStepMiniPreview(file, canvasId) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) return;

  const renderer = new THREE.WebGLRenderer({
    canvas: canvas,
    alpha: true,
    antialias: true,
  });
  renderer.setClearColor(0xf5f6fa, 0);

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(45, 1, 0.1, 1000);
  camera.position.set(0, 0, 80);
  scene.add(new THREE.AmbientLight(0xffffff, 1.2));
  const light = new THREE.DirectionalLight(0xffffff, 0.8);
  light.position.set(1, 2, 3);
  scene.add(light);

  // Parsear el STEP
  const reader = new FileReader();
  reader.onload = function (e) {
    try {
      const text = e.target.result;
      const model = window.STEP.parse(text); // Usa el parser cargado
      if (model.shapes && model.shapes.length) {
        model.shapes.forEach((shape) => {
          const material = new THREE.MeshPhongMaterial({
            color: 0x0080ff,
            shininess: 80,
          });
          const mesh = new THREE.Mesh(shape, material);
          scene.add(mesh);
        });
      }
      // Animación ligera para vista previa
      let frame = 0;
      function animate() {
        if (frame > 50) return; // Solo unos frames (mini preview)
        scene.rotation.y += 0.03;
        renderer.render(scene, camera);
        frame++;
        requestAnimationFrame(animate);
      }
      animate();
    } catch (err) {
      // Fallback si hay error
      canvas.parentNode.innerHTML = `<div class="file-icon">🧊</div>`;
    }
  };
  reader.readAsText(file);
}
