@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.2.0/magnific-popup.css" integrity="sha512-UhvuUthI9VM4N3ZJ5o1lZgj2zNtANzr3zyucuZZDy67BO6Ep5+rJN2PST7kPj+fOI7M/7wVeYaSaaAICmIQ4sQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.2.0/jquery.magnific-popup.js" integrity="sha512-tOyzsVuGuz0il5EcXFi/qA5DI4BNLna4gHbWn+HbQBP0jmRhyqMKup24fzyKnxSX0jBxt2+qStqwwHDIh5TaGA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<style>
    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    #view3d {
        right: 0;
        left: 0;
        top: 0;
        background: linear-gradient(#CAEDFF, #318CBA);
        overflow: hidden;
        position: fixed;
        width: 100%;
        height: 100%;
        margin-right: auto;
        margin-left: auto;
        z-index: 1;
        max-height: 1000px;
    }

    #ui {
        display: inline-block;
        vertical-align: middle;
        z-index: 1;
        padding: 10px;
        background-color: white;
        position: absolute;
        width: 10vw;
        top: 65px;
        left: 0;
    }

    #tooltip {
        z-index: 99;
        position: fixed;
        background-color: white;
        padding: 10px;
        pointer-events: none;
        touch-action: none;
        opacity: 0.0;
    }

    #orderList {
        word-wrap: break-word;
        max-width: 300px;
        align-items: center;
        display: flex;
        flex-direction: column;
    }

    .box {
        font-weight: bold;
    }

    #data-form {
        z-index: 3;
        max-width: 600px;
        max-height: 800px;
        margin: auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: #ffffff;
        opacity: 0;
    }

    .mfp-hide {
        display: block !important;
        opacity: 0;
        pointer-events: none;
    }


    .required::after {
        content: ' *';
        color: #337ab7;
    }

    .footer-area {
        position: sticky;
        bottom: 0;
        width: 100%;
        z-index: 2;
        padding: 10px;
        text-align: center;
    }

    .no-select {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    @media (max-width: 768px) {
        #ui {
            width: 30vw;
            font-size: 55%;
            scale: 80%;
            opacity: 80%;
        }

        #data-form {
            max-width: 100%;
        }

        #back-top {
            display: block !important;
        }

    }
</style>
<div class="container mob" style="margin-top:15px;margin-bottom:15px;padding: 0px 40px;">
    <div id="view3d"></div>


    <div id="ui" class="container mt-5 no-select">
        <div class="form-group">
            <label for="startDate">Date Entrée:</label>
            <input id="startDate" type="date" class="form-control no-select">
        </div>
        <div class="form-group">
            <label for="endDate">Date Sortie:</label>
            <input id="endDate" type="date" class="form-control no-select">
        </div>
        <table class="table table-responsive" id="orderList" hidden>
            <tbody id="tableorderList" class="no-select">
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <button id="validateButton" class="btn" style="padding: 10px;background-color: #1d9236dc;font-family: 'Open Sans', sans-serif !important;color:#fff;" onclick="return false;">Valider</button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div id="tooltip"></div>
</div>


<script src="https://cdn.jsdelivr.net/npm/es-module-shims@1.10.0/dist/es-module-shims.min.js" defer></script>
<script type="importmap" defer>
    {
        "imports": {
            "three": "https://cdn.jsdelivr.net/npm/three@0.165.0/build/three.module.js",
            "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.165.0/examples/jsm/"
        }
    }
