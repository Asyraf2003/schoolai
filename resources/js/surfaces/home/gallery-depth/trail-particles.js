/*
 * Adapted from Houmahani Kane / Codrops Atmospheric Depth Gallery.
 * MIT license notice: docs/third-party/codrops-depth-gallery-MIT.txt
 */

export class DepthTrailParticles {
    constructor(THREE) {
        this.THREE = THREE;
        this.group = new THREE.Group();
        this.group.renderOrder = 1300;
        this.maxParticles = 18;
        this.spawnPerSecond = 20;
        this.spawnRadius = 0.52;
        this.speedMin = 0.05;
        this.speedMax = 0.22;
        this.lifeMin = 0.25;
        this.lifeMax = 0.6;
        this.sizeMin = 0.007;
        this.sizeMax = 0.02;
        this.dragPerFrame = 0.94;
        this.spawnAccumulator = 0;
        this.nextSpawnIndex = 0;
        this.geometry = new THREE.SphereGeometry(1, 5, 4);
        this.particles = Array.from({ length: this.maxParticles }, () => {
            const material = new THREE.MeshBasicMaterial({
                color: '#f6f9ff',
                transparent: true,
                opacity: 0,
                depthWrite: false,
                depthTest: false,
            });
            const mesh = new THREE.Mesh(this.geometry, material);
            mesh.visible = false;
            this.group.add(mesh);
            return {
                mesh,
                velocity: new THREE.Vector3(),
                lifeRemaining: 0,
                totalLife: 0,
            };
        });
    }

    update(deltaSeconds, headPosition, opacity = 1) {
        const delta = Math.min(Math.max(deltaSeconds || 0, 0), 0.1);
        if (delta > 0) {
            this.spawnAccumulator += delta * this.spawnPerSecond;
            const count = Math.floor(this.spawnAccumulator);
            this.spawnAccumulator -= count;
            for (let index = 0; index < count; index += 1) {
                this.spawn(headPosition);
            }
        }
        const drag = Math.pow(this.dragPerFrame, delta * 60);
        const safeOpacity = this.THREE.MathUtils.clamp(opacity, 0, 1);
        this.particles.forEach((particle) => {
            if (particle.lifeRemaining <= 0) return;
            particle.lifeRemaining -= delta;
            if (particle.lifeRemaining <= 0) {
                particle.mesh.visible = false;
                particle.mesh.material.opacity = 0;
                return;
            }
            particle.velocity.multiplyScalar(drag);
            particle.mesh.position.addScaledVector(particle.velocity, delta);
            particle.mesh.material.opacity =
                (particle.lifeRemaining / particle.totalLife) * safeOpacity * 0.75;
        });
    }

    spawn(headPosition) {
        const THREE = this.THREE;
        const particle = this.particles[this.nextSpawnIndex];
        this.nextSpawnIndex = (this.nextSpawnIndex + 1) % this.particles.length;
        const angle = Math.random() * Math.PI * 2;
        const radius = Math.random() * this.spawnRadius;
        particle.mesh.position.set(
            headPosition.x + Math.cos(angle) * radius,
            headPosition.y + (Math.random() - 0.5) * this.spawnRadius * 0.6,
            headPosition.z + Math.sin(angle) * radius,
        );
        const size = THREE.MathUtils.lerp(this.sizeMin, this.sizeMax, Math.random());
        particle.mesh.scale.setScalar(size);
        particle.mesh.visible = true;
        const speed = THREE.MathUtils.lerp(this.speedMin, this.speedMax, Math.random());
        particle.velocity.set(
            (Math.random() - 0.5) * speed,
            (Math.random() - 0.5) * speed * 0.6,
            (Math.random() - 0.5) * speed,
        );
        particle.totalLife = THREE.MathUtils.lerp(this.lifeMin, this.lifeMax, Math.random());
        particle.lifeRemaining = particle.totalLife;
        particle.mesh.material.opacity = 0.4;
    }

    clear() {
        this.spawnAccumulator = 0;
        this.particles.forEach((particle) => {
            particle.lifeRemaining = 0;
            particle.mesh.visible = false;
            particle.mesh.material.opacity = 0;
        });
    }

    dispose() {
        this.clear();
        this.particles.forEach((particle) => particle.mesh.material.dispose());
        this.geometry.dispose();
        this.group.clear();
    }
}
