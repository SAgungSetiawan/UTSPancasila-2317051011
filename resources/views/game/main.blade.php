
@extends('layouts.app')

@section('content')
<!-- Canvas untuk Three.js -->
<div id="canvasContainer"></div>

<!-- UI Game -->
<div class="game-ui w-full h-full">
    <!-- Header -->
    <div class="absolute top-0 left-0 right-0 z-10 bg-gradient-to-b from-black via-black/80 to-transparent p-6">
        <div class="flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex gap-4">
                <div class="bg-blue-600 px-6 py-3 rounded-full font-bold text-xl text-white shadow-lg">
                    ⭐ Skor: <span id="skorDisplay">0</span>
                </div>
                <!-- <div class="bg-red-600 px-6 py-3 rounded-full font-bold text-xl text-white shadow-lg">
                    ❤️ Nyawa: <span id="nyawaDisplay">3</span>
                </div> -->
                <div class="bg-green-600 px-6 py-3 rounded-full font-bold text-xl text-white shadow-lg">
                    ✅ Progress: <span id="progressDisplay">0/6</span>
                </div>
            </div>
            <button id="btnPause" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold px-8 py-3 rounded-full shadow-lg transition-all">
                ⏸️ Pause
            </button>
        </div>
    </div>
    
    <!-- Kontrol Info -->
    <div class="absolute bottom-6 left-6 bg-black/70 text-white p-4 rounded-lg">
        <div class="font-bold mb-2">🎮 Kontrol:</div>
        <div class="text-sm">W = Maju | S = Mundur</div>
        <div class="text-sm">A = Kiri | D = Kanan</div>
    </div>
    
    <!-- Dialog NPC -->
    <div id="dialogNPC" class="hidden absolute bottom-32 left-1/2 transform -translate-x-1/2 bg-white rounded-2xl shadow-2xl p-6 max-w-lg w-full dialog-bubble animate-bounce-slow">
        <div class="flex items-start gap-4">
            <div id="dialogAvatar" class="w-16 h-16 rounded-full flex items-center justify-center text-white font-bold text-2xl flex-shrink-0">
            </div>
            <div class="flex-1">
                <h3 id="dialogNama" class="font-bold text-2xl mb-2"></h3>
                <p id="dialogTeks" class="text-gray-700 mb-4"></p>
                <button id="btnMulaiSoal" class="bg-green-500 hover:bg-green-600 text-white font-bold px-6 py-3 rounded-full w-full transition-all">
                    💬 Mulai Percakapan
                </button>
            </div>
        </div>
    </div>
    
    <!-- Modal Soal -->
    <div id="modalSoal" class="hidden fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header NPC -->
            <div class="flex items-center gap-4 mb-6 pb-4 border-b-2">
                <div id="soalAvatar" class="w-20 h-20 rounded-full flex items-center justify-center text-white font-bold text-3xl"></div>
                <div>
                    <h2 id="soalNama" class="text-3xl font-bold"></h2>
                    <p id="soalSila" class="text-gray-600 text-lg"></p>
                </div>
            </div>
            
            <!-- Pertanyaan -->
            <div class="bg-blue-50 rounded-xl p-6 mb-6">
                <p id="soalPertanyaan" class="text-xl font-semibold text-gray-800"></p>
            </div>
            
            <!-- Pilihan Jawaban -->
            <div id="containerPilihan" class="space-y-3"></div>
            
            <!-- Hasil Jawaban -->
            <div id="hasilJawaban" class="hidden rounded-2xl p-6 mt-6"></div>
        </div>
    </div>
    
    <!-- Modal Pause -->
    <div id="modalPause" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-3xl p-12 shadow-2xl text-center max-w-xl">
            <h1 class="text-5xl font-bold mb-8 text-gray-800">⏸️ GAME DIJEDA</h1>
            
            <div class="bg-blue-50 rounded-xl p-6 mb-8">
                <div class="grid grid-cols-2 gap-4 text-xl">
                    <div class="bg-white rounded-lg p-4">
                        <div class="text-gray-600">Skor</div>
                        <div id="pauseSkor" class="text-3xl font-bold text-blue-600">0</div>
                    </div>
                    <div class="bg-white rounded-lg p-4">
                        <div class="text-gray-600">Progress</div>
                        <div id="pauseProgress" class="text-3xl font-bold text-green-600">0/6</div>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-4 justify-center">
                <button id="btnResume" class="bg-green-500 hover:bg-green-600 text-white font-bold text-xl px-8 py-4 rounded-full shadow-lg">
                    ▶️ Lanjutkan
                </button>
                <button id="btnRestart" class="bg-orange-500 hover:bg-orange-600 text-white font-bold text-xl px-8 py-4 rounded-full shadow-lg">
                    🔄 Restart
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Data NPC dari Laravel
const dataNPC = @json($dataNPC);

