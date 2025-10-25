import * as THREE from 'https://esm.sh/three';

document.addEventListener("DOMContentLoaded", function() {
    const world = new Globe(document.getElementById('globeViz'), { 
        animateIn: false,
        backgroundColor: 'rgba(0,0,0,0)' // Fundo transparente para manter o gradiente da página
    })
    .globeImageUrl('//cdn.jsdelivr.net/npm/three-globe/example/img/earth-blue-marble.jpg')
    .bumpImageUrl('//cdn.jsdelivr.net/npm/three-globe/example/img/earth-topology.png');

    // Auto-rotate
    world.controls().autoRotate = true;
    world.controls().autoRotateSpeed = 0.35;

    // Desabilitar zoom para manter o tamanho fixo
    world.controls().enableZoom = false;
    
    // Ajustar a posição da câmera para mostrar mais da parte inferior e centralizar
    world.pointOfView({ lat: 0, lng: 0, altitude: 1.5 });

    // Add clouds sphere
    const CLOUDS_IMG_URL = './clouds.png'; // from https://github.com/turban/webgl-earth
    const CLOUDS_ALT = 0.004;
    const CLOUDS_ROTATION_SPEED = -0.006; // deg/frame

    new THREE.TextureLoader().load(CLOUDS_IMG_URL, cloudsTexture => {
      const clouds = new THREE.Mesh(
        new THREE.SphereGeometry(world.getGlobeRadius() * (1 + CLOUDS_ALT), 75, 75),
        new THREE.MeshPhongMaterial({ map: cloudsTexture, transparent: true, opacity: 0.8 })
      );
      world.scene().add(clouds);

      (function rotateClouds() {
        clouds.rotation.y += CLOUDS_ROTATION_SPEED * Math.PI / 180;
        requestAnimationFrame(rotateClouds);
      })();
    });

    // Ajustar o tamanho do globo quando a janela for redimensionada
    function resizeGlobe() {
        const container = document.getElementById('globeViz');
        if (container) {
            world.width(container.offsetWidth);
            world.height(container.offsetHeight);
        }
    }

    // Redimensionar inicialmente
    setTimeout(resizeGlobe, 100);
    
    // Adicionar listener para redimensionamento
    window.addEventListener('resize', resizeGlobe);
});
