(function() {
    'use strict';
    // 'To actually be able to display anything with Three.js, we need three things:
    // A scene, a camera, and a renderer so we can render the scene with the camera.'
    // - http://threejs.org/docs/#Manual/Introduction/Creating_a_scene

    var scene, camera, renderer;

    // I guess we need this stuff too
    var container, HEIGHT,
        WIDTH, fieldOfView, aspectRatio,
        nearPlane, farPlane, stats,
        geometry, particleCount,
        i, h, color, size,
        materials = [],
        mouseX = 0,
        mouseY = 0,
        windowHalfX, windowHalfY, cameraZ,
        fogHex, fogDensity, parameters = {},
        parameterCount, particles,
        displayStats = false;

    init();
    animate();


    function init() {

        HEIGHT = window.innerHeight;
        WIDTH = window.innerWidth;
        windowHalfX = WIDTH / 2;
        windowHalfY = HEIGHT / 2;

        fieldOfView = 40;
        aspectRatio = WIDTH / HEIGHT;
        nearPlane = 3;
        farPlane = 5000;

        /* 	fieldOfView — Camera frustum vertical field of view.
	aspectRatio — Camera frustum aspect ratio.
	nearPlane — Camera frustum near plane.
	farPlane — Camera frustum far plane.

	- http://threejs.org/docs/#Reference/Cameras/PerspectiveCamera

	In geometry, a frustum (plural: frusta or frustums)
	is the portion of a solid (normally a cone or pyramid)
	that lies between two parallel planes cutting it. - wikipedia.		*/

        cameraZ = farPlane / 3; /*	So, 1000? Yes! move on!	*/
        // fogHex = 0x20e70d; /* As black as your heart.	*/
        // fogHex = 0x4ec3d5;

        fogHex = 0x000000;

        fogDensity = 0.0007; /* So not terribly dense?	*/
        camera = new THREE.PerspectiveCamera(fieldOfView, aspectRatio, nearPlane, farPlane);
        camera.position.z = cameraZ;

        scene = new THREE.Scene();
        scene.fog = new THREE.FogExp2(fogHex, fogDensity);

        container = document.createElement('div');
        document.body.appendChild(container);
        document.body.style.margin = 0;
        document.body.style.overflow = 'hidden';

        geometry = new THREE.Geometry(); /*	NO ONE SAID ANYTHING ABOUT MATH! UGH!	*/

        particleCount = 5000; /* Leagues under the sea */

        /*	Hope you took your motion sickness pills;
	We're about to get loopy.	*/

        for (i = 0; i < particleCount; i++) {

            var vertex = new THREE.Vector3();
            vertex.x = Math.random() * 2000 - 1000;
            vertex.y = Math.random() * 2000 - 1000;
            vertex.z = Math.random() * 2000 - 1000;

            geometry.vertices.push(vertex);
        }


        /*	We can't stop here, this is bat country!	*/

        parameters = [
            [
                [1, 1, 0.5], 5
            ],
            [
                [0.95, 1, 0.5], 4
            ]
        ];
        parameterCount = parameters.length;

        /*	I told you to take those motion sickness pills.
	Clean that vommit up, we're going again!	*/

        for (i = 0; i < parameterCount; i++) {

            color = parameters[i][0];
            size = parameters[i][1];

            materials[i] = new THREE.PointCloudMaterial({
                size: size
            });

            particles = new THREE.PointCloud(geometry, materials[i]);

            particles.rotation.x = Math.random() * 6;
            particles.rotation.y = Math.random() * 6;
            particles.rotation.z = Math.random() * 6;

            scene.add(particles);
        }

        /*	If my calculations are correct, when this baby hits 88 miles per hour...
	you're gonna see some serious shit.	*/

        renderer = new THREE.WebGLRenderer({ antialias: false,alpha:true }); /*	Rendererererers particles.	*/
        renderer.setPixelRatio(window.devicePixelRatio); /*	Probably 1; unless you're fancy.	*/
        renderer.setSize(WIDTH, HEIGHT); /*	Full screen baby Wooooo!	*/
        renderer.setClearColor(0x000000, 0)
        container.appendChild(renderer.domElement); /* Let's add all this crazy junk to the page.	*/

        /*	I don't know about you, but I like to know how bad my
		code is wrecking the performance of a user's machine.
		Let's see some damn stats!	*/

        stats = new Stats();
        stats.domElement.style.position = 'absolute';
        stats.domElement.style.top = '0px';
        stats.domElement.style.right = '0px';
        if(displayStats){
          container.appendChild(stats.domElement);
        }

        /* Event Listeners */

        window.addEventListener('resize', onWindowResize, false);
        document.addEventListener('mousemove', onDocumentMouseMove, false);
        document.addEventListener('touchstart', onDocumentTouchStart, false);
        document.addEventListener('touchmove', onDocumentTouchMove, false);

    }

    function animate() {
        requestAnimationFrame(animate);
        render();
        stats.update();
    }

    //Permet d'osciller les couleurs entre deux valeurs
    function getTFromTLS(x1, x2, t, tc) {
      var a = Math.abs(x1 - x2)/2;
      var xMin = x1 < x2 ? x1 : x2;
      var h = a + xMin
      return a*Math.sin(2*Math.PI/t*tc+1)+h;
    }


    function render() {
        var timeC = Date.now();
        var time =  timeC * 0.00001

        camera.position.x += (mouseX - camera.position.x) * 0.05;
        camera.position.y += (-mouseY - camera.position.y) * 0.05;

        camera.lookAt(scene.position);

        for (i = 0; i < scene.children.length; i++) {

            var object = scene.children[i];

            // if (object instanceof THREE.PointCloud) {
                object.rotation.y = time * (i < 4 ? i + 1 : -(i + 1));
            // }@
        }

        for (i = 0; i < materials.length; i++) {

            color = parameters[i][0];

            var white = 0xffffff;
            h = getTFromTLS(.27, .70, 20000, timeC)

            // materials[i].color.setHSL(h, color[1], color[2]);
            // materials[i].color.setHSL(h, .7, .7);
            materials[i].color.setHSL(1, 1, 1)
        }

        renderer.render(scene, camera);
    }

    function onDocumentMouseMove(e) {
        mouseX = e.clientX - windowHalfX;
        mouseY = e.clientY - windowHalfY;
    }

    /*	Mobile users?  I got your back homey	*/

    function onDocumentTouchStart(e) {

        if (e.touches.length === 1) {

            e.preventDefault();
            mouseX = e.touches[0].pageX - windowHalfX;
            mouseY = e.touches[0].pageY - windowHalfY;
        }
    }

    function onDocumentTouchMove(e) {

        if (e.touches.length === 1) {

            e.preventDefault();
            mouseX = e.touches[0].pageX - windowHalfX;
            mouseY = e.touches[0].pageY - windowHalfY;
        }
    }

    function onWindowResize() {

        windowHalfX = window.innerWidth / 2;
        windowHalfY = window.innerHeight / 2;

        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    }
})();