// Variabel game
let scene, camera, renderer, kamera;
let karakterUtama, modelKarakter;
let mixerKarakter; 
let clock = new THREE.Clock();
let aksiJalan = null;
let modelMap;
let modelNPC = [];
let tombolDitekan = {};
let skorPancasila = 0;
let boxKarakter = new THREE.Box3();
// let nyawa = 3;
let npcSelesai = [];
let npcAktif = null;
let soalAktif = null;
let gamePaused = false;

// Posisi dan pergerakan karakter
const posisiKarakter = { x: 0, y: 0, z: 0 };
//0,3 0.05
const kecepatanGerak = 0.5;
const kecepatanRotasi = 0.1;

// Kumpulan objek yang bisa ditabrak
const obstacles = [];
const raycaster = new THREE.Raycaster();
const arahGerak = new THREE.Vector3();



// Inisialisasi Three.js
function initThreeJS() {
    // Scene
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x87CEEB); // Langit biru
    scene.fog = new THREE.Fog(0x87CEEB, 50, 200);
    
    // Camera
    camera = new THREE.PerspectiveCamera(
        75,
        window.innerWidth / window.innerHeight,
        0.1,
        1000
    );
    
    // Renderer
    const container = document.getElementById('canvasContainer');
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    container.appendChild(renderer.domElement);
    
    // Lighting
    const cahayaAmbien = new THREE.AmbientLight(0xffffff, 0.6);
    scene.add(cahayaAmbien);
    
    const cahayaMatahari = new THREE.DirectionalLight(0xffffff, 0.8);
    cahayaMatahari.position.set(50, 100, 50);
    cahayaMatahari.castShadow = true;
    cahayaMatahari.shadow.mapSize.width = 2048;
    cahayaMatahari.shadow.mapSize.height = 2048;
    cahayaMatahari.shadow.camera.far = 200;
    cahayaMatahari.shadow.camera.left = -100;
    cahayaMatahari.shadow.camera.right = 100;
    cahayaMatahari.shadow.camera.top = 100;
    cahayaMatahari.shadow.camera.bottom = -100;
    scene.add(cahayaMatahari);
    
   
// --- Load Map (low_poly_city.glb) ---
let mapLoaded = false;

const loaderGLTF = new THREE.GLTFLoader();
loaderGLTF.load(
    '{{ asset("assets/low_poly_city.glb") }}',
    function (gltf) {
        modelMap = gltf.scene;
        modelMap.scale.set(2, 2, 2);
        modelMap.position.set(0, 0, 0);

        window.obstacles = [];

        modelMap.traverse(function (node) {
            const nama = node.name?.toLowerCase() || "";

            const isObstacle =
                nama.includes("building") ||
                nama.includes("house") ||
                nama.includes("wall") ||
                nama.includes("muro") ||
                nama.includes("albero") ||
                nama.includes("lampione") ||
                nama.includes("panchina") ||
                nama.includes("palazzo") ||
                nama.includes("macchina");

            if (node.isMesh && isObstacle) {
                node.castShadow = true;
                node.receiveShadow = true;

                // Hitung bounding box di world space
                const box = new THREE.Box3().setFromObject(node);
                window.obstacles.push({ node, box });
            }
        });

        scene.add(modelMap);
        mapLoaded = true; // ✅ tandai map sudah siap

        console.log("✅ Map loaded successfully");
        console.log("🧱 Total obstacles:", window.obstacles.length);
        modelMap.traverse((n) => console.log("🟩 node:", n.name));
    },
    undefined,
    function (error) {
        console.error("Error loading map:", error);

        const groundGeo = new THREE.PlaneGeometry(200, 200);
        const groundMat = new THREE.MeshStandardMaterial({ color: 0x90ee90 });
        const ground = new THREE.Mesh(groundGeo, groundMat);
        ground.rotation.x = -Math.PI / 2;
        ground.receiveShadow = true;
        scene.add(ground);
        mapLoaded = true; // tetap tandai siap agar game tidak freeze
    }
);

     


    // Load Karakter Utama (mainCaracter.glb)
    loaderGLTF.load(
        '{{ asset("assets/mainCaracter.glb") }}',
        function(gltf) {
            modelKarakter = gltf.scene;
            modelKarakter.scale.set(1, 1, 1);
            modelKarakter.position.set(11, 0, 11);
            
            modelKarakter.traverse(function(node) {
                if (node.isMesh) {
                    node.castShadow = true;
                }
            });
            
            scene.add(modelKarakter);
            karakterUtama = modelKarakter;
            // 🔹 Tampilkan semua animasi yang tersedia di model
console.log(gltf.animations.map(a => a.name));

// 🔹 Setup mixer animasi
mixerKarakter = new THREE.AnimationMixer(modelKarakter);

// 🔹 Pilih animasi jalan (ganti 'Walk' sesuai nama animasi hasil console.log di atas)
aksiJalan = mixerKarakter.clipAction(gltf.animations.find(a => a.name === 'Running'));
        console.log('Karakter utama loaded dengan animasi');
    },
        undefined,
        function(error) {
            console.error('Error loading character:', error);
            // Fallback: buat karakter sederhana
            const karakterGeo = new THREE.CylinderGeometry(0.5, 0.5, 2, 8);
            const karakterMat = new THREE.MeshStandardMaterial({ color: 0x0066FF });
            karakterUtama = new THREE.Mesh(karakterGeo, karakterMat);
            karakterUtama.castShadow = true;
            scene.add(karakterUtama);
        }
    );
    
