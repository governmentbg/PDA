{{-- Viewer --}}
<div id="three-wrap"
     data-src="{{$item?->has_web_view_resource?->first()?->web_resource_address}}"
     style=" max-width:960px;
     margin:1rem auto;
     height:520px;
     border-radius:12px;
     overflow:hidden;
     position:relative;
     user-select:none;
     -webkit-user-select:none;
     -ms-user-select:none;
     touch-action:none;
     overscroll-behavior:contain;">

    {{-- Click-to-start overlay ON THE MAIN VIEWER --}}
    <button id="three-play"
            type="button"
            aria-label="{{ __('Click to load 3D viewer') }}"
            title="{{ __('Click to load 3D viewer') }}"
            style="
              position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
              backdrop-filter: blur(2px);
              background: rgba(0,0,0,0.35); color:#fff; cursor:pointer; border:0; z-index:3;
            ">
        <span style="display:inline-flex; align-items:center; gap:.6rem; font-size:1.1rem;">
          <i class="fas fa-play" aria-hidden="true" style="font-size:28px; filter:drop-shadow(0 1px 3px rgba(0,0,0,.6));"></i>
          {{ __('cultural_object.start_3d') }}
        </span>
    </button>
</div>

@push('scripts')
    <script type="module">
        import * as THREE from 'https://esm.sh/three@0.146.0';
        import { OrbitControls } from 'https://esm.sh/three@0.146.0/examples/jsm/controls/OrbitControls.js';
        import { GLTFLoader }  from 'https://esm.sh/three@0.146.0/examples/jsm/loaders/GLTFLoader.js';
        import { DRACOLoader } from 'https://esm.sh/three@0.146.0/examples/jsm/loaders/DRACOLoader.js';
        import { KTX2Loader }  from 'https://esm.sh/three@0.146.0/examples/jsm/loaders/KTX2Loader.js';
        import { RoomEnvironment } from 'https://esm.sh/three@0.146.0/examples/jsm/environments/RoomEnvironment.js';
        import { FBXLoader } from 'https://esm.sh/three@0.146.0/examples/jsm/loaders/FBXLoader.js';
        import { OBJLoader } from 'https://esm.sh/three@0.146.0/examples/jsm/loaders/OBJLoader.js';
        import { MTLLoader } from 'https://esm.sh/three@0.146.0/examples/jsm/loaders/MTLLoader.js';
        import { PLYLoader } from 'https://esm.sh/three@0.146.0/examples/jsm/loaders/PLYLoader.js';
        import { STLLoader } from 'https://esm.sh/three@0.146.0/examples/jsm/loaders/STLLoader.js';

        const wrap = document.getElementById('three-wrap');
        const playBtn = document.getElementById('three-play');

        // Initial pending source
        let pendingSrc = getSrcFromUrlParam() || wrap.dataset.src || null;
        let hasStarted = false;

        // Renderer
        const renderer = new THREE.WebGLRenderer({ antialias: true, preserveDrawingBuffer: true });
        renderer.outputEncoding = THREE.sRGBEncoding;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.05;
        renderer.setPixelRatio(window.devicePixelRatio || 1);
        renderer.setSize(wrap.clientWidth, wrap.clientHeight);

        // Scene / camera / controls
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(50, wrap.clientWidth / wrap.clientHeight, 0.01, 5000);
        camera.position.set(0, 1.2, 3);
        const controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.08;
        controls.enableZoom = true;
        controls.enablePan = true;
        controls.zoomSpeed = 0.15;
        controls.mouseButtons = {
            LEFT: THREE.MOUSE.ROTATE,
            MIDDLE: THREE.MOUSE.DOLLY,
            RIGHT: THREE.MOUSE.PAN
        };

        const el = renderer.domElement;
        wrap.appendChild(renderer.domElement);

        // Smooth wheel zoom
        const clamp = THREE.MathUtils.clamp;
        function distToTarget(){ return camera.position.clone().sub(controls.target).length(); }
        let targetDist = distToTarget();
        controls.minDistance = controls.minDistance ?? 0.2;
        controls.maxDistance = controls.maxDistance ?? 5000;

        el.addEventListener('wheel', (e) => {
            e.preventDefault();
            const delta = e.deltaY * (e.deltaMode === 1 ? 20 : 1);
            const factor = Math.pow(0.985, -delta * (controls.zoomSpeed ?? 0.15));
            targetDist = clamp(targetDist * factor, controls.minDistance, controls.maxDistance);
        }, {passive:false});

        // Lights + environment
        scene.add(new THREE.HemisphereLight(0xffffff, 0x222222, 0.9));
        const dir = new THREE.DirectionalLight(0xffffff, 1);
        dir.position.set(5, 10, 7);
        scene.add(dir);
        const pmrem = new THREE.PMREMGenerator(renderer);
        scene.environment = pmrem.fromScene(new RoomEnvironment(renderer), 0.04).texture;
        renderer.setClearColor(0xc9c9c9, 1);
        scene.background = null;

        let current = null, autoRotate = false;

        // Utils
        function frameObject(obj){
            const box = new THREE.Box3().setFromObject(obj);
            const size = new THREE.Vector3(), center = new THREE.Vector3();
            box.getSize(size); box.getCenter(center);
            obj.position.sub(center);

            const sphere = new THREE.Sphere(); box.getBoundingSphere(sphere);
            const radius = Math.max(sphere.radius, 0.001);
            controls.minDistance = radius * 0.75;
            controls.maxDistance = radius * 50;

            const fit = Math.max(size.x, size.y, size.z) || 1;
            const fitDist = fit / (2 * Math.tan((camera.fov * Math.PI) / 360));
            const dist = fitDist * 1.8;
            camera.position.set(0.8 * dist, 0.6 * dist, dist);
            camera.near = dist / 100;
            camera.far = dist * 100;
            camera.updateProjectionMatrix();
            controls.target.set(0, 0, 0);
            controls.update();
            targetDist = distToTarget();
        }

        function makeGLTFLoader(){
            const gltf = new GLTFLoader();
            const draco = new DRACOLoader().setDecoderPath('https://cdn.jsdelivr.net/npm/three@0.146.0/examples/jsm/libs/draco/');
            const ktx2  = new KTX2Loader().setTranscoderPath('https://cdn.jsdelivr.net/npm/three@0.146.0/examples/jsm/libs/basis/').detectSupport(renderer);

            gltf.setDRACOLoader(draco);
            gltf.setKTX2Loader(ktx2);
            return gltf;
        }

        function chooseLoader(src){
            const ext = src.split('?')[0].split('#')[0].split('.').pop().toLowerCase();
            if (ext === 'glb' || ext === 'gltf') return makeGLTFLoader();
            if (ext === 'fbx') return new FBXLoader();
            if (ext === 'obj') return new OBJLoader();
            if (ext === 'ply') return new PLYLoader();
            if (ext === 'stl') return new STLLoader();
            throw new Error('Unsupported format: ' + ext);
        }

        function addToScene(obj){
            if (current) scene.remove(current);
            current = obj;
            scene.add(obj);
            frameObject(obj);
        }

        function asRenderable(objOrGeom){
            if (objOrGeom && objOrGeom.isObject3D) return objOrGeom;
            if (objOrGeom && objOrGeom.isBufferGeometry){
                if (!objOrGeom.getAttribute('normal')) objOrGeom.computeVertexNormals();
                const mat = new THREE.MeshStandardMaterial({ vertexColors: !!objOrGeom.getAttribute('color'), metalness: 0.0, roughness: 0.9, side: THREE.DoubleSide });
                return new THREE.Mesh(objOrGeom, mat);
            }
            return new THREE.Group();
        }

        function loadModel(src){
            const loader = chooseLoader(src);

            if (loader instanceof OBJLoader){
                const base = src.replace(/\.obj(\?.*)?$/i, '');
                new MTLLoader().load(base + '.mtl',
                    (materials) => {
                        materials.preload();
                        loader.setMaterials(materials);
                        loader.load(src, (obj) => {
                            addToScene(obj);
                            document.dispatchEvent(new CustomEvent('three:loaded', { detail: { src } }));
                        });
                    },
                    undefined,
                    () => loader.load(src, (obj) => {
                        addToScene(obj);
                        document.dispatchEvent(new CustomEvent('three:loaded', { detail: { src } }));
                    })
                );
                return;
            }
            loader.load(src, (res) => {
                const obj = asRenderable(res.scene || res);
                addToScene(obj);
                document.dispatchEvent(new CustomEvent('three:loaded', { detail: { src } }));
            });
        }

        // Find a 3D thumbnail by resource id and return its src
        function getSrcFromRes(resId) {
            if (!resId) return null;
            const el = document.querySelector(`a.thumb-card[data-kind="3d"][data-res="${resId}"]`);
            return el ? el.dataset.src : null;
        }

        // If URL has ?res=, resolve the matching 3D src (or null)
        function getSrcFromUrlParam() {
            try {
                const url = new URL(window.location.href);
                const res = url.searchParams.get('res');
                return getSrcFromRes(res);
            } catch (e) {
                return null;
            }
        }

        // Reset viewer: show Play again and queue the requested src
        document.addEventListener('three:reset', (e) => {
            const nextSrc = e?.detail?.src
                || getSrcFromUrlParam()
                || window.__three_pending_src
                || wrap.dataset.src
                || null;

            if (current) { scene.remove(current); current = null; }
            hasStarted = false;
            pendingSrc = nextSrc;
            playBtn.style.display = '';
        });

        // Play handler: hide overlay and load exactly one model (finalSrc)
        playBtn.addEventListener('click', () => {
            if (hasStarted) return;
            hasStarted = true;
            playBtn.style.display = 'none';
            document.dispatchEvent(new CustomEvent('three:started'));

            const urlSrc = getSrcFromUrlParam();
            const finalSrc = urlSrc || window.__three_pending_src || pendingSrc;
            if (finalSrc) loadModel(finalSrc);
        });

        // Resize + animate
        window.addEventListener('resize', () => {
            renderer.setSize(wrap.clientWidth, wrap.clientHeight);
            camera.aspect = wrap.clientWidth / wrap.clientHeight;
            camera.updateProjectionMatrix();
        });

        const clock = new THREE.Clock();
        (function animate(){
            requestAnimationFrame(animate);

            const cur = distToTarget();
            if (Math.abs(cur - targetDist) > 1e-6) {
                const eased = THREE.MathUtils.lerp(cur, targetDist, 0.15);
                const off = camera.position.clone().sub(controls.target).setLength(eased);
                camera.position.copy(controls.target).add(off);
            }

            controls.update();
            renderer.render(scene, camera);
        })();
    </script>
@endpush
