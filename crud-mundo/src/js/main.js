import * as THREE from 'https://esm.sh/three';

document.addEventListener("DOMContentLoaded", function() {
    // Criar o globo com fundo transparente e sem atmosfera embutida
    const world = new Globe(document.getElementById('globeViz'), {
        animateIn: false,
        backgroundColor: 'rgba(0,0,0,0)',
        // informar cor/altitude da atmosfera aqui (serão sobrescritas abaixo também)
        atmosphereColor: 'rgba(0,0,0,0)',
        atmosphereAltitude: 0
    })
    .globeImageUrl('//cdn.jsdelivr.net/npm/three-globe/example/img/earth-blue-marble.jpg')
    .bumpImageUrl('//cdn.jsdelivr.net/npm/three-globe/example/img/earth-topology.png');

    // Acessar cena e garantir que não haja background (transparente)
    const scene = world.scene();
    scene.background = null;

        // === ESTRELAS NO FUNDO ===
    const starGeometry = new THREE.BufferGeometry();
    const starCount = 1000;
    const starPositions = [];

    for (let i = 0; i < starCount; i++) {
      const radius = 800; // distância das estrelas (maior que o globo)
      const theta = Math.random() * 2 * Math.PI;
      const phi = Math.acos(2 * Math.random() - 1);
      const x = radius * Math.sin(phi) * Math.cos(theta);
      const y = radius * Math.sin(phi) * Math.sin(theta);
      const z = radius * Math.cos(phi);
      starPositions.push(x, y, z);
    }

    starGeometry.setAttribute('position', new THREE.Float32BufferAttribute(starPositions, 3));

    const starMaterial = new THREE.PointsMaterial({
      color: 0xffffff,
      size: 2.2,
      transparent: true,
      opacity: 0.8
    });

    const stars = new THREE.Points(starGeometry, starMaterial);
    stars.renderOrder = -1; // garante que fiquem atrás de tudo
    scene.add(stars);
        // ============================

    // Remover luzes padrões que o Globe pode ter adicionado
    const oldLights = [];
    scene.traverse(obj => { if (obj.isLight) oldLights.push(obj); });
    oldLights.forEach(light => scene.remove(light));

    // --- Remover atmosfera/halo do Globe (API de opções + defensiva) ---
    try {
        // se a API do Globe suportar setters
        if (typeof world.atmosphereAltitude === 'function') {
            world.atmosphereAltitude(0); // sem altitude = sem camada visível
        } else {
            // fallback: tentar forçar via propriedade
            world.options = { ...(world.options || {}), atmosphereAltitude: 0, atmosphereColor: 'rgba(0,0,0,0)' };
        }
    } catch (e) {
        // não crítico se falhar — continuamos com outras medidas
        console.warn('Não foi possível setar atmosphereAltitude diretamente:', e);
    }

    // Forçar material do globo a não depender de luzes externas:
    // usar emissive para que o mapa fique visível sem iluminação adicional.
    const mat = world.globeMaterial();
    if (mat) {
        // garantir que o material permita transparência e emissive
        mat.transparent = true;
        // tornar o globo visível mesmo sem luzes: usar emissive
        if (!mat.emissive) mat.emissive = new THREE.Color(0xffffff);
        mat.emissiveIntensity = 0.9; // ajuste fino: 0.6-1.0; usa 0.9 para ficar vibrante sem brilho excessivo
        // caso ainda exista algum "glow" por camada extra, reduzir opacidade dessa camada
        if (typeof mat.opacity !== 'undefined') {
            mat.opacity = 1.0;
        }
    } else {
        console.warn('Material do globo não encontrado no momento da inicialização.');
    }

    // Também aplicar o pequeno shader fix (opcional) — reduz possíveis halos residuais
    try {
        world.globeMaterial().onBeforeCompile = (shader) => {
            shader.fragmentShader = shader.fragmentShader.replace(
                'gl_FragColor = vec4( outgoingLight, diffuseColor.a );',
                `
                // Reduz possíveis halos residuais nas bordas do globo
                float edge = smoothstep(0.0, 0.25, vViewPosition.z);
                vec3 adjustedColor = mix(outgoingLight, outgoingLight * 0.7, edge);
                gl_FragColor = vec4(adjustedColor, diffuseColor.a);
                `
            );
        };
    } catch (e) {
        console.warn('Falha ao aplicar onBeforeCompile shader (pode ser versão diferente do Globe):', e);
    }

    // Auto-rotate
    world.controls().autoRotate = true;
    world.controls().autoRotateSpeed = 0.35;

    // Desabilitar zoom (como já estava)
    world.controls().enableZoom = false;

    // Posição da câmera (ajuste se quiser mais perto/mais longe)
    world.pointOfView({ lat: 0, lng: 0, altitude: 1.5 });

    // Adicionar nuvens (opcional) — mantenho caso você queira; remove se preferir
    const CLOUDS_IMG_URL = './clouds.png';
    const CLOUDS_ALT = 0.004;
    const CLOUDS_ROTATION_SPEED = -0.006;

    new THREE.TextureLoader().load(CLOUDS_IMG_URL, cloudsTexture => {
        const clouds = new THREE.Mesh(
            new THREE.SphereGeometry(world.getGlobeRadius() * (1 + CLOUDS_ALT), 75, 75),
            new THREE.MeshPhongMaterial({ map: cloudsTexture, transparent: true, opacity: 0.8 })
        );
        // se não quer camada de luz por trás das nuvens, pode reduzir a intensidade
        clouds.renderOrder = 2;
        scene.add(clouds);

        (function rotateClouds() {
            clouds.rotation.y += CLOUDS_ROTATION_SPEED * Math.PI / 180;
            requestAnimationFrame(rotateClouds);
        })();
    }, undefined, (err) => {
        // se não existir clouds.png ou falhar, silenciosamente continua
        // console.warn('Não carregou clouds.png', err);
    });

    // Redimensionar o globo dinamicamente
    function resizeGlobe() {
        const container = document.getElementById('globeViz');
        if (container && typeof world.width === 'function' && typeof world.height === 'function') {
            world.width(container.offsetWidth);
            world.height(container.offsetHeight);
        } else if (container) {
            const canvas = container.querySelector('canvas');
            if (canvas) {
                canvas.style.width = '100%';
                canvas.style.height = '100%';
            }
        }
    }

    setTimeout(resizeGlobe, 100);
    window.addEventListener('resize', resizeGlobe);

   
});
