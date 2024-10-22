@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.2.0/magnific-popup.css" integrity="sha512-UhvuUthI9VM4N3ZJ5o1lZgj2zNtANzr3zyucuZZDy67BO6Ep5+rJN2PST7kPj+fOI7M/7wVeYaSaaAICmIQ4sQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.2.0/jquery.magnific-popup.js" integrity="sha512-tOyzsVuGuz0il5EcXFi/qA5DI4BNLna4gHbWn+HbQBP0jmRhyqMKup24fzyKnxSX0jBxt2+qStqwwHDIh5TaGA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
html, body {
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
    max-height:1000px;
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
<form id="data-form">
    <p>Dimensions maximales pour une palette (L x R x H) 50cm x 50cm x 50cm</p>
    <div class="container-fluid"> 
        <div class="row justify-content-center"> 
            <div class="col-12"> 
                <h3 class="mb-0 text-center" style="font-size: 1.50rem;color:white;background:#05364d;">Palette(s) sélectionnée(s)</h3>
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
                            <tr>
                                <td><span id="paletteCount"></span></td>
                                <td><span id="dayCount"></span></td>
                                <td><span id="price"></span>€</td>
                            </tr>
                        
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
        <input type="file" id="file" class="form-control" placeholder="Aucun fichier choisit">
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

    import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
    import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
    import { DRACOLoader } from 'three/addons/loaders/DRACOLoader.js';

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
        const dataList = [
        { name: 'shelving', url: '3d/Rack.glb' },
        { name: 'floor', url: '3d/Floor.glb' },
        { name: 'front', url: '3d/Front.glb' },
        { name: 'back', url: '3d/Back.glb' },
        { name: 'left', url: '3d/Left.glb' },
        { name: 'right', url: '3d/Right.glb' },
        { name: 'room', url: '3d/Room.glb' },
    ];
    const dateToServerStr = (d) => d.getDate().toString().padStart(2, '0') + '.' + (d.getMonth() + 1).toString().padStart(2, '0') + '.' + d.getFullYear();
    const dateToStr = (d) => d.getFullYear() + '-' + (d.getMonth() + 1).toString().padStart(2, '0') + '-' + d.getDate().toString().padStart(2, '0');
    const strToDate = (s) => new Date(s);
    const getDataByName = (name) => dataList.filter(data => data.name == name)[0];


    const onAllLoad = () => {
        const shelving = getDataByName('shelving').gltf.scene;
        const floor = getDataByName('floor').gltf.scene;
        const front = getDataByName('front').gltf.scene;
        const back = getDataByName('back').gltf.scene;
        const left = getDataByName('left').gltf.scene;
        const right = getDataByName('right').gltf.scene;
        const room = getDataByName('room').gltf.scene;
        const box = new Box3();
        let backZ, leftX, rightX;
        // back
        box.expandByObject(back); // console.log( box );
        backZ = box.max.z;
        back.userData.position = new Vector3(0, 0, backZ);
        back.userData.normal = new Vector3(0, 0, 1);
        // left
        box.makeEmpty();
        box.expandByObject(left); // console.log( box );
        leftX = box.max.x;
        left.userData.position = new Vector3(leftX, 0, 0);
        left.userData.normal = new Vector3(1, 0, 0);
        // right
        box.makeEmpty();
        box.expandByObject(right); // console.log( box );
        rightX = box.min.x;
        right.userData.position = new Vector3(rightX, 0, 0);
        right.userData.normal = new Vector3(-1, 0, 0);
        // office
        room.userData.position = new Vector3(rightX, 0, 0);
        room.userData.normal = new Vector3(-1, 0, 0);
        //
        walls.push(back, left, right/*, room*/);
        scene.add(floor, front, back, left, right, room);
        // shelving
        shelving.traverse(mesh => {
            if (mesh.material && mesh.material.name == 'Box') {
                if (!boxMaterial) {
                    boxMaterial = mesh.material;
                }
                if (mesh.material != boxMaterial) {
                    mesh.material.dispose();
                    mesh.material = boxMaterial;
                }
            }
        });
        
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
        box.expandByObject(shelving);
        const center = box.getCenter(new Vector3());
        const size = box.getSize(new Vector3());
        //console.log( box, size, center );
        const ox = ((rightX - leftX) - size.x * 8) / 4.2;
        const cx = (leftX + rightX) / 2 - ox / 4.2;
        let serial = 0;
        const getSerial = () => (++serial).toString().padStart(4, '0');
        const processShelving = (object, baseName) => {
            const box1_1 = object.getObjectByName('Box_31');
            const box1_2 = object.getObjectByName('Box_32');
            const box1_3 = object.getObjectByName('Box_33');
            const box2_1 = object.getObjectByName('Box_21');
            const box2_2 = object.getObjectByName('Box_22');
            const box2_3 = object.getObjectByName('Box_23');
            const box3_1 = object.getObjectByName('Box_11');
            const box3_2 = object.getObjectByName('Box_12');
            const box3_3 = object.getObjectByName('Box_13');
            const box4_1 = object.getObjectByName('Box_01');
            const box4_2 = object.getObjectByName('Box_02');
            const box4_3 = object.getObjectByName('Box_03');
            box4_1.name = baseName + getSerial() + 'E0C1';
            box4_2.name = baseName + getSerial() + 'E0C2';
            box4_3.name = baseName + getSerial() + 'E0C3';
            box3_1.name = baseName + getSerial() + 'E1C1';
            box3_2.name = baseName + getSerial() + 'E1C2';
            box3_3.name = baseName + getSerial() + 'E1C3';
            box2_1.name = baseName + getSerial() + 'E2C1';
            box2_2.name = baseName + getSerial() + 'E2C2';
            box2_3.name = baseName + getSerial() + 'E2C3';
            box1_1.name = baseName + getSerial() + 'E3C1';
            box1_2.name = baseName + getSerial() + 'E3C2';
            box1_3.name = baseName + getSerial() + 'E3C3';
            boxes.push(
                box4_1, box4_2, box4_3,
                box3_1, box3_2, box3_3,
                box2_1, box2_2, box2_3,
                box1_1, box1_2, box1_3,
            );
            boxNames.push(
                box4_1.name, box4_2.name, box4_3.name,
                box3_1.name, box3_2.name, box3_3.name,
                box2_1.name, box2_2.name, box2_3.name,
                box1_1.name, box1_2.name, box1_3.name,
            );
        };
        for (let i = 1; i < 17; i++) {
            const c5 = shelving.clone();
            processShelving(c5, 'A');
            c5.position.set(leftX + size.x / 2, 0, backZ + size.z / 2 + (16 - i) * size.z);
            scene.add(c5);
        }
        for (let i = 1; i < 17; i++) {
            const c4 = shelving.clone();
            processShelving(c4, 'B');
            c4.position.set(-(ox + size.x * 2) + size.x / -2 + cx, 0, backZ + size.z / 2 + (16 - i) * size.z);
            scene.add(c4);
        }
        for (let i = 1; i < 17; i++) {
            const c3 = shelving.clone();
            processShelving(c3, 'C');
            c3.position.set(-(ox + size.x * 2) + size.x / 2 + cx, 0, backZ + size.z / 2 + (16 - i) * size.z);
            scene.add(c3);
        }
        for (let i = 1; i < 17; i++) {
            const c2 = shelving.clone();
            processShelving(c2, 'D');
            c2.position.set(size.x / -2 + cx, 0, backZ + size.z / 2 + (16 - i) * size.z);
            scene.add(c2);
        }
        for (let i = 1; i < 17; i++) {
            const c1 = shelving.clone();
            processShelving(c1, 'E');
            c1.position.set(size.x / 2 + cx, 0, backZ + size.z / 2 + (16 - i) * size.z);
            scene.add(c1);
        }
        for (let i = 1; i < 17; i++) {
            const c6 = shelving.clone();
            processShelving(c6, 'F');
            c6.position.set((ox + size.x * 2) + size.x / -2 + cx, 0, backZ + size.z / 2 + (16 - i) * size.z);
            scene.add(c6);
        }
        for (let i = 1; i < 11; i++) {
            const c7 = shelving.clone();
            processShelving(c7, 'G');
            c7.position.set((ox + size.x * 2) + size.x / 2 + cx, 0, backZ + size.z / 2 + (10 - i) * size.z);
            scene.add(c7);
        }
        for (let i = 3; i < 9; i++) {
            const c8 = shelving.clone();
            processShelving(c8, 'H');
            c8.position.set(rightX - size.x / 2, 0, backZ + size.z / 2 + (10 - i) * size.z + (size.z / 3));
            scene.add(c8);
        }
        startDateInput.min =
        endDateInput.min = dateToStr(new Date());
        startDateInput.onchange = (event) => {
            endDateInput.min = startDateInput.value;
            const end = strToDate(endDateInput.value);
            const endMin = strToDate(endDateInput.min);
            if (end < endMin) {
                endDateInput.value = endDateInput.min;
            }
            showFreeBoxes();
        };
        endDateInput.onchange = (event) => showFreeBoxes();
        showFreeBoxes();
    };

	const showFreeBoxes = () => {
    renderer.domElement.removeEventListener('pointerdown', pointerdown);
    renderer.domElement.removeEventListener('pointerup', pointerup);
    renderer.domElement.removeEventListener('pointermove', pointermove);
    window.removeEventListener('blur', blur);

    selectedBoxNames.forEach(name => removeElement(name));
    selectedBoxNames.length = 0;

    startDateInput.disabled = endDateInput.disabled = true;

    freeBoxes.length = 0;

    orderList.style.pointerEvents = '';
    orderList.hidden = true;

    validateButton.onclick = null;

    const start = dateToServerStr(strToDate(startDateInput.value));
    const end = dateToServerStr(strToDate(endDateInput.value));

    fetch('{{ route('api.boxes.free') }}?start=' + start + '&finish=' + end)
        .then(response => {
            if (!response.ok) throw new Error('HTTPError, status = ' + response.status);
            return response.json();
        })
        .then(array => {
            const boxMap = new Map();
            array.forEach(boxData => {
                boxMap.set(boxData.box_id, boxData.price);
            });

            boxes.forEach(box => {
                const boxId = boxNames[boxes.indexOf(box)];
                if (boxMap.has(boxId)) {
                    dataByBoxName[boxId] = { price: boxMap.get(boxId) };
                    box.material = freeBoxMaterial;
                    freeBoxes.push(box);
                } else {
                    box.material = boxMaterial;
                }
            });

            startDateInput.disabled = endDateInput.disabled = false;

            renderer.domElement.addEventListener('pointerdown', pointerdown);
            renderer.domElement.addEventListener('pointerup', pointerup);
            renderer.domElement.addEventListener('pointermove', pointermove);
            window.addEventListener('blur', blur);
        })
        .catch(error => console.error(error));
};

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
    initialPointerPosition = { x: event.clientX, y: event.clientY };

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

    const select = (name) => {
        const freeBox = freeBoxes.find(box => box.name == name);

        if (freeBox && !selectedBoxNames.includes(name)) {
            selectedBoxNames.push(name);
            addElement(name);

            freeBox.material = freeBoxHighlightedMaterial;

            orderList.hidden = false;

        if (validateButton.onclick == null) {
            validateButton.onclick = () => {
                let html = '',
                    price = 0;

        document.querySelector('#data-form').style.opacity = '1';
                        const oneDay = 24 * 60 * 60 * 1000,
        firstDate = new Date(startDateInput.value),
        secondDate = new Date(endDateInput.value),
        diffDays = Math.round(Math.abs((firstDate - secondDate) / oneDay)) + 1;

    for (let i = 0; i < selectedBoxNames.length; i++) {
        const boxPrice = dataByBoxName[selectedBoxNames[i]].price;

        html += `
                    <tr>
                        <td>${selectedBoxNames[i]}</td>
                        <td>${diffDays === 1 ? `${diffDays} jour` : `${diffDays} jours`}</td>
                        <td>${boxPrice}€</td>
                        <td>${diffDays * boxPrice}€</td>
                    </tr>
                `;
        price = price + diffDays * boxPrice;
    }

    const info_html = document.querySelector('#info'),
        price_html = document.querySelector('#price'),
        paletteCount_html = document.querySelector('#paletteCount'),
        dayCount_html = document.querySelector('#dayCount');

    info_html.innerHTML = html;
    price_html.innerHTML = price;
    paletteCount_html.innerHTML = selectedBoxNames.length;
    dayCount_html.innerHTML = diffDays;

                $.magnificPopup.open({
                    items: { src: '#data-form' },
                    type: 'inline',
                    callbacks: {
                        open: function() {
                        $('#data-form').css({
                            'opacity': '1',
                            'max-height': 'unset' 
                        });
                    },
                    close: function() {
                        $('#data-form').css({
                            'opacity': '0', 
                            'max-height': '800px'
                        });
                    }
                }
                });
                };

                const form = document.querySelector('#data-form');

                form.onsubmit = (event) => {
                    event.preventDefault();

                    const formData = new FormData();
                    const category = document.querySelector('#category'),
                    comment = document.querySelector('#comment'),
                        first_name = document.querySelector('#first_name'),
                        last_name = document.querySelector('#last_name'),
                        email = document.querySelector('#email'),
                        phone = document.querySelector('#phone');

                    renderer.domElement.removeEventListener('pointerdown', pointerdown);
                    orderList.style.pointerEvents = 'none';

                    const start = dateToServerStr(strToDate(startDateInput.value)),
                        end = dateToServerStr(strToDate(endDateInput.value)),
                        names = selectedBoxNames.join(',');

                    formData.append('box_id', names);
                    formData.append('start', start);
                    formData.append('finish', end);
                    formData.append('category', category.value);
                    formData.append('comment', comment.value);
                    formData.append('first_name', first_name.value);
                    formData.append('last_name', last_name.value);
                    formData.append('email', email.value);
                    formData.append('phone', phone.value);

                    const options = {
                        method: 'POST',
						headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                	},
                        body: formData,
                    };

                    fetch( "{{ route('api.boxes.data')}}", options).then(response => {
                        if (!response.ok) {
                            throw new Error('HTTPError, status = ' + response.status);
                        }
                        return response.text();
                    })
                        .then(data => $.magnificPopup.close())
                        .catch(error => console.error('Sending failed:', error))
                        .finally(() => showFreeBoxes());
                };
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
                select(name);
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

			const onRollOver = ( name, data ) =>
			{
				// console.log( 'over:', name );

				const freeBox = freeBoxes.find( box => box.name == name );

				if( freeBox && !selectedBoxNames.includes( name ) )
				{
					freeBox.material = freeBoxOverMaterial;
				}

				clearInterval( infoIntervalID );

				infoElement.innerHTML = '<b>' + name + '</b><br />Prix : ' + data.price + ' €';
				infoIntervalID = setTimeout( () =>
				{
					infoElement.style.opacity = 1.0;

				}, 500 );
			};

			const onRollOut = ( name ) =>
			{
				// console.log( 'out:', name );

				const freeBox = freeBoxes.find( box => box.name == name );

				if( freeBox && !selectedBoxNames.includes( name ) )
				{
					freeBox.material = freeBoxMaterial;
				}

				infoElement.style.opacity = 0.0;

				clearInterval( infoIntervalID );
			};



			const init = () =>
			{
				renderer = new WebGLRenderer( { antialias:true } );
				renderer.setClearColor(0x000000, 0);
				renderer.outputColorSpace = SRGBColorSpace;
				renderer.setPixelRatio( window.devicePixelRatio );
				domContainer.appendChild( renderer.domElement );
				camera = new PerspectiveCamera( 45, 1.0, 1, 3000 );

				scene = new Scene();

				scene.add( camera );
				scene.add( new AmbientLight( 0xFFFFFF ) );
				// scene.add( new GridHelper( 1000, 100, 0xCCCCCC, 0xCCCCCC ) );

				light = new DirectionalLight( 0xFFFFFF, 1 );
				light.position.set( 3, 2, 1 );
				
				light2 = new DirectionalLight( 0xFFFFFF, 1 );
				light2.position.set( -3, 2, -1 );

				scene.add( light );
				scene.add( light2 );
				//camera.add( light );

				controls = new OrbitControls( camera, renderer.domElement );

				controls.listenToKeyEvents( window );
				controls.screenSpacePanning = false;
				controls.enableDamping = true;
				controls.dampingFactor = 0.1;
				controls.minDistance =
				controls.maxDistance = 600;
				controls.maxPolarAngle =
				controls.minPolarAngle = Math.PI / 180 * 80;
				controls.update();
				controls.saveState();

				//console.log( controls.getDistance() );

				render();

				controls.maxPolarAngle = Math.PI / 180 * 87;
				controls.minPolarAngle = 0;
				controls.minDistance = 200;
				controls.maxDistance = 1000;
				controls.reset();

				const isAllLoaded = () => dataList.filter( data => data.loaded !== true ).length == 0;

				dataList.forEach( data =>
				{
					loader.load( data.url, ( gltf ) =>
					{
						data.gltf = gltf;
						data.loaded = true;
						
						/*data.gltf.scene.traverse( mesh =>
						{
							if( mesh.material )
							{
								mesh.material.roughness = 0.6;
							}
						} );*/

						if( isAllLoaded() )
						{
							onAllLoad();
						}
					} );
				} );

				//

				const day = new Date();

				day.setDate( day.getDate() + 1 );

				startDateInput.value =
				endDateInput.value = dateToStr( day );

				startDateInput.disabled =
				endDateInput.disabled = true;
			};

			const render = ( timeStamp = null ) =>
			{
				const w = domContainer.offsetWidth;
				const h = domContainer.offsetHeight;

				if( w != width || h != height )
				{
					width = w;
					height = h;

					camera.aspect = w / h;
					camera.updateProjectionMatrix();

					renderer.setSize( w, h );
				}

				controls.update();

				//light.lookAt( new Vector3() );

				walls.forEach( wall =>
				{
					const position = wall.userData.position;
					const normal = wall.userData.normal;

					if( position && normal )
					{
						const direction = camera.position.clone().sub( position );
						const dot = normal.dot( direction );

						wall.visible = ( dot > 0 );
					}
				} );

				renderer.render( scene, camera );

				window.requestAnimationFrame( render );
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
    observer.observe(tableBody, { childList: true });
});


		</script>
@endsection