</script>
<script type="module">
    import {
        WebGLRenderer,
        SRGBColorSpace,
        PerspectiveCamera,
        Scene,
        GridHelper,
        AmbientLight,
        DirectionalLight,
        Mesh,
        Box3,
        Raycaster,
        Vector2,
        Vector3,
    } from 'three';

    import {
        OrbitControls
    } from 'three/addons/controls/OrbitControls.js';
    import {
        GLTFLoader
    } from 'three/addons/loaders/GLTFLoader.js';
    import {
        DRACOLoader
    } from 'three/addons/loaders/DRACOLoader.js';

    let width = 0;
    let height = 0;
    let renderer, camera, scene, light, light2, controls;
    let boxMaterial, freeBoxMaterial, freeBoxOverMaterial, freeBoxHighlightedMaterial, freeBoxTransparentMaterial;
    let clickCanceled;
    let clickStarted;
    let rollOverName;
    let infoIntervalID;

    const orderList = document.querySelector('#orderList');
    const tableorderList = document.querySelector('#tableorderList');
    const validateButton = document.querySelector('#validateButton');

    const mouse = new Vector2();

    const ray = new Vector2();
    const raycaster = new Raycaster();
    const boxes = [];
    const boxNames = [];
    const dataByBoxName = {};
    const selectedBoxNames = [];
    const freeBoxes = [];
    const walls = [];
    const infoElement = document.querySelector('#tooltip');
    const domContainer = document.querySelector('#view3d');
    const startDateInput = document.querySelector('#startDate');
    const endDateInput = document.querySelector('#endDate');

    const loader = new GLTFLoader();
    const dracoLoader = new DRACOLoader();
    dracoLoader.setDecoderPath('https://www.gstatic.com/draco/versioned/decoders/1.5.7/');
    loader.setDRACOLoader(dracoLoader);
    const dataList = [{
            name: 'Back',
            url: '3drl/Back.glb'
        },
        {
            name: 'Bureaux',
            url: '3drl/Bureaux.glb'
        },
        {
            name: 'Floor',
            url: '3drl/Floor.glb'
        },
        {
            name: 'front',
            url: '3drl/Front.glb'
        },
        {
            name: 'left',
            url: '3drl/Left.glb'
        },
        {
            name: 'right',
            url: '3drl/Right.glb'
        },
        {
            name: 'rackA1',
            url: '3drl/rackA1maj.glb'
        },
        {
            name: 'rackA3',
            url: '3drl/rackA3maj.glb'
        },
        {
            name: 'rackA4',
            url: '3drl/Rack A4.glb'
        },
        {
            name: 'rackB2',
            url: '3drl/Rack B2.glb'
        },
        {
            name: 'rackB6',
            url: '3drl/Rack B6.glb'
        },
        {
            name: 'rackB12',
            url: '3drl/Rack B12.glb'
        },
        {
            name: 'rackB13',
            url: '3drl/Rack B13.glb'
        }

    ];
    const racks = [];

    const dateToServerStr = (d) => d.getDate().toString().padStart(2, '0') + '.' + (d.getMonth() + 1).toString().padStart(2, '0') + '.' + d.getFullYear();
    const dateToStr = (d) => d.getFullYear() + '-' + (d.getMonth() + 1).toString().padStart(2, '0') + '-' + d.getDate().toString().padStart(2, '0');
    const strToDate = (s) => new Date(s);
    const getDataByName = (name) => dataList.filter(data => data.name == name)[0];


    const onAllLoad = () => {
        const Bureaux = getDataByName('Bureaux').gltf.scene;
        const floor = getDataByName('Floor').gltf.scene;
        const front = getDataByName('front').gltf.scene;
        // back = getDataByName('back').gltf.scene;
        const left = getDataByName('left').gltf.scene;
        const right = getDataByName('right').gltf.scene;
        const Back = getDataByName('Back').gltf.scene;
        const rackA1 = getDataByName('rackA1').gltf.scene;
        const rackA3 = getDataByName('rackA3').gltf.scene;
        const rackA4 = getDataByName('rackA4').gltf.scene;
        const rackB2 = getDataByName('rackB2').gltf.scene;
        const rackB6 = getDataByName('rackB6').gltf.scene;
        const rackB12 = getDataByName('rackB12').gltf.scene;
        const rackB13 = getDataByName('rackB13').gltf.scene;

        const box = new Box3();
        let backZ, leftX, rightX;
        //walls.push(back, left, right /*, room*/ );
        scene.add(Back, Bureaux, floor, front, left, right, rackA1, rackA3, rackB2, rackB6, rackB12, rackB13);
        //scene.add(Back, Bureaux, floor, front, left, right, rackA1, rackA3);

        const boundingBox = new Box3().setFromObject(rackA1);
        const sizerack1 = new Vector3();
        boundingBox.getSize(sizerack1);

        let serial = 0;

        const getSerial = () => (++serial).toString().padStart(4, '0');
        let rakNumber = 1;

        function cloneAndPositionInDirection(rack, lastRackPosition, offset, direction, basename) {
            const clone = rack.clone();

            // Assurez-vous que chaque partie du clone a son propre matériau
            clone.traverse((child) => {
                if (child.isMesh) {
                    child.material = child.material.clone(); // Crée une copie unique du matériau
                }
            });

            clone.userData.price = 10; // Attribuer le prix dans les données utilisateur

            // Positionner le clone perpendiculairement selon la direction choisie
            switch (direction) {
                case "est":
                    clone.position.x = lastRackPosition.x + offset * sizerack1.x;
                    clone.position.z = lastRackPosition.z; // Même position z
                    break;

                case "ouest":
                    clone.position.x = lastRackPosition.x - offset * sizerack1.x;
                    clone.position.z = lastRackPosition.z; // Même position z
                    break;

                case "nord":
                    clone.position.x = lastRackPosition.x; // Même position x
                    clone.position.z = lastRackPosition.z - offset * sizerack1.z;
                    break;

                case "sud":
                    clone.position.x = lastRackPosition.x; // Même position x
                    clone.position.z = lastRackPosition.z + offset * sizerack1.z;
                    break;

                default:
                    console.error("Direction non valide ! Utilisez 'est', 'ouest', 'nord' ou 'sud'.");
                    return null;
            }

            console.log('eto', clone);
            const box0_1 = clone.getObjectByName('Box_33050');
            const box0_2 = clone.getObjectByName('Box_33051');
            const box0_3 = clone.getObjectByName('Box_33049');
            const box1_1 = clone.getObjectByName('Box_33047');
            const box1_2 = clone.getObjectByName('Box_33046');
            const box1_3 = clone.getObjectByName('Box_33048');
            console.log('box0_1', box0_1);

            if (box0_1) box0_1.name = basename + getSerial() + 'E0C1';
            if (box0_2) box0_2.name = basename + getSerial() + 'E0C2';
            if (box0_3) box0_3.name = basename + getSerial() + 'E0C3';
            if (box1_1) box1_1.name = basename + getSerial() + 'E1C1';
            if (box1_2) box1_2.name = basename + getSerial() + 'E1C2';
            if (box1_3) box1_3.name = basename + getSerial() + 'E1C3';

            // Ajouter le clone à la scène
            scene.add(clone);

            // Ajouter les boîtes individuelles au tableau racks
            racks.push(
                ...(box1_1 ? [box1_1] : []),
                ...(box1_2 ? [box1_2] : []),
                ...(box1_3 ? [box1_3] : []),
                ...(box0_1 ? [box0_1] : []),
                ...(box0_2 ? [box0_2] : []),
                ...(box0_3 ? [box0_3] : [])
            );

            return clone;
        }

        let offset = 1;
        let lastRack = cloneAndPositionInDirection(rackB2, rackB2.position, 3, "ouest", "INIT");

        console.log("lasRack", lastRack);
        for (let i = 0; i < 20; i++) {
            lastRack = cloneAndPositionInDirection(rackB2, lastRack.position, offset, "sud", `FIRST`);
            offset = 1;
        }

        console.log("racks", racks);
        window.addEventListener('contextmenu', onRightClick, false);


        freeBoxMaterial = boxMaterial.clone();
        freeBoxMaterial.visible = false;
        //freeBoxMaterial.transparent = true;
        //freeBoxMaterial.opacity = 0.5;
        freeBoxHighlightedMaterial = boxMaterial.clone();
        freeBoxHighlightedMaterial.color.set(0x99FF00);
        freeBoxOverMaterial = boxMaterial.clone();
        //freeBoxOverMaterial.color.set( 0x33CC00 );
        freeBoxTransparentMaterial = freeBoxMaterial.clone();
        freeBoxTransparentMaterial.transparent = true;
        freeBoxTransparentMaterial.opacity = 0.5;
        //boxMaterial.color.set( 0xFF00FF );
        //boxMaterial.transparent = true;
        //boxMaterial.opacity = 0.5;
        box.makeEmpty();
        //box.expandByObject(shelving);
        const center = box.getCenter(new Vector3());
        const size = box.getSize(new Vector3());
        //console.log( box, size, center );
        const ox = ((rightX - leftX) - size.x * 8) / 4.2;
        const cx = (leftX + rightX) / 2 - ox / 4.2;


    };

    // Fonction pour gérer le clic droit
    function onRightClick(event) {
        event.preventDefault(); // Empêcher le menu contextuel par défaut

        // Calculer la position de la souris
        mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
        mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

        // Effectuer le raycast uniquement sur les racks
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(racks); // Limite l'intersection aux racks

        if (intersects.length > 0) {
            const rack = intersects[0].object;

            // Vérifier et modifier le statut du rack
            if (rack.status === "selected") { // Utilisation de === pour vérifier l'état
                rack.status = "deselected"; // Changement correct du statut
                rack.material.color.set('green'); // Change la couleur en vert
            } else {
                rack.status = "selected"; // Changement correct du statut
                rack.material.color.set('red'); // Change la couleur en rouge
            }

            console.log("clicked", rack, "status:", rack.status);
        }
    }


    let isSelecting = false;
    let selectionStartEvent = null;
    let initialPointerPosition = null;
    const CLICK_THRESHOLD = 5;

    const pointerdown = (event) => {


        if (!event.isPrimary) {
            return;
        }

        if (event.pointerType === 'mouse' && event.button !== 0) {
            return;
        }

        clickCanceled = false;
        clickStarted = true;
        isSelecting = true;
        selectionStartEvent = event;
        initialPointerPosition = {
            x: event.clientX,
            y: event.clientY
        };

        move(event);

        const pointerBox = freeBoxes.find(box => box.name == rollOverName);

        if (pointerBox) {
            rollOverName = pointerBox.name;

            if (!selectedBoxNames.includes(rollOverName))
                pointerBox.material = freeBoxOverMaterial;
        } else {
            console.log('PointerBox not found');
        }
    };

    const pointermove = (event) => {
        if (isSelecting && selectionStartEvent && event.pointerId === selectionStartEvent.pointerId) {
            return;
        }

        clickCanceled = true;

        if (!clickStarted) {
            move(event);
        }
    };

    const pointerup = (event) => {
        if (event != null) {
            if (!event.isPrimary || (event.button !== 0 && event.pointerType === 'mouse')) {
                return;
            }
        }

        move(null);

        if (clickStarted) {
            clickStarted = false;
            isSelecting = false;
            selectionStartEvent = null;

            if (!clickCanceled) {
                const distanceMoved = Math.sqrt(
                    Math.pow(event.clientX - initialPointerPosition.x, 2) +
                    Math.pow(event.clientY - initialPointerPosition.y, 2)
                );

                if (distanceMoved <= CLICK_THRESHOLD) {
                    click(event);
                }
            }
        }
    };



    const deselect = (name, isPointerOver = false) => {
        const freeBox = freeBoxes.find(box => box.name == name);

        if (freeBox) {
            const index = selectedBoxNames.indexOf(name);

            if (index !== -1) {
                selectedBoxNames.splice(index, 1);
                removeElement(name);


                freeBox.material = freeBoxMaterial;


                if (selectedBoxNames.length == 0) {
                    orderList.hidden = true;
                    validateButton.onclick = null;
                }
            }
        }
    };

    const addElement = (name) => {
        let element = document.querySelector(`[data-name="${name}"]`);

        if (element == null) {
            const rowHtml = `
            <tr class="box" data-name="${name}">
                <td>${name}</td>
                <td><button class="btn-danger btn-sm padding-sm"><i class="fas fa-trash" aria-hidden="true"></i></button></td>
            </tr>
        `;
            tableorderList.insertAdjacentHTML('afterbegin', rowHtml);

            element = document.querySelector(`[data-name="${name}"]`);
            element.querySelector('button').onclick = () => deselect(name);
        }
    };

    const removeElement = (name) => {
        const element = document.querySelector(`[data-name="${name}"]`);

        if (element) {
            element.remove();
        }
    };

    const click = (event) => {
        const rect = renderer.domElement.getBoundingClientRect();

        ray.set(((event.clientX - rect.x) / rect.width) * 2 - 1, -((event.clientY - rect.y) / rect.height) * 2 + 1);
        raycaster.setFromCamera(ray, camera);

        const intersection = raycaster.intersectObjects(freeBoxes, false)[0];

        if (intersection) {
            const name = intersection.object.name;
            const freeBox = freeBoxes.find(box => box.name == name);

            if (freeBox) {
                if (selectedBoxNames.includes(name)) {
                    deselect(name, true);
                } else {
                    //select(name);
                }
            }
        }
    };


    const move = (event) => {
        let dispatchRollOut = true;
        let dispatchMove = false;

        if (event instanceof PointerEvent) {
            const rect = renderer.domElement.getBoundingClientRect();

            const x = ((event.clientX - rect.x) / rect.width) * 2 - 1;
            const y = -((event.clientY - rect.y) / rect.height) * 2 + 1;

            ray.set(x, y);
            raycaster.setFromCamera(ray, camera);

            const intersections = raycaster.intersectObjects(freeBoxes, false);

            if (intersections.length > 0) {
                const name = intersections[0].object.name;

                if (name !== rollOverName) {
                    if (boxNames.includes(rollOverName)) {
                        onRollOut(rollOverName);
                    }

                    rollOverName = name;
                    onRollOver(rollOverName, dataByBoxName[rollOverName]);
                }

                dispatchRollOut = false;
            }

            dispatchMove = true;
        }

        if (dispatchRollOut) {
            if (boxNames.includes(rollOverName)) {
                onRollOut(rollOverName);
                rollOverName = null;
            }
        }

        if (dispatchMove) {
            onPointerMove(event);
        }
    };

    const onPointerMove = (event) => {


        const rect = infoElement.getBoundingClientRect();

        infoElement.style.left = Math.min(event.clientX, 1700) + 'px';
        infoElement.style.top = (event.clientY - rect.height) + 'px';

    };

    const onRollOver = (name, data) => {
        // console.log( 'over:', name );

        const freeBox = freeBoxes.find(box => box.name == name);

        if (freeBox && !selectedBoxNames.includes(name)) {
            freeBox.material = freeBoxOverMaterial;
        }

        clearInterval(infoIntervalID);

        infoElement.innerHTML = '<b>' + name + '</b><br />Prix : ' + data.price + ' €';
        infoIntervalID = setTimeout(() => {
            infoElement.style.opacity = 1.0;

        }, 500);
    };

    const onRollOut = (name) => {
        // console.log( 'out:', name );

        const freeBox = freeBoxes.find(box => box.name == name);

        if (freeBox && !selectedBoxNames.includes(name)) {
            freeBox.material = freeBoxMaterial;
        }

        infoElement.style.opacity = 0.0;

        clearInterval(infoIntervalID);
    };



    const init = () => {
        renderer = new WebGLRenderer({
            antialias: true
        });
        renderer.setClearColor(0x000000, 0);
        renderer.outputColorSpace = SRGBColorSpace;
        renderer.setPixelRatio(window.devicePixelRatio);
        domContainer.appendChild(renderer.domElement);
        camera = new PerspectiveCamera(45, 1.0, 1, 3000);
        camera.position.set(150, 50, 0);

        scene = new Scene();

        scene.add(camera);
        scene.add(new AmbientLight(0xFFFFFF));
        // scene.add( new GridHelper( 1000, 100, 0xCCCCCC, 0xCCCCCC ) );

        light = new DirectionalLight(0xFFFFFF, 1);
        light.position.set(3, 2, 1);

        light2 = new DirectionalLight(0xFFFFFF, 1);
        light2.position.set(-3, 2, -1);

        scene.add(light);
        scene.add(light2);
        //camera.add( light );

        controls = new OrbitControls(camera, renderer.domElement);

        controls.listenToKeyEvents(window);
        controls.screenSpacePanning = false;
        controls.enableDamping = true;
        controls.dampingFactor = 0.1;
        controls.maxPolarAngle =
            controls.minPolarAngle = Math.PI / 180 * 80;
        controls.update();
        controls.saveState();

        //console.log( controls.getDistance() );

        render();

        controls.maxPolarAngle = Math.PI / 180 * 87;
        controls.minPolarAngle = 0;
        controls.minDistance = 100;
        controls.maxDistance = 300;
        controls.reset();

        const isAllLoaded = () => dataList.filter(data => data.loaded !== true).length == 0;

        dataList.forEach(data => {
            loader.load(data.url, (gltf) => {
                data.gltf = gltf;
                data.loaded = true;

                /*data.gltf.scene.traverse( mesh =>
                {
                	if( mesh.material )
                	{
                		mesh.material.roughness = 0.6;
                	}
                } );*/

                if (isAllLoaded()) {
                    onAllLoad();
                }
            });
        });

        //

        const day = new Date();

        day.setDate(day.getDate() + 1);

        startDateInput.value =
            endDateInput.value = dateToStr(day);

        startDateInput.disabled =
            endDateInput.disabled = true;
    };

    const render = (timeStamp = null) => {
        const w = domContainer.offsetWidth;
        const h = domContainer.offsetHeight;

        if (w != width || h != height) {
            width = w;
            height = h;

            camera.aspect = w / h;
            camera.updateProjectionMatrix();

            renderer.setSize(w, h);
        }

        controls.update();

        //light.lookAt( new Vector3() );

        walls.forEach(wall => {
            const position = wall.userData.position;
            const normal = wall.userData.normal;

            if (position && normal) {
                const direction = camera.position.clone().sub(position);
                const dot = normal.dot(direction);

                wall.visible = (dot > 0);
            }
        });

        renderer.render(scene, camera);

        window.requestAnimationFrame(render);
    }

    init();

    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.getElementById('tableorderList');
        const validateButton = document.getElementById('validateButton');

        function updateButtonVisibility() {
            if (tableBody.children.length === 0) {
                validateButton.style.display = 'none';
            } else {
                validateButton.style.display = 'block';
            }
        }

        // Initial check
        updateButtonVisibility();


        const observer = new MutationObserver(updateButtonVisibility);
        observer.observe(tableBody, {
            childList: true
        });
    });
</script>
@endsection