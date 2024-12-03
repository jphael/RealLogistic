@extends('layouts.app')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

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

        align-items: center;
        display: flex;
        flex-direction: column;
    }

    .box {
        font-weight: bold;
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
            <div class="input-group">
                <input id="startDate" type="text" class="form-control no-select" placeholder="Sélectionnez une date">
                <span class="input-group-text">
                    <i class="bi bi-calendar"></i> <!-- Icône Bootstrap Icons -->
                </span>
            </div>
        </div>

        <div class="form-group">
            <label for="endDate">Date Sortie:</label>
            <div class="input-group">
                <input id="endDate" type="text" class="form-control no-select" placeholder="Sélectionnez une date">
                <span class="input-group-text">
                    <i class="bi bi-calendar"></i>
                </span>
            </div>
        </div>
        <table class="table table-responsive" id="orderList">
            <tbody id="tableorderList" class="no-select">
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <button id="open-popup" class="btn btn-primary">
                            Valider
                        </button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div id="tooltip"></div>
</div>
<!-- Modal -->
<div id="form-popup" class="modal fade" tabindex="-1" aria-labelledby="form-popup-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="form-popup-label">Formulaire</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Your Form -->
                <form id="data-form">
                    <p>Dimensions maximales pour une palette (L x R x H) 50cm x 50cm x 50cm</p>
                    <div class="container-fluid">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <h3 class="mb-0 text-center" style="font-size: 1.50rem; color: white; background: #05364d;">
                                    Palette(s) sélectionnée(s)
                                </h3>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped text-center">
                                        <thead>
                                            <tr>
                                                <th>Nombre de palettes</th>
                                                <th>Nombre de jours</th>
                                                <th>Prix total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <tbody id="infoHead">

                                        </tbody>
                                        <tr>
                                            <td colspan="3">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped text-center">
                                                        <thead>
                                                            <tr>
                                                                <th>Palette</th>
                                                                <th>Durée</th>
                                                                <th>Prix/Jour</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="info">
                                                            <!-- Rows will be added dynamically by JavaScript -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="category" class="form-label">Catégorie</label>
                                <input type="text" id="category" class="form-control" placeholder="Catégorie">
                            </div>
                            <div class="mb-3">
                                <label for="file" class="form-label">Document</label>
                                <input type="file" id="file" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">Remarque</label>
                                <textarea id="comment" class="form-control" placeholder="Votre remarque" style="height: 100px; resize: none;"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="first_name" class="form-label required">Prénom</label>
                                <input type="text" id="first_name" class="form-control" value="{{ Auth::user()->prenom ?? '' }}" placeholder="Saisissez votre Prénom" required>
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label required">Nom de famille</label>
                                <input type="text" id="last_name" class="form-control" value="{{ Auth::user()->nom ?? '' }}" placeholder="Saisissez votre nom de famille" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label required">E-mail</label>
                                <input type="email" id="email" class="form-control" value="{{ Auth::user()->email ?? '' }}" placeholder="Saisissez votre e-mail" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label required">Numéro de téléphone</label>
                                <input type="tel" id="phone" class="form-control" placeholder="Saisissez votre numéro de téléphone" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Valider</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
        MeshBasicMaterial,
        BoxGeometry
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

    const mouse = new Vector2();

    const ray = new Vector2();
    const raycaster = new Raycaster();
    const boxes = [];
    const boxNames = [];
    const dataByBoxName = {};
    const selectedBoxNames = [];
    const walls = [];
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
            

            if (box0_1) initBoxe(basename,box0_1,getSerial(),'E0C1');
            if (box0_2) initBoxe(basename,box0_2,getSerial(),'E0C2');
            if (box0_3) initBoxe(basename,box0_3,getSerial(),'E0C3');
            if (box1_1) initBoxe(basename,box1_1,getSerial(),'E1C1');
            if (box1_2) initBoxe(basename,box1_2,getSerial(),'E1C2');
            if (box1_3) initBoxe(basename,box1_3,getSerial(),'E1C3');

            console.log('box0_1', box0_1);
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
        let lastRack = cloneAndPositionInDirection(rackB2, rackB2.position, 3, "ouest", "I");

        console.log("lasRack", lastRack);
        for (let i = 0; i < 5; i++) {
            lastRack = cloneAndPositionInDirection(rackB2, lastRack.position, offset, "sud", `A`);
            offset = 1;
        }

        console.log("racks", racks);
        //window.addEventListener('contextmenu', onRightClick, false);
        window.addEventListener('click', onRightClick, false);
        window.addEventListener('mousemove', onMouseMove, false);

        initAllBoxesBystatusApi();

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


    function initBoxe(basename,box,serial,boxPosition)
    {
        box.name = basename + serial + boxPosition;
        box.boxPrice = 5;
    }

    const initAllBoxesBystatusApi = () => {
        fetch('{{ route('getBoxesReserved') }}')
            .then(response => {
                if (!response.ok) throw new Error('HTTPError, status = ' + response.status);
                return response.json();
            })
            .then(boxIds => {
                racks.forEach(rack => {
                    if (boxIds.includes(rack.name)) {
                        // Action si rack.name est trouvé dans boxIds
                        //rack.isSelected = true;
                        rack.isFree = false;
                        rack.apiReserved = true;
                        console.log(`Rack trouvé :`, rack);
                    } else {
                        // Action si rack.name n'est pas trouvé dans boxIds

                        //rack.isSelected = false;
                        rack.isFree = true;
                        rack.apiReserved = false;
                        console.log(`Rack non trouvé, traitement par défaut :`, rack);
                    }
                    initMaterialBoxReserved(rack);
                });

                // Résultat final après traitement
            }).catch(error => console.error(error));

    };


    let isSelecting = false;
    let selectionStartEvent = null;
    let initialPointerPosition = null;
    const CLICK_THRESHOLD = 5;



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

        //startDateInput.disabled =endDateInput.disabled = true;
        startDateInput.min =
            endDateInput.min = dateToStr(new Date());
        startDateInput.onchange = (event) => {
            endDateInput.min = startDateInput.value;
            const end = strToDate(endDateInput.value);
            const endMin = strToDate(endDateInput.min);
            if (end < endMin) {
                endDateInput.value = endDateInput.min;
            }
            initAllBoxesBystatusApi();
        };
        endDateInput.onchange = (event) => initAllBoxesBystatusApi();
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
                //validateButton.style.display = 'none';
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

    let isRightClickActive = false; // Indicateur temporaire pour bloquer onMouseMove
    let currentHoveredRack = null; // Dernier rack survolé

    function onMouseMove(event) {
        if (isRightClickActive) return; // Bloquer les survols pendant un clic droit actif

        // Calculer la position de la souris en coordonnées normalisées
        mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
        mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

        // Effectuer un raycast
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(racks);

        if (intersects.length > 0) {
            const rack = intersects[0].object;

            if (rack.apiReserved) return;
            if (!rack.isFree) return; // Ignorer les racks réservés

            // Si un nouveau rack est survolé
            if (currentHoveredRack !== rack) {
                // Réinitialiser l'apparence du rack précédent
                if (currentHoveredRack) {
                    resetRackAppearance(currentHoveredRack);
                }

                // Mettre à jour le rack survolé
                currentHoveredRack = rack;

                // Afficher le carton pour le rack survolé
                showRackCarton(rack);
            }
        } else {
            // Si aucun rack n'est survolé, réinitialiser le rack précédent
            if (currentHoveredRack) {
                resetRackAppearance(currentHoveredRack);
                currentHoveredRack = null; // Réinitialiser le suivi
            }
        }
    }

    function onRightClick(event) {
        event.preventDefault(); // Empêcher le menu contextuel par défaut

        // Calculer la position de la souris en coordonnées normalisées
        mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
        mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

        // Effectuer un raycast
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(racks);

        if (intersects.length > 0) {
            const rack = intersects[0].object;

            if (rack.apiReserved) return;
            const boxName = rack.name;
            // Vérifiez si l'élément existe déjà dans le tableau
            const index = selectedBoxNames.indexOf(boxName);
            if (index === -1) {
                // Si le nom n'est pas dans le tableau, on l'ajoute
                selectedBoxNames.push(boxName);
            } else {
                // Si le nom est déjà dans le tableau, on le supprime
                selectedBoxNames.splice(index, 1);
            }
            // Basculer le statut de réservation
            rack.isFree = !rack.isFree; // Inverse le statut libre/réservé
            //rack.isSelected = !rack.isSelected; // Mettre à jour le statut visuel

            // Forcer la mise à jour visuelle
            initStatusBox(rack);

            // Bloquer temporairement onMouseMove
            isRightClickActive = true;
            setTimeout(() => (isRightClickActive = false), 200);

            console.log("Clic droit sur le rack :", rack.name, "isFree:", rack.isFree, selectedBoxNames);
        }
    }

    function initStatusBox(rack) {
        // Modifier l'apparence du rack en fonction de son statut
        if (!rack.isFree) {
            rack.material.visible = true;

            rack.material.color.set('green'); // Rouge pour réservé

        } else {
            rack.material.visible = false;
        }
    }

    function initMaterialBoxReserved(rack) {
        // Modifier l'apparence du rack en fonction de son statut reservé
        if (rack.apiReserved) {
            rack.material.visible = true;

            rack.material.color.set('red'); // Rouge pour réservé

        } else {
            rack.material.visible = false;
        }
    }

    function resetRackAppearance(rack) {
        if (rack.isFree) {
            rack.material.visible = false; // Masquer le rack
        }
    }

    function showRackCarton(rack) {
        rack.material.visible = true; // Afficher le carton
        rack.material.color.set('orange'); // Orange pour indiquer la possibilité de poser un carton
    }

    function updateTable() {
        const tbody = $('#info'); // Sélectionne le <tbody> du tableau
        tbody.empty(); // Vide le contenu existant


        const days = calculateDays();
        if (days !== null) {
            console.log(`Nombre de jours : ${days}`);
            // Vous pouvez effectuer d'autres actions ici
        }
        let totalPrice = 0;
        // Parcourt le tableau selectedBoxNames pour générer les lignes
        selectedBoxNames.forEach((name, index) => {
            const foundRack = racks.find(rack => rack.name === name);
            let priceItem = 0;
            if (foundRack) {
                console.log(`rack trouvé`,foundRack);
                priceItem = foundRack.boxPrice;
            }
            totalPrice +=  (days * priceItem);
            const row = `
            <tr>
                <td>${name}</td>
                <td>${days}</td>
                <td>${priceItem}€</td>
                <td>${days * priceItem}€</td>
            </tr>
        `;
            tbody.append(row); // Ajoute chaque ligne au tableau
        });

        const tbodyHead = $('#infoHead'); // Sélectionne le <tbody> du tableau
        tbodyHead.empty(); // Vide le contenu existant

        // Parcourt le tableau selectedBoxNames pour générer les lignes
        const rowHead = `
            <tr>
                <td>${selectedBoxNames.length}</td>
                <td>${days} jour(s)</td>
                <td>${totalPrice}€</td>
            </tr>
        `;
        tbodyHead.append(rowHead);
    }

    $('#open-popup').on('click', function(event) {
        if (selectedBoxNames.length === 0) {
            event.preventDefault(); // Empêche le comportement par défaut du bouton
            alert('Veuillez sélectionner au moins une palette avant de valider.');
        } else {
            // Affiche le popup en ajoutant dynamiquement les attributs Bootstrap
            updateTable();
            const modal = new bootstrap.Modal(document.getElementById('form-popup'));
            modal.show();
        }
    });

    $(document).ready(function() {
        // Active le datepicker
        $('#startDate, #endDate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom',
        });

        // Permet l'ouverture via clic sur l'icône
        $('.input-group-text').on('click', function() {
            $(this).siblings('input').datepicker('show');
        });
    });

    function calculateDays() {
        const startDate = $('#startDate').datepicker('getDate'); // Récupère la date entrée
        const endDate = $('#endDate').datepicker('getDate'); // Récupère la date sortie

        if (!startDate || !endDate) {
            alert('Veuillez sélectionner les deux dates.');
            return;
        }

        // Vérifie que endDate >= startDate
        if (endDate < startDate) {
            alert('La date de sortie doit être supérieure ou égale à la date d\'entrée.');
            return;
        }

        // Calcule la différence en millisecondes
        const timeDifference = endDate - startDate;

        // Convertit en jours (ajouter 1 jour si les dates sont identiques)
        const days = Math.ceil(timeDifference / (1000 * 60 * 60 * 24)) + 1;

        return days;
    }
</script>

@endsection