// Load semua NPC
dataNPC.forEach((npc, index) => {
    const pathModel = '{{ asset("assets") }}/' + npc.model;

    loaderGLTF.load(
        pathModel,
        function (gltf) {
            const modelNPCItem = gltf.scene;

            // 🔹 Atur skala berdasarkan model NPC
            if (npc.model.toLowerCase().includes('business') || npc.model.toLowerCase().includes('man')) {
                modelNPCItem.scale.set(0.2, 0.2, 0.2); // perkecil businessman
            } 
            else if (npc.model.toLowerCase().includes('elf')) {
                modelNPCItem.scale.set(0.1, 0.1, 0.1); // perkecil elf girl
            } 
            else {
                modelNPCItem.scale.set(2, 2, 2); // default (NPC lain)
            }

            modelNPCItem.position.set(npc.posisi.x, npc.posisi.y, npc.posisi.z);
            modelNPCItem.rotation.y = (npc.rotasi * Math.PI) / 180;

            modelNPCItem.traverse(function (node) {
                if (node.isMesh) {
                    node.castShadow = true;
                }
            });
                
                // Tambahkan marker di atas NPC
                const markerGeo = new THREE.SphereGeometry(0.3, 16, 16);
                const markerMat = new THREE.MeshBasicMaterial({ 
                    color: npc.warna,
                    transparent: true,
                    opacity: 0.8
                });
                const marker = new THREE.Mesh(markerGeo, markerMat);
                marker.position.y = 3;
                modelNPCItem.add(marker);
                
                // Animasi marker naik-turun
                marker.userData.animasi = true;
                marker.userData.offsetY = 0;
                
                modelNPCItem.userData.npcId = npc.id;
                modelNPCItem.userData.marker = marker;
                modelNPC.push(modelNPCItem);
                scene.add(modelNPCItem);
                
                console.log('NPC loaded:', npc.nama);
            },
            undefined,
            function(error) {
                console.error('Error loading NPC:', error);
                // Fallback: buat NPC sederhana
                const npcGeo = new THREE.CylinderGeometry(0.5, 0.5, 2, 8);
                const npcMat = new THREE.MeshStandardMaterial({ color: npc.warna });
                const npcMesh = new THREE.Mesh(npcGeo, npcMat);
                npcMesh.position.set(npc.posisi.x, npc.posisi.y + 1, npc.posisi.z);
                npcMesh.castShadow = true;
                npcMesh.userData.npcId = npc.id;
                modelNPC.push(npcMesh);
                scene.add(npcMesh);
            }
        );
    });
    
    // Setup kamera mengikuti karakter (third person)
    camera.position.set(0, 10, 15);
    camera.lookAt(0, 0, 0);
    
    // Event listeners
    window.addEventListener('resize', onWindowResize);
    document.addEventListener('keydown', onKeyDown);
    document.addEventListener('keyup', onKeyUp);
    
    // Mulai animasi
    animate();
}

// Handle resize
function onWindowResize() {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
}

// Handle keyboard input
function onKeyDown(event) {
    if (gamePaused) return;
    tombolDitekan[event.key.toLowerCase()] = true;
}

function onKeyUp(event) {
    tombolDitekan[event.key.toLowerCase()] = false;
}


// 🔹 Update posisi karakter dengan deteksi tabrakan
function updateKarakter() {
    if (!karakterUtama || gamePaused || !window.obstacles) return; // Pastikan map sudah dimuat
    if (typeof mapLoaded !== "undefined" && !mapLoaded) return;

    // Simpan posisi lama untuk rollback jika tabrakan
    const posisiSebelumnya = karakterUtama.position.clone();
    let bergerak = false;
    const arahDepan = new THREE.Vector3();
    const arahKanan = new THREE.Vector3();
    const arahGerak = new THREE.Vector3();

    // Hitung arah berdasarkan rotasi karakter
    arahDepan.set(
        Math.sin(karakterUtama.rotation.y),
        0,
        Math.cos(karakterUtama.rotation.y)
    );
    arahKanan.set(
        Math.sin(karakterUtama.rotation.y + Math.PI / 2),
        0,
        Math.cos(karakterUtama.rotation.y + Math.PI / 2)
    );

    // 🔸 Gerak maju
    if (tombolDitekan["w"] || tombolDitekan["arrowup"]) {
        arahGerak.copy(arahDepan).normalize();
        karakterUtama.position.add(arahGerak.multiplyScalar(kecepatanGerak));
        bergerak = true;
    }

    // 🔸 Gerak mundur
    if (tombolDitekan["s"] || tombolDitekan["arrowdown"]) {
        arahGerak.copy(arahDepan).normalize().negate();
        karakterUtama.position.add(arahGerak.multiplyScalar(kecepatanGerak));
        bergerak = true;
    }

    // 🔸 Rotasi kiri/kanan
    if (tombolDitekan["a"] || tombolDitekan["arrowleft"]) {
        karakterUtama.rotation.y += kecepatanRotasi;
    }
    if (tombolDitekan["d"] || tombolDitekan["arrowright"]) {
        karakterUtama.rotation.y -= kecepatanRotasi;
    }

    // --- 🔥 Deteksi tabrakan yang disesuaikan dengan struktur obstacles baru ---
    if (window.obstacles.length > 0) {
        const karakterBox = new THREE.Box3().setFromObject(karakterUtama);
        for (const obs of window.obstacles) {
            // Pastikan obs dan obs.box valid
            if (!obs || !obs.box) continue;

            // Perbarui posisi bounding box obstacle ke world space
            obs.box.setFromObject(obs.node);

            if (karakterBox.intersectsBox(obs.box)) {
                // 🚫 Kembalikan posisi karakter sebelum tabrakan
                karakterUtama.position.copy(posisiSebelumnya);
                bergerak = false;
                break;
            }
        }
    }

    // 🔸 Batasi area pergerakan (biar gak keluar map)
    karakterUtama.position.x = Math.max(-15, Math.min(105, karakterUtama.position.x));
    karakterUtama.position.z = Math.max(-83, Math.min(80, karakterUtama.position.z));

    // 🔸 Update posisi kamera (third person)
    const jarakKamera = 6;
    const tinggiKamera = 3;
    const posisiKameraX =
        karakterUtama.position.x - Math.sin(karakterUtama.rotation.y) * jarakKamera;
    const posisiKameraZ =
        karakterUtama.position.z - Math.cos(karakterUtama.rotation.y) * jarakKamera;

    camera.position.x = posisiKameraX;
    camera.position.y = karakterUtama.position.y + tinggiKamera;
    camera.position.z = posisiKameraZ;
    camera.lookAt(karakterUtama.position);

    // 🔸 Kontrol animasi jalan
    if (mixerKarakter && aksiJalan) {
        if (bergerak) {
            if (!aksiJalan.isRunning()) aksiJalan.play();
        } else {
            aksiJalan.stop();
        }
    }
}




    





// Cek jarak ke NPC
function cekJarakKeNPC() {
    if (gamePaused || soalAktif) return;
    
    const jarakMaksimal = 5;
    let npcTerdekat = null;
    let jarakTerdekat = Infinity;
    
    modelNPC.forEach(npcModel => {
        const npcId = npcModel.userData.npcId;
        
        // Skip jika NPC sudah selesai
        if (npcSelesai.includes(npcId)) {
            // Buat NPC lebih transparan
            npcModel.traverse(node => {
                if (node.isMesh) {
                    node.material.transparent = true;
                    node.material.opacity = 0.3;
                }
            });
            return;
        }
        
        const jarak = karakterUtama.position.distanceTo(npcModel.position);
        
        if (jarak < jarakMaksimal && jarak < jarakTerdekat) {
            jarakTerdekat = jarak;
            npcTerdekat = npcId;
        }
    });
    
    if (npcTerdekat && npcTerdekat !== npcAktif) {
        tampilkanDialog(npcTerdekat);
    } else if (!npcTerdekat && npcAktif) {
        sembunyikanDialog();
    }
}

