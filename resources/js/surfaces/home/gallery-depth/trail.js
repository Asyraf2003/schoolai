/*
 * Adapted from Houmahani Kane / Codrops Atmospheric Depth Gallery.
 * MIT license notice: docs/third-party/codrops-depth-gallery-MIT.txt
 */

export class DepthTrail {
    constructor(THREE) {
        this.THREE = THREE;
        this.group = new THREE.Group();
        this.points = [];
        this.mesh = null;
        this.minDistance = 0.006;
        this.maxPoints = 220;
        this.curveTension = 0.5;
        this.curveSegments = 220;
        this.radialSegments = 8;
        this.radiusHead = 0.012;
        this.radiusTail = 0.003;
        this.pointSmoothing = 0.3;
        this.maxTrimPerFrame = 4;
        this.material = new THREE.MeshStandardMaterial({
            color: new THREE.Color('#f6f9ff'),
            emissive: new THREE.Color('#ffffff'),
            emissiveIntensity: 1.35,
            roughness: 0.2,
            metalness: 0.05,
            transparent: true,
            opacity: 0.51,
            depthWrite: false,
            depthTest: false,
            blending: THREE.NormalBlending,
        });
    }

    get object() {
        return this.group;
    }

    addPoint(position) {
        const last = this.points[this.points.length - 1] || null;
        if (last && position.distanceToSquared(last) < this.minDistance ** 2) return;
        const next = last
            ? last.clone().lerp(position, this.pointSmoothing)
            : position.clone();
        this.points.push(next);
        let trimBudget = this.maxTrimPerFrame;
        while (this.points.length > this.maxPoints && trimBudget > 0) {
            this.points.shift();
            trimBudget -= 1;
        }
        if (this.points.length < 2) return;
        const curve = new this.THREE.CatmullRomCurve3(
            this.points,
            false,
            'centripetal',
            this.curveTension,
        );
        const segments = Math.max(
            24,
            Math.min(this.curveSegments, this.points.length * 4),
        );
        const geometry = this.createTaperedTube(curve, segments);
        if (!this.mesh) {
            this.mesh = new this.THREE.Mesh(geometry, this.material);
            this.mesh.renderOrder = 1200;
            this.group.add(this.mesh);
            return;
        }
        this.mesh.geometry.dispose();
        this.mesh.geometry = geometry;
    }

    createTaperedTube(curve, segments) {
        const THREE = this.THREE;
        const pathPoints = curve.getSpacedPoints(segments);
        const ringPoints = this.radialSegments + 1;
        const vertices = [];
        const indices = [];
        const up = new THREE.Vector3(0, 0, 1);
        const tangent = new THREE.Vector3();
        const normal = new THREE.Vector3();
        const binormal = new THREE.Vector3();
        const offset = new THREE.Vector3();
        const vertex = new THREE.Vector3();

        pathPoints.forEach((point, pathIndex) => {
            const t = pathIndex / Math.max(pathPoints.length - 1, 1);
            const radius = this.radiusHead
                + (this.radiusTail - this.radiusHead) * Math.pow(t, 1.5);
            curve.getTangent(t, tangent).normalize();
            normal.crossVectors(up, tangent).normalize();
            if (normal.lengthSq() === 0) normal.set(1, 0, 0);
            binormal.crossVectors(tangent, normal).normalize();

            for (let ringIndex = 0; ringIndex <= this.radialSegments; ringIndex += 1) {
                const angle = (ringIndex / this.radialSegments) * Math.PI * 2;
                offset
                    .copy(normal)
                    .multiplyScalar(-Math.cos(angle) * radius)
                    .addScaledVector(binormal, Math.sin(angle) * radius);
                vertex.copy(point).add(offset);
                vertices.push(vertex.x, vertex.y, vertex.z);
            }
        });

        for (let pathIndex = 0; pathIndex < pathPoints.length - 1; pathIndex += 1) {
            for (let ringIndex = 0; ringIndex < this.radialSegments; ringIndex += 1) {
                const base = pathIndex * ringPoints + ringIndex;
                indices.push(base, base + ringPoints, base + 1);
                indices.push(base + ringPoints, base + ringPoints + 1, base + 1);
            }
        }

        const geometry = new THREE.BufferGeometry();
        geometry.setAttribute(
            'position',
            new THREE.Float32BufferAttribute(vertices, 3),
        );
        geometry.setIndex(indices);
        geometry.computeVertexNormals();
        return geometry;
    }

    reset() {
        if (this.mesh) {
            this.mesh.geometry.dispose();
            this.group.remove(this.mesh);
            this.mesh = null;
        }
        this.points = [];
    }

    dispose() {
        this.reset();
        this.material.dispose();
    }
}