// Tampilkan dialog NPC
function tampilkanDialog(npcId) {
    npcAktif = npcId;
    const npc = dataNPC.find(n => n.id === npcId);
    
    const dialogDiv = document.getElementById('dialogNPC');
    const avatarDiv = document.getElementById('dialogAvatar');
    const namaDiv = document.getElementById('dialogNama');
    const teksDiv = document.getElementById('dialogTeks');
    
    avatarDiv.style.backgroundColor = npc.warna;
    avatarDiv.textContent = npc.nama.charAt(0);
    namaDiv.textContent = npc.nama;
    teksDiv.textContent = npc.dialogAwal;
    
    dialogDiv.classList.remove('hidden');
}

// Sembunyikan dialog NPC
function sembunyikanDialog() {
    npcAktif = null;
    document.getElementById('dialogNPC').classList.add('hidden');
}

// Mulai soal
function mulaiSoal() {
    if (!npcAktif) return;
    
    const npc = dataNPC.find(n => n.id === npcAktif);
    soalAktif = npc;
    
    sembunyikanDialog();
    
    // Isi modal soal
    document.getElementById('soalAvatar').style.backgroundColor = npc.warna;
    document.getElementById('soalAvatar').textContent = npc.nama.charAt(0);
    document.getElementById('soalNama').textContent = npc.nama;
    document.getElementById('soalSila').textContent = `Sila ke-${npc.sila} Pancasila`;
    document.getElementById('soalPertanyaan').textContent = npc.soal.pertanyaan;
    
    // Buat pilihan jawaban
    const containerPilihan = document.getElementById('containerPilihan');
    containerPilihan.innerHTML = '';
    
    npc.soal.pilihan.forEach((pilihan, index) => {
        const btnPilihan = document.createElement('button');
        btnPilihan.className = 'w-full text-left p-4 rounded-xl font-semibold text-lg transition-all bg-gray-100 hover:bg-gray-200 cursor-pointer';
        btnPilihan.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center font-bold text-gray-700">
                    ${String.fromCharCode(65 + index)}
                </div>
                <span>${pilihan.teks}</span>
            </div>
        `;
        btnPilihan.onclick = () => pilihJawaban(index);
        containerPilihan.appendChild(btnPilihan);
    });
    
    // Tampilkan modal
    document.getElementById('modalSoal').classList.remove('hidden');
}

// Pilih jawaban
function pilihJawaban(indexPilihan) {
    const pilihan = soalAktif.soal.pilihan[indexPilihan];
    
    // Disable semua tombol
    const semuaBtn = document.querySelectorAll('#containerPilihan button');
    semuaBtn.forEach(btn => {
        btn.disabled = true;
        btn.classList.add('cursor-not-allowed', 'opacity-50');
    });
    
    // Highlight pilihan yang dipilih
    semuaBtn[indexPilihan].classList.remove('bg-gray-100');
    semuaBtn[indexPilihan].classList.add('bg-blue-500', 'text-white', 'scale-105');
    
    // Tampilkan hasil setelah delay
    setTimeout(() => {
        tampilkanHasil(pilihan);
    }, 1000);
}

// Tampilkan hasil jawaban
function tampilkanHasil(pilihan) {
    // Update skor dan nyawa
    skorPancasila += pilihan.poin;
    // if (pilihan.poin < 0) {
    //     nyawa = Math.max(0, nyawa - 1);
    // }
    
    updateUI();
    
    // Sembunyikan pilihan
    document.getElementById('containerPilihan').classList.add('hidden');
    
    // Tampilkan hasil
    const hasilDiv = document.getElementById('hasilJawaban');
    const bgColor = pilihan.benar ? 'bg-green-100' : 'bg-red-100';
    const textColor = pilihan.benar ? 'text-green-700' : 'text-red-700';
    const iconColor = pilihan.benar ? 'text-green-600' : 'text-red-600';
    const icon = pilihan.benar ? '✅' : '❌';
    const judul = pilihan.benar ? 'Jawaban Benar!' : 'Jawaban Kurang Tepat';
    
    hasilDiv.className = `rounded-2xl p-6 ${bgColor}`;
    hasilDiv.innerHTML = `
        <div class="flex items-start gap-4 mb-4">
            <div class="text-5xl ${iconColor}">
                ${icon}
            </div>
            <div class="flex-1">
                <h3 class="text-2xl font-bold mb-2 ${textColor}">
                    ${judul}
                </h3>
                <p class="text-lg text-gray-800 mb-3">
                    ${pilihan.pesan}
                </p>
                <div class="flex items-center gap-4 text-lg">
                    <span class="font-bold ${pilihan.poin > 0 ? 'text-green-600' : 'text-red-600'}">
                        ${pilihan.poin > 0 ? '+' : ''}${pilihan.poin} Poin
                    </span>
                </div>
            </div>
        </div>
        <button onclick="lanjutkanGame()" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold text-xl px-6 py-4 rounded-xl mt-4">
            Lanjutkan Petualangan →
        </button>
    `;
    
    hasilDiv.classList.remove('hidden');
}

// Lanjutkan game setelah soal
function lanjutkanGame() {
    // Tandai NPC sebagai selesai
    npcSelesai.push(soalAktif.id);

    updateUI(); 
    
    // Reset
    soalAktif = null;
    document.getElementById('modalSoal').classList.add('hidden');
    document.getElementById('containerPilihan').classList.remove('hidden');
    document.getElementById('hasilJawaban').classList.add('hidden');
    
    // Cek apakah semua NPC sudah selesai
    if (npcSelesai.length === dataNPC.length) {
        setTimeout(() => {
            selesaiGame();
        }, 1000);
    }
}

// Game selesai
function selesaiGame() {
    // Simpan hasil ke server
    fetch('{{ route("game.simpanHasil") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            skor: skorPancasila,
            npc_selesai: npcSelesai.length
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("game.hasil") }}';
        }
    });
}

// Update UI
function updateUI() {
    document.getElementById('skorDisplay').textContent = skorPancasila;
    //document.getElementById('nyawaDisplay').textContent = '❤️'.repeat(nyawa);
    document.getElementById('progressDisplay').textContent = `${npcSelesai.length}/6`;
}

// Pause game
function pauseGame() {
    updateUI(); 
    gamePaused = true;
    document.getElementById('pauseSkor').textContent = skorPancasila;
    document.getElementById('pauseProgress').textContent = `${npcSelesai.length}/6`;
    document.getElementById('modalPause').classList.remove('hidden');
}

// Resume game
function resumeGame() {
    gamePaused = false;
    document.getElementById('modalPause').classList.add('hidden');
}

// Restart game
function restartGame() {
    window.location.reload();
}

// Loop animasi
function animate() {
    requestAnimationFrame(animate);
    
    if (!gamePaused) {
        updateKarakter();
        cekJarakKeNPC();
        
        // Animasi marker NPC
        modelNPC.forEach(npcModel => {
            if (npcModel.userData.marker && npcModel.userData.marker.userData.animasi) {
                npcModel.userData.marker.userData.offsetY += 0.02;
                npcModel.userData.marker.position.y = 3 + Math.sin(npcModel.userData.marker.userData.offsetY) * 0.3;
            }
        });
    }
    // 🔹 Update animasi karakter
if (mixerKarakter) {
    mixerKarakter.update(clock.getDelta());
}

    renderer.render(scene, camera);
}

// Event listeners untuk UI
document.getElementById('btnMulaiSoal').addEventListener('click', mulaiSoal);
document.getElementById('btnPause').addEventListener('click', pauseGame);
document.getElementById('btnResume').addEventListener('click', resumeGame);
document.getElementById('btnRestart').addEventListener('click', restartGame);

// Inisialisasi saat halaman load
window.addEventListener('load', () => {
    initThreeJS();
});

// Klik mouse untuk lihat posisi di map
window.addEventListener("click", (event) => {
    const mouse = new THREE.Vector2(
        (event.clientX / window.innerWidth) * 2 - 1,
        -(event.clientY / window.innerHeight) * 2 + 1
    );

    const raycaster = new THREE.Raycaster();
    raycaster.setFromCamera(mouse, camera);

    const intersects = raycaster.intersectObjects(scene.children, true);
    if (intersects.length > 0) {
        const point = intersects[0].point;
        console.log(
            `🧭 Posisi klik: x=${point.x.toFixed(2)}, y=${point.y.toFixed(2)}, z=${point.z.toFixed(2)}`
        );
    }
});

</script>
@endsection